<?php

require_once _CORE_.'PHPExcel_1.8.0_doc/Classes/PHPExcel.php';
require_once _CORE_API_.'/lib.php';
//require_once _MODEL_API_.'VisualisationController.php';


class DataFile 
{

	private $pdo;
	//private $pdoData;

	/*public function __construct()
	{
		//$this->pdo = Database::getInstance();
		//$this->pdoData = $this->pdo->getDbConnection('_'.$_SESSION['info']['id_entreprise']);
	}
	*/

	/* Il faut check si l'id est valide */
	private function getInstance($id)
	{
		$this->pdo = Database::getInstance('_'.$id);
		//$this->pdo = $this->pdo->getDbConnection('_'.$id);
	}

	 /**
     * Returns a JSON string object to the browser with all datafile name into db of entreprise
     *
     * @url GET /file/$id
     */
	public function getFileName($id)
	{
		$this->getInstance($id);
		$sql = 'show tables FROM _'.$id;
		$req = $this->pdo->query($sql);
		$data = $req->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($data)){
			return $data;
		}
		return NULL;
	}

	 /**
     * Returns a JSON string object to the browser with all datafile name into db of entreprise
     *
     * @url GET /file/info/$id
     */
	public function getFileInfo($id)
	{
		$this->getInstance($id);
		$sql = "SELECT table_name AS 'nom', round(((data_length + index_length) / 1024 / 1024), 2) AS 'size', CAST(create_time AS DATE) AS 'date'
		FROM information_schema.tables 
		WHERE table_schema =  '_".$id."';";

		$req = $this->pdo->query($sql);
		$data = $req->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($data)){
			return $data;
		}
		return NULL;

	}

	
	public function getTypeAlea($id_ent, $id_file)
	{
		$this->getInstance($id_ent);
		$sql = 'SELECT type_alea, count(*) as nb from `'.$id_file.'` group by type_alea';
		$req = $this->pdo->query($sql);
		$data = $req->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}

	public function getDbSize($id)
	{
		$this->getInstance($id);
		$sql = 'SELECT table_schema "DB Name", Round(Sum(data_length + index_length) / 1024 / 1024, 1) "DB Size in MB" FROM information_schema.tables GROUP BY table_schema';
		$req = $this->pdo->query($sql);
		$data = $req->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}
	
	public function getPiece($id_file){
		$sql = 'SELECT Piece,sum(Nb__Pieces_finies) as nbPi, sum(Heures) as heure from `'.$id_file.'`  group by `Piece`order by heure';
		$req = $this->pdoData->query($sql);
		$data = $req->fetchAll(PDO::FETCH_ASSOC);
		return $data;
	}

	public function getData($id_ent, $id_file)
	{
		$this->getInstance($id_ent);
		$sql = 'SELECT * from `'.$id_file.'`';
		$req = $this->pdo->query($sql);
		$data = $req->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($data)){
			return $data;
		}
		return NULL;
	}

	public function getCollumName($id_ent, $id_file)
	{
		$this->getInstance($id_ent);
		$sql = 'DESCRIBE `'.$id_file.'`;';
		//var_dump($sql);
		$req = $this->pdo->query($sql);
		$data = $req->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($data)){
			return $data;
		}
		return NULL;
	}



	public function getDataRange($id_ent, $id_file, $range1, $range2)
	{
		$this->getInstance($id_ent);
		$sql = 'SELECT * from `'.$id_file.'` LIMIT '.$range1.','.$range2;
		$req = $this->pdo->query($sql);
		$data = $req->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($data)){
			return $data;
		}
		return NULL;
	}

	public function getDataRangeCollumn($id_ent, $id_file, $collumn, $range1, $range2)
	{
		$this->getInstance($id_ent);
		$sql = 'SELECT '.$collumn.' from `'.$id_file.'` LIMIT '.$range1.','.$range2;
		$req = $this->pdo->query($sql);
		$data = $req->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($data)){
			return $data;
		}
		return NULL;
	}

	private function idToName($id)
	{
		;
	}

	public function upload($id_ent)
   	{
		$allowed =  array('gif','png' ,'jpg', 'pdf', 'xls', 'xlsx');
		print_r($_FILES);
		$filename = $_FILES['filedata']['name'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if(!in_array($ext,$allowed) ) {
		    echo 'error file is incorrect';
		    exit();
		}
		$this->getInstance($id_ent); // On se connect à la base de l'entreprise

		$sFileName = $_FILES['filedata']['name'];
		$sFileType = $_FILES['filedata']['type'];
		$sFileSize = $this->bytesToSize1024($_FILES['filedata']['size'], 1);

		$target_dir = _FILES_.$id_ent.'/';

		if(!file_exists($target_dir))
			mkdir($target_dir, 0777);

		$target_dir = $target_dir.basename($_FILES["filedata"]["name"]);
		$uploadOk=1;
		//print_r($_FILES);
		if (move_uploaded_file($_FILES["filedata"]["tmp_name"], $target_dir)) 
		{
		    echo "The file ". basename( $_FILES["filedata"]["name"]). " has been uploaded.";
		} 
		else 
		{
		    echo "Sorry, there was an error uploading your file.";
		    if ($_FILES['filedata']['error'] == UPLOAD_ERR_NO_FILE ) $erreur = "Fichier manquant";
		    if ($_FILES['filedata']['error'] == UPLOAD_ERR_INI_SIZE ) $erreur = "Taille limite php";
		    if ($_FILES['filedata']['error'] == UPLOAD_ERR_FORM_SIZE ) $erreur = "Taille max formulaire";
		    if ($_FILES['filedata']['error'] == UPLOAD_ERR_PARTIAL ) $erreur = "Fichier partiellement transféré";
			echo $erreur;
		}

		if ($_FILES['filedata']['error'] > 0) $erreur = "Erreur lors du transfert";
		echo ("<p>Your file: {$sFileName} has been successfully received.</p> 
			  <p>Type: {$sFileType}</p><p>Size: {$sFileSize}</p>"); 
		
		$file_name = $_FILES["filedata"]["name"];
		// J'explose dans un tableau à chaque fois que je rencontre un point
		$file_array = explode ('.',$file_name);
		// Je récupère l'indice dans le tableau de l'extension "jpg", soit le dernier élément
		$extension = count ($file_array) - 1;

		// Je découpe en enlevant l'extension cad (la taille de "jpg" + la taille du point d'où le -1)
		$New = substr ($file_name,0,strlen($file_name) -strlen ($file_array[$extension])-1);
		
		$New=str_replace(array(" ","(",")","-",".","/"),array("","","","","",""), $New);
		
		$this->insertFile($target_dir,$New);
		echo ("<p>insert has been successfully received.</p>");
	}

	private function insertFile($file,$filename)
	{
		// Chargement du fichier Excel
		try
		{
			$objPHPExcel = PHPExcel_IOFactory::load($file);
			$sheet = $objPHPExcel->getSheet(0);
		 
			/*------------------Loading File into an Array-------------------------------*/
			$cptFirstDim = 0;
			// On boucle sur les lignes
			foreach($sheet->getRowIterator() as $row) 
			{
			//echo '<tr>';
			 $cptSecDim = 0;
			// On boucle sur les cellule de la ligne
				foreach ($row->getCellIterator() as $cell) 
				{
					if($cptFirstDim ==0){
						$array[$cptFirstDim][$cptSecDim] = changeToNoPoint(preg_replace('/\s+/', '_',trim(changeToNoAccent($cell->getValue()))));
					}
					else
					{			
						$value = $cell->getFormattedValue();
						
						if(PHPExcel_Shared_Date::isDateTime($cell)) {
							$value =  (new DateTime(date('d-M-Y',PHPExcel_Shared_Date::ExcelToPHP($cell->getValue()))))->format('d-m-Y');	
						}
						$cellde = gettype($value);
						if(strcmp($cellde, "string") == 0 ){
							$array[$cptFirstDim][$cptSecDim] = '"'.$value.'"';
						}elseif(strcmp($cellde, "NULL")  == 0){
							$array[$cptFirstDim][$cptSecDim] = '"'.'"';
						}else{
							$array[$cptFirstDim][$cptSecDim] = $value;
						}
					}
					$cptSecDim++;
				}
			$cptFirstDim++;
			}

		}catch(Exception $e){
			echo "Erreur lors du chargement du fichier";
		}
				
		//$DataFile = new DataFile();
		$this->createTable($array,$filename);
		$this->insert($array,$filename);	
	}

	public function createTable($array, $name)
	{
		$tableCreate = 'CREATE TABLE IF NOT EXISTS `'. $name .'` (';

		for($i = 0; $i < count($array[1]); $i++){
		if($i > 0){
		 	$tableCreate = $tableCreate.", ";
		}
			$tableCreate = $tableCreate.$array[0][$i];
			$cellType = gettype($array[1][$i]);
			if(strcmp($cellType, "NULL")  == 0 || strcmp($cellType, "string") == 0 ){
				$cellType = "VARCHAR(255)";
			}	
			$tableCreate = $tableCreate." ".$cellType;
	
		}
		$tableCreate = $tableCreate.");";
		$this->pdo->exec($tableCreate);
	}

	public function insert($array, $name)
	{
		$columnName = implode(",", $array[0]);
		for($i = 1; $i < count($array); $i++)
		{
			$values = implode(",", $array[$i]);
			$insertQuery = 'INSERT INTO `'. $name . '` ('.$columnName.') VALUES('.$values.')';
			$this->pdo->exec($insertQuery);
		}
	} 

   private function bytesToSize1024($bytes, $precision = 2) 
   {
	    $unit = array('B','KB','MB');
	    return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision).' '.$unit[$i];
   }

  
	
}

?>
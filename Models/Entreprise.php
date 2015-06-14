<?php

require_once(_MODEL_.'adresse.php');

define('OFFRE_OPDATA','0');
define('OFFRE_BI', '1');
define('OFFRE_PREMUIM', '2');

define('DEFAULT_SIZE_OPDATA','50');
define('DEFAULT_SIZE_BI','100');
define('DEFAULT_SIZE_PREMUIM','200');


class Entreprise {

	private $pdo;
	public function __construct()
	{
        $this->pdo = Database::getInstance();
	}

	public function getInfoEntreprise($idEntreprise)
	{
		$sql = "SELECT * FROM entreprise WHERE id_entreprise ='".$idEntreprise."';";
		$req = $this->pdo->query($sql);
		$data = $req->fetch(PDO::FETCH_ASSOC);
		if(!empty($data)){
			return $data;
		}
		return NULL;
	} 

    public function searchEntreprise($nom)
    {
        $sql = "SELECT id_entreprise FROM entreprise WHERE nom_entreprise ='".$nom."';";
        $req = $this->pdo->query($sql);
        $data = $req->fetch(PDO::FETCH_ASSOC);
        if(!empty($data)){
            return $data;
        }
        return NULL;   
    }

    public function getAdresse($idAdresse)
    {
        $entreprise = new Adresse();
        return $entreprise->getAdresseOfEntreprise($idAdresse);
    }

    public function getTypeOffreByName($type)
    {
        if($type == OFFRE_OPDATA)
        {
            return 'Open Data';
        }
        if($type == OFFRE_BI)
        {
            return 'Offre BI';
        }
        if($type == OFFRE_PREMUIM)
        {
            return 'Offre Premuim';
        }

    }

	public function insert($nom_entreprise, $email_entreprise, $siret_entreprise, $tel_entreprise, $fax_entreprise, $forme_juridique_entreprise, $activite_entreprise, $id_of_inserted_adresse)
	{
		$entreprise_sql_req = "INSERT INTO `entreprise` (
                `id_entreprise`, 
                `nom_entreprise`, 
                `email_entreprise`, 
                `siret_entreprise`, 
                `tel_entreprise`, 
                `fax_entreprise`, 
                `forme_juridique_entreprise`, 
                `activite_entreprise`, 
                `type_offre`,
                `espace_disponible`,
                `nombre_fichier`,
                `id_adresse`)
                VALUES (NULL, 
                ".$this->pdo->quote($nom_entreprise).",  
                ".$this->pdo->quote($email_entreprise).", 
                ".$this->pdo->quote($siret_entreprise).",  
                ".$this->pdo->quote($tel_entreprise).", 
                ".$this->pdo->quote($fax_entreprise).",  
                ".$this->pdo->quote($forme_juridique_entreprise).", 
                ".$this->pdo->quote($activite_entreprise).", 
                ".$this->pdo->quote(OFFRE_BI).",  
                ".$this->pdo->quote($this->convertMoToOctets(DEFAULT_SIZE_BI)).", 
                ".$this->pdo->quote(0).", 
                ".$this->pdo->quote($id_of_inserted_adresse).");";
                        
                //echo $entreprise_sql_req;
                if($this->pdo->exec($entreprise_sql_req))
                {
                   return $this->pdo->lastInsertId();
                }
	}

    private function convertMoToOctets($mo)
    {
        return $mo*1048576;
    }

    public function createDbData($id_of_db)
    {
       $bddData = $this->pdo->createDbData($id_of_db);
       if(!empty($bddData))
       {
          echo 'bdd Data cree';
       }
       //$this->createTableInfo($bddData);
    }

    public function getListeEntreprise()
    {
        $sql = 'SELECT nom_entreprise, id_entreprise FROM entreprise ORDER BY nom_entreprise ASC;';
        $req = $this->pdo->query($sql);
        $data = $req->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($data)){
            return $data;
        }
        return NULL;
    }

    public function getOffre($idEntreprise)
    {
        $sql = 'SELECT type_offre FROM entreprise WHERE id_entreprise ='.$idEntreprise.';';
        $req = $this->pdo->query($sql);
        $data = $req->fetch(PDO::FETCH_ASSOC);
        if(!empty($data)){
            return $data['type_offre'];
        }
        return NULL;
    }

    public function getUse($idEntreprise)
    {
        $sql = "SELECT round(sum(data_length+index_length)/1024/1024,4) AS 'size' FROM information_schema.tables 
                WHERE table_schema = '_".$idEntreprise."'GROUP BY table_schema;";

        $req = $this->pdo->query($sql);
        $data = $req->fetch(PDO::FETCH_ASSOC);
        if(!empty($data)){
            return $data['size'];
        }
        return NULL; 
    }

    public function getSpace($idEntreprise)
    {
        $sql = 'SELECT espace_disponible FROM entreprise WHERE id_entreprise='.$_SESSION['info']['id_entreprise'];
        $req = $this->pdo->query($sql);
        $data = $req->fetch(PDO::FETCH_ASSOC);
        if(!empty($data)){
            return $data['espace_disponible'];
        }
        return NULL; 
    }

    public function getNumberFile($idEntreprise)
    {
        $sql = "SELECT count(table_name) FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = '_".$idEntreprise."'";
        $req = $this->pdo->query($sql);
        $data = $req->fetch(PDO::FETCH_ASSOC);
        if(!empty($data)){
            return $data['count(table_name)'];
        }
        return NULL;
    }

    public function updateSpace($size, $mode)
    {
        $sql = 'SELECT espace_disponible FROM entreprise WHERE id_entreprise='.$_SESSION['info']['id_entreprise'];
        $req = $this->pdo->query($sql);
        $data = $req->fetch(PDO::FETCH_ASSOC);
        $space = 0;
        if(!empty($data)){
            $space = $data['espace_disponible'];
        }

        if($mode == '+')
        {
            $space = $space + $size;
        }
        else if($mode == '-')
        {
            $space = $space - $size;
        }
        else
            exit();

        $sql = 'UPDATE entreprise SET espace_disponible ='.$space.' WHERE id_entreprise ='.$_SESSION['info']['id_entreprise'];
        $this->pdo->exec($sql);
    }

    public function updateNumberFile($mode)
    {
        $sql = 'SELECT nombre_fichier FROM entreprise WHERE id_entreprise='.$_SESSION['info']['id_entreprise'];
        $req = $this->pdo->query($sql);
        $data = $req->fetch(PDO::FETCH_ASSOC);
        //print_r($data);
        $nb_of_file = 0;
        if(!empty($data)){
            $nb_of_file = $data['nombre_fichier'];
        }
        //print_r($mode);
        if($mode == '+')
        {
            $nb_of_file++;
        }
        else if($mode == '-')
        {
            $nb_of_file--;
        }
        else
            exit();
        //echo $nb_of_file;

        $sql = 'UPDATE entreprise SET nombre_fichier ='.$nb_of_file.' WHERE id_entreprise ='.$_SESSION['info']['id_entreprise'];
        //echo $sql;
        $this->pdo->exec($sql);
    }

}

?>
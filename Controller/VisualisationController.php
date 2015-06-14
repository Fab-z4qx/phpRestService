<?php
require_once (_CORE_.'jpgraph/src/jpgraph.php');
require_once (_CORE_.'jpgraph/src/jpgraph_bar.php');
require_once (_CORE_.'jpgraph/src/jpgraph_pie.php');
require_once (_CORE_.'jpgraph/src/jpgraph_pie3d.php');

class VisualisationController 
{

   public function getPlot($id_ent, $id_file)
   {
	   	$dataFile = new DataFile($id_ent);
		$data = $dataFile->getTypeAlea($id_ent,$id_file);
		$name = array();
		$value = array();
		foreach ($data as $dat) {
			if($dat['nb'] != 0){
				array_push($name, $dat['type_alea']);
				array_push($value, $dat['nb']);
			}
		}

		$graph = new PieGraph(800,500,'auto');
		$graph->SetScale("textlin");

		$p1 = new PiePlot3D($value);
		$p1->setLegends($name);
		$p1->SetStartAngle(0);
		$p1->SetSize(200);
		$graph->Add($p1);

		$p1->ShowBorder();
		$p1->SetColor('black');
		$graph->legend->SetLayout(LEGEND_HOR);
		$graph->legend->SetColumns(3);
		$graph->legend->SetFrameWeight(2);
		$graph->title->Set("Type d'evenement");
	    $graph->SetMarginColor("#f5f5f5");
		@unlink("graph.jpg"); 
		$graph->Stroke("graph.jpg");
		//$this->getFileName();
		//return @unlink("graph.jpg"); 
		$this->smarty->assign('graph', '<img src="graph.jpg">' );
   }




}

?>
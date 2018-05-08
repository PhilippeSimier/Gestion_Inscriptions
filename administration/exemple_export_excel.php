<?php
// Inialise la session
   session_start();

   $user=$_SESSION['username'];
     
     require_once('definitions.inc.php');
     // connexion � la base
     @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
     @mysql_select_db(IMS) or die("Echec de selection de la base IMS");

// infos sur la formation demand�e
     $sql = sprintf("SELECT * FROM `formation` where id=%s",$_GET['id']);
     $res = mysql_query("$sql");
     $formation = mysql_fetch_object($res);
     $titre = $formation->formation;
// pr�paration du contenu pour la cellule A1
     $A1 = 'Liste : '.$formation->formation;
     $dt_debut = date_create($formation->dated);
// pr�paration du contenu pour la cellule A2
     $A2 .= " Stage du ".date_format($dt_debut, 'd/m/Y H:i')." au ";
     $dt_fin = date_create($formation->datef);
     $A2 .= date_format($dt_fin, 'd/m/Y  H:i');

     include 'Classes/PHPExcel.php';
     include 'Classes/PHPExcel/Writer/Excel5.php';



     $classeur  = new PHPExcel;

     $feuille = $classeur->getActiveSheet();
     $feuille->setTitle(utf8_encode($titre));

//-------------------------------------------------------------------------------
//   Premi�re ligne de la feuille   Arial taille 16 bold gris  alignement centr�
//   hauteur 75 (100px)
//-------------------------------------------------------------------------------

     $styleA1 = $feuille->getStyle('A1:H1');
     $styleA1->applyFromArray(array(
        'font'=>array(
        'bold'=>true,
        'size'=>16,
        'name'=>Arial,
        'color'=>array(
            'rgb'=>'808080'))
        ));
//---- On fusionne les cellules pour la premi�re ligne -----
     $feuille->mergeCells('B1:E1');
     // hauteur de la premi�re ligne
     $feuille->getRowDimension('1')->setRowHeight(75);


     $feuille->duplicateStyleArray(array(
                   'alignment'=>array(
                            'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER)), 'A1:E1');
                    


     $feuille->setCellValue('B1',utf8_encode($A1));

//-------------------------------------------------------------------------------
//   Deuxi�me ligne de la feuille   Arial taille 12 bold gris
//-------------------------------------------------------------------------------
     $styleA1 = $feuille->getStyle('A2:H2');
     $styleA1->applyFromArray(array(
        'font'=>array(
          'bold'=>true,
          'size'=>12,
          'name'=>Arial,
          'color'=>array(
            'rgb'=>'404000'))
        ));
     // On fusionne les cellules pour la deuxi�me ligne
     $feuille->mergeCells('A2:E2');
     $feuille->getRowDimension('2')->setRowHeight(40);
     $feuille->setCellValue('A2',$A2);

//-------------------------------------------------------------------------
//   Ent�te du tableau    Arial 13 gras blanc
//-------------------------------------------------------------------------
     $feuille->getRowDimension('3')->setRowHeight(60);
     $styleA4 = $feuille->getStyle('A4:H4');
     $styleA4->applyFromArray(array(
        'font'=>array(
        'bold'=>true,
        'size'=>13,
        'name'=>Arial,
        'color'=>array(
            'rgb'=>'FFFFFF'))
        ));


        $feuille->getStyle('A4:E4')->applyFromArray(array(

            'fill'=>array(
                'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                'color'=>array(
                    'rgb'=>'A0A0A0'))));
                




     // Pr�paration des colonnes sur la troisi�me ligne
     $feuille->getColumnDimension('A')->setWidth(17);
     $feuille->setCellValue('A4','Nom');
     $feuille->getColumnDimension('B')->setWidth(15);
     $feuille->setCellValue('B4',utf8_encode('Pr�nom'));
     $feuille->getColumnDimension('C')->setWidth(30);
     $feuille->setCellValue('C4','Etablissement');
     $feuille->getColumnDimension('D')->setWidth(8);
     $feuille->setCellValue('D4','Classe');
     $feuille->getColumnDimension('E')->setWidth(20);
     $feuille->setCellValue('E4',utf8_encode('Pr�sent/absent'));

                    

//-------------------------------------------------------------------------
//   remplissage du tableau
//-------------------------------------------------------------------------
     $sql = sprintf("SELECT * FROM eleves,user where eleves.rne=user.rne AND `formation`='%s'",$_GET['id']);
     $result = mysql_query("$sql");
     $ligne = 5;
     while ($eleve = mysql_fetch_object($result)) {

        $feuille->setCellValueByColumnAndRow(0, $ligne, utf8_encode($eleve->nom ));
        $feuille->setCellValueByColumnAndRow(1, $ligne, utf8_encode($eleve->prenom ));
        $feuille->setCellValueByColumnAndRow(2, $ligne, utf8_encode($eleve->etablissement ));
        $feuille->setCellValueByColumnAndRow(3, $ligne, utf8_encode($eleve->classe ));
        
        $ligne +=1;
      }
//-------------------------------------------------------------------------
//   cr�ation des bordures
//-------------------------------------------------------------------------
     $ligne -=1;
     $zone = "A4:E".$ligne;

     $feuille->getStyle($zone)->getBorders()->applyFromArray(
    		array(
    			'allborders' => array(
    				'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
    				'color' => array(
    					'rgb' => 'A0A0A0'
    				)
    			)
    		)
    );
//--------------------------------------------------------------------------
//    Affichage du logo du lyc�e en haut � droite
//--------------------------------------------------------------------------
      $objDrawing = new PHPExcel_Worksheet_Drawing();
      $objDrawing->setName('PHPExcel logo');
      $objDrawing->setDescription('PHPExcel logo');
      $objDrawing->setPath('./images/logo.gif');
      $objDrawing->setHeight(100);
      $objDrawing->setCoordinates('A1');
      $objDrawing->setOffsetX(+10);
      $objDrawing->setWorksheet($feuille);


//--------------------------------------------------------------------------
//   Envoie du classeur
//   vers le client
//--------------------------------------------------------------------------

     $writer = new PHPExcel_Writer_Excel5($classeur);

     header('Content-type: application/vnd.ms-excel');
     // header pour d�finir le nom du fichier
     $entete="Content-Disposition:inline;filename=liste ".$titre.".xls ";
     header($entete);

     $writer->save('php://output');


     
?>


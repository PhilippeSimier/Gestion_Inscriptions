<?php
//---------------------------------------------------------------------------------------
//  ce script exporte les informations relatives � un evenement
//  dans un fichier au format vCard
//---------------------------------------------------------------------------------------
// v�rification des variables de session pour le temps d'inactivit� et de l'adresse IP
	include "authentification/authcheck.php" ;
	// V�rification des droits pour cette page tous sauf les exclus
	if ($_SESSION['droits']<'2'){ 
		header("Location: index.php");
	};

	require_once('../definitions.inc.php');
	require_once('utile_sql.php');
	// Classe vcard
	include "inc/class_vcalendar.php" ;

	// Connexion � la base pour rechercher les infos de l'utilisateur
    $bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE); 

	// Lecture configuration  saison
    $sql = "SELECT * FROM `cross_route_configuration`";
    $stmt = $bdd->query($sql);

	while ($conf = $stmt->fetchObject()){
		define($conf->conf_key, $conf->conf_value);
    }
    // fin de la lecture configuration saison

	//  requ�te SQL:
	//  si l'id est absent alors toutes les comp�titions
    if(isset($_GET['id'])) {
        $sql = sprintf('SELECT * FROM `competition` WHERE `id` = %s',
			GetSQLValueString($_GET['id'], "int")
        );
        $stmt = $bdd->query($sql); 
        $competition = $stmt->fetchObject();
        $fichier = $competition->nom;
         

      } else {
        $sql = 'SELECT * FROM `competition`';
        $stmt = $bdd->query($sql);  
        $fichier = "calendrier_".SAISON;
    }
    // Création d'un nouvel objet vCalendar  en version 2.0
    $e = new vCalendar("UTF8",$fichier);
	
	$stmt = $bdd->query($sql);
	
	
	While ($competition = $stmt->fetchObject()){
		// Ecriture d'un évènement vCalendar
		list($date,$time) = explode(" ", $competition->date);
		// les infos sup dans la description
		$info = "Organisateur : ".$competition->organisateur." \\n";
		$info .= "Email : ".$competition->email." \\n";
		$info .= "Licences autorisees : ";
		if($competition->licence=="") $info .= "toutes"; else $info .= $competition->licence;

		$e->addEvenement($competition->nom , $date." 090000" , $date." 170000", $competition->lieu, $info, "Competition Cross Route" ,1);
	}
    $output = $e->getvCalendar();
    $filename = $e->getFileName();


    Header("Content-Disposition: attachment; filename=$filename");
    Header("Content-Length: ".strlen($output));
    Header("Connection: close");
    Header($e->getTypeName()."name=$filename");

    echo $output;
     
?>

<?php
 //-----------------------------------------------------
 // script r�ponse au format JSON � la requete Ajax
 // un seul param�tre noclub (par la m�thode POST)
 //-----------------------------------------------------
	require_once('definitions.inc.php');
	
	if (!isset($_POST['noclub']) {
        echo "{}";
        exit;
    };

    // ouverture connexion � la base 
    
	$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
    $sql = "SELECT * FROM ffa_club WHERE `noclub`=".$_POST['noclub'];
    $stmt = $bdd->query($sql);   
    $club = $stmt->fetchObject();
    $reponse = '{';

    if ($club){
        $reponse .= '"club":"'.trim($club->nom).'"';
        $reponse .= ',"sigle":"'.trim($club->sigle).'"';
        $reponse .= ',"noclub":"'.trim($club->noclub).'"';
    }

    $reponse .='}';
    echo $reponse;

exit;
?>




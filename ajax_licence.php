<?php
 // script r�ponse au format JSON � la requete Ajax
 // un seul param�tre nolicence (par la m�thode POST)
	require_once('definitions.inc.php');
   
	if ($_POST['nolicence']=="") {
        echo "{}";
        exit;
    };

    // ouverture connexion � la base de donn�es
    $bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);
   
    $sql = "SELECT * FROM ffa_licence WHERE `nolicence`='".$_POST['nolicence']."'";
    $stmt = $bdd->query($sql);
    $licencie = $stmt->fetchObject();


    // si la recherche est fructueuse on recherche le nom du club
    if ($licencie) {
		$sql = "SELECT * FROM ffa_club WHERE `noclub`=".$licencie->noclub;
		$stmt = $bdd->query($sql);
		$club = $stmt->fetchObject();

    }

    $reponse = '{';
    if ($licencie){
        $reponse .= '"nom":"'.trim($licencie->nom).'"';
        $reponse .= ',"prenom":"'.trim($licencie->prenom).'"';
        $reponse .= ',"annee":"'.substr($licencie->date,0,4).'"';
        $reponse .= ',"sexe":"'.trim($licencie->sexe).'"';

        if ($club){
            $reponse .= ',"club":"'.trim($club->nom).'"';
            $reponse .= ',"sigle":"'.trim($club->sigle).'"';
            $reponse .= ',"noclub":"'.trim($club->noclub).'"';
        }
    }
    $reponse .='}';
    echo $reponse;

	$pdo = null; 
	exit;
?>




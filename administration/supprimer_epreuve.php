<?php
 //------------------------------------------------------------------------
 // ce script supprime une �preuve dans la table des �preuves.
 // l'�preuve est identifi�e par id_epreuve m�thode GET
 // version 1.0    2009
 // Auteur    SIMIER Philippe
 //------------------------------------------------------------------------
 
// v�rification des variables de session pour le temps d'inactivit� et de l'adresse IP
	include "authentification/authcheck.php" ;
	// V�rification des droits pour cette page uniquement l'organisateur
	if ($_SESSION['droits']<>'2'){ 
		header("Location: index.php");
	};


// connexion � la base
	require_once('../definitions.inc.php');
	$bdd = new PDO('mysql:host=' . SERVEUR . ';dbname=' . BASE, UTILISATEUR,PASSE);

	// r�cup�ration de la variable id_epreuve transmise par GET
	// et cr�ation de la requ�te SQL
	if ((isset($_GET['id_epreuve'])) && ($_GET['id_epreuve'] != "")) {

        // on efface l'engagement
        $sql = "DELETE FROM cross_route_epreuve WHERE id_epreuve=".$_GET['id_epreuve']."";
        $stmt = $bdd->query($sql);
	}
 
	// retour vers la page configuration des epreuve
	$retour = "Location: epreuve.php?competition=".$_GET['id_competition'];
	header($retour);

?>

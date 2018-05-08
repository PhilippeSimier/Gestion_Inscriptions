<?php
//------------------------------------------------------------------------------------
// ce script  exporte  le tableau des engagements au format excel
// // format TSV tab-separated-values
// Auteur: SIMIER Philippe Endurance72  Octobre 2009
//
// Format simplifi� pour transmission au club organisateur
//------------------------------------------------------------------------------------
// v�rification des variables de session pour le temps d'inactivit� et de l'adresse IP
include "authentification/authcheck.php" ;
// V�rification des droits pour cette page tous sauf les exclus
if ($_SESSION['droits']<>'2') { header("Location: index.php");};


require_once('../definitions.inc.php');
require_once('utile_sql.php');

// connexion � la base
     @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
     @mysql_select_db(BASE) or die("Echec de selection de la base cdt");


     // Si la variable ordre n'exite pas on trie suivant nomcourse
     if(!isset($_GET['ordre'])) { $_GET['ordre']="nomcourse"; }

     if (isset($_GET['course'])) {

     $sql = sprintf("SELECT * FROM cross_route_engagement WHERE `nomcourse`=%s ORDER BY %s",
            GetSQLValueString($_GET['course'], "text"),
            GetSQLValueString($_GET['ordre'], "text")
            );
     $nom_fichier="engages_".stripslashes($_GET['course']).".csv";
     }
     else {
     
     $sql = sprintf("SELECT * FROM cross_route_engagement WHERE `competition`=%s ORDER BY %s",
            GetSQLValueString($_GET['competition'], "text"),
            GetSQLValueString($_GET['ordre'], "text")
            );
     $nom_fichier="engages_".stripslashes($_GET['competition']).".csv";
     }

    $resultat = mysql_query($sql);



    header('Content-Type: application/csv-tab-delimited-table');
    header('Content-Disposition:attachment;filename='.$nom_fichier);
    // la  premi�re ligne du fichier Excel
    echo "Dossard;Licence;Nom;Pr�nom;Nom �quipe;Comment;Naiss;Cat�g;Sexe;Course\r\n";

if (mysql_num_rows($resultat) != 0) {

  // donn�es de la table
  while ($engagement = mysql_fetch_object ($resultat))
    {
    	echo $engagement->dossard.";";            // dossard
        echo $engagement->nolicence.";";          // num�ro de licence
        echo $engagement->nom.";";                // nom
        echo $engagement->prenom.";";             // pr�nom
        echo $engagement->nomequipe.";";          // le nom de l'�quipe
        echo $engagement->commentaire.";";        // le nombre de repas champ�tre r�serv�
        echo $engagement->anneenaissance.";";     // l'ann�e de naissance
        echo $engagement->categorie.";";          // la cat�gorie FFA
        echo $engagement->sexe.";";               // le sexe   M ou F
        $num = explode(" ", $engagement->nomcourse);
        echo array_pop($num).";";                  // No de la course ou nom
        echo "\r\n";                              // retour � la ligne

    }
}

// fonction pour convertir un bool�en en chaine VRAI
function v_f($bool)
 { 
 if ($bool==1) return "VRAI"; else return "";
 }

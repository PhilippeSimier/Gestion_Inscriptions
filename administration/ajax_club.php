<?php
 //-----------------------------------------------------
 // script r�ponse au format JSON � la requete Ajax
 // un seul param�tre noclub (par la m�thode POST)
 //-----------------------------------------------------
   require_once('definitions.inc.php');

   if ($_POST['noclub']=="") {
        echo "{}";
        exit;
     };

    // ouverture connexion � la base marsouin
    @mysql_connect(SERVEUR,UTILISATEUR,PASSE) or die("Connexion impossible");
    @mysql_select_db(BASE) or die("Echec de selection de la base cdt");

       $sql = "SELECT * FROM ffa_club WHERE `noclub`=".$_POST['noclub'];
       $res = mysql_query($sql) or die(mysql_error());
       $club = mysql_fetch_object ($res);

      }

        $reponse = '{';

        if ($club){
               $reponse .= ',"club":"'.trim($club->nom).'"';
               $reponse .= ',"sigle":"'.trim($club->sigle).'"';
               $reponse .= ',"noclub":"'.trim($club->noclub).'"';
               }
        }
        $reponse .='}';
        echo $reponse;

@mysql_close();
exit;
?>




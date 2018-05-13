<?php
// cette fonction affiche les lettres de l'alphabet avec un lien pour chaque lettre

function makeListLettre($link)
{
 $alphabet="abcdefghijklmnopqrstuvwxyz";
 echo '<div id="multi_page">';
 
 // lien pour tous
 if ($_GET['lettre']==''){
     echo '<span class="actuel">Tous</span> ';
 } else {
     echo "<a href='".$link."&lettre='>Tous</a> ";
 }

 // lien alphabétique
 for ($i=0; $i<26; $i++)
   {
          if ($alphabet{$i}==$_GET['lettre'])
             {
             echo '<span class="actuel">'.$alphabet{$i}."</span> ";
             }
          else
             {
             echo "<a href='".$link."&lettre=".$alphabet{$i}."'>".$alphabet{$i}."</a> ";
             }
   }
 echo "</div>\n";
}
?>

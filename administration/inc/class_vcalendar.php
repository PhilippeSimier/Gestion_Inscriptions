<?php
/***************************************************************************

	PHP vCalendar class v2.0

Cette classe permet de crÃ©er des fichiers textes au format vCalendar v1.0 ou 2.0
pour transmettre des Ã©vÃ¨nements vers un agenda
Auteur SIMIER Philippe philaure@wanadoo.fr

si le constructeur recoit pour argument encodage le valeur quoted alors version 1.0
avec l'encodage UTF8 se sera la version 2.0
Un composant "VEVENT" offre un panel de propriÃ©tÃ©s qui dÃ©crivent un Ã©vÃ¨nement comme reprÃ©sentant une quantitÃ© de temps planifiÃ©e
sur un calendrier. En temps normal, un Ã©vÃ¨nement valide rendra ce temps occupÃ©, mais il est possible de le configurer 
en mode "Transparent", pour changer cette interprÃ©tation.

Les propriÃ©tÃ©s classiques d'un composant VEVENT sont :

DTSTART: Date de dÃ©but de l'Ã©vÃ¨nement 
DTEND: Date de fin de l'Ã©vÃ¨nement 
SUMMARY: Titre de l'Ã©vÃ¨nement 
LOCATION: Lieu de l'Ã©vÃ¨nement 
CATEGORIES: CatÃ©gorie de l'Ã©vÃ¨nement (ex: ConfÃ©rence, FÃªte, ...) 
STATUS: Statut de l'Ã©vÃ¨nement (TENTATIVE, CONFIRMED, CANCELLED) 
DESCRIPTION: Description de l'Ã©vÃ¨nement 
TRANSP: DÃ©finit si la ressource affectÃ©e Ã  l'Ã©venement est rendu indisponible (OPAQUE, TRANSPARENT)

Les donnÃ©es vCalendar ont comme type-MIME text/calendar. L'extension ".vcs"
***************************************************************************/


function encode($string) {
	return escape(quoted_printable_encode($string));
}

function escape($string) {
	return str_replace(";","\;",$string);
}

// fonction pour encoder les caractÃ¨res spÃ©ciaux et dÃ©couper la ligne en tronÃ§ons de 76 caractÃ©res
if(!function_exists('quoted_printable_encode')) {
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
}

/*Les lignes de texte NE DEVRAIENT PAS faire plus de 75 octets de long, le saut de ligne exclu. 
Les longues lignes de contenu DEVRAIENT Ãªtre dÃ©coupÃ©es en une reprÃ©sentation sur plusieurs lignes 
Ã  l'aide d'une technique dite de Ã  pliage » (folding) des lignes. C'est-Ã -dire la coupure d'une longue ligne
 entre deux caractÃ¨res en insÃ©rant une sÃ©quence CRLF suivie immÃ©diatement d'un seul caractÃ¨re 
 blanc (whitespace) linÃ©aire, Ã  savoir un  ESPACE (code dÃ©cimal 32 US-ASCII) 
 ou TABULATION HORIZONTALE (code dÃ©cimal 9 US-ASCII). 
 Toute sÃ©quence CRLF suivie immÃ©diatement d'un seul caractÃ¨re blanc linÃ©aire est ignorÃ©e (
 c'est-Ã -dire supprimÃ©e) au traitement du type de contenu.  */

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
	var $properties;    // tableau des propriÃ©tÃ©s d'un evenement
	var $evenements = array();    // tableau des evevements
	var $filename;      // le nom du fichier
	var $encodage;      // le type d'encodage
	var $version;       // version 1.0 ou 2.0
	var $extension;     // l'extension du fichier ics ou vcs
	
	// le constructeur d'objet  vCalendar
    public function __construct ($encodage="UTF8",$name="calendrier")
    {
        $this->encodage = $encodage;
        $this->id = 0;
        if ($encodage=="UTF8"){
			$this->version = "VERSION:2.0\r\n";
            $this->extension = ".ics";
        }
        else { 
			$this->version = "VERSION:1.0\r\n";
            $this->extension = ".vcs";
        }
            $this->filename = $name;
    }

  // mÃ©thode pour ajouter une propriÃ©tÃ©
  function setKey($key,$valeur,$type=""){
        if ($type!="") $key .= ";".$type;
        if ($this->encodage!="UTF8") {
                $key.= ";ENCODING=QUOTED-PRINTABLE";
                $this->properties[$key] = quoted_printable_encode(trim($valeur));
        }
        else {
             	
				$this->properties[$key] = $valeur;
             }
        }

 //mÃ©thode pour ajouter de dÃ©but d'un Ã©vÃ¨nement  DTSTART DTEND
 function setDateTime($key,$datetime) {

	list($date,$time) = explode(" ", $datetime);
	$date = str_replace("-", "", $date);  // retire les - de la date
	$time = str_replace(":", "", $time);  // retire les deux points de l'heure
	$this->properties[$key] = $date."T".$time."Z";
 }


 // mÃ©thode pour crÃ©er un Ã©vÃ¨nement
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

	// mÃ©thode pour crÃ©er le contenu du fichier
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

	// mÃ©thode pour donner le nom du fichier
 function getFileName() {
        return $this->filename.$this->extension;
 }
	// mÃ©thode pour donner le type mime
 function getTypeName() {
       return "Content-Type: text/calendar; ";
 }
}

?>

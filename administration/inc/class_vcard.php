<?php
/***************************************************************************

PHP vCard class v2.0

Cette classe permet de créer des fichiers textes au format vCard v2.0

***************************************************************************/


function encode($string) {
	return escape(quoted_printable_encode($string));
}

function escape($string) {
	return str_replace(";","\;",$string);
}

// taken from PHP documentation comments
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

class vCard {
	var $properties;
	var $filename;

        // méthode pour ajouter une propriété  
        function setKey($key,$valeur,$type=""){
        if ($type!="") $key .= ";".$type;
        $this->properties[$key] = $valeur;
        }

        function setPhoneNumber($number, $type="") {
	// type may be PREF | WORK | HOME | VOICE | FAX | MSG | CELL | PAGER | BBS | CAR | MODEM | ISDN | VIDEO or any senseful combination, e.g. "PREF;WORK;VOICE"
		$key = "TEL";
		if ($type!="") $key .= ";".$type;
		$key.= ";ENCODING=QUOTED-PRINTABLE";
		$this->properties[$key] = quoted_printable_encode($number);
	}
	
	// UNTESTED !!!
	function setPhoto($type, $photo) { // $type = "GIF" | "JPEG"
		$this->properties["PHOTO;TYPE=$type;ENCODING=BASE64"] = base64_encode($photo);
	}
	
	function setFormattedName($name) {
		$this->properties["FN"] = quoted_printable_encode(trim("$prefix $first $additional $family $suffix"));
	}
	
	// méthode pour ajouter le nom et le prénom aux tableaux des propriétés 
	// et pour créer le nom du fichier
        function setName($family="", $first="", $additional="", $prefix="", $suffix="") {
		$this->properties["N"] = "$family;$first;$additional;$prefix;$suffix";
                $this->properties["FN"] = "$prefix $first $family $suffix";
		$family=  strtr($family, "éèëêï ", "eeeei_");
                $this->filename = $first."_".$family.".vcf";
	}
	
	function setBirthday($date) { // $date format is YYYY-MM-DD
		$this->properties["BDAY"] = $date;
	}
	
	function setAddress($postoffice="", $extended="", $street="", $city="", $region="", $zip="", $country="", $type="HOME;POSTAL") {
	// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
		$key = "ADR";
		if ($type!="") $key.= ";$type";
		$key.= ";ENCODING=QUOTED-PRINTABLE";
		$this->properties[$key] = encode($postoffice).";".encode($extended).";".encode($street).";".encode($city).";".encode($region).";".encode($zip).";".encode($country);
		
		//if ($this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] == "") {
			//$this->setLabel($postoffice, $extended, $street, $city, $region, $zip, $country, $type);
		//}
	}
	
	function setLabel($postoffice="", $extended="", $street="", $city="", $region="", $zip="", $country="", $type="HOME;POSTAL") {
		$label = "";
		if ($postoffice!="") $label.= "$postoffice\r\n";
		if ($extended!="") $label.= "$extended\r\n";
		if ($street!="") $label.= "$street\r\n";
		if ($zip!="") $label.= "$zip ";
		if ($city!="") $label.= "$city\r\n";
		if ($region!="") $label.= "$region\r\n";
		if ($country!="") $country.= "$country\r\n";
		
		$this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] = quoted_printable_encode($label);
	}
	
	function setEmail($address) {
		$this->properties["EMAIL;INTERNET"] = $address;
	}
	
	function setNote($note) {
		$this->properties["NOTE;ENCODING=QUOTED-PRINTABLE"] = quoted_printable_encode($note);
	}
	
	function setURL($url, $type="") {
	// $type may be WORK | HOME
		$key = "URL";
		if ($type!="") $key.= ";$type";
		$this->properties[$key] = $url;
	}
	
	function getVCard() {
		$text = "BEGIN:VCARD\r\n";
		$text.= "VERSION:2.1\r\n";
		foreach($this->properties as $key => $value) {
			$text.= "$key:$value\r\n";
		}
		$text.= "REV:".date("Y-m-d")."T".date("H:i:s")."Z\r\n";
		$text.= "MAILER:Export vCard php\r\n";
		$text.= "END:VCARD\r\n";
		return $text;
	}
	
	function getFileName() {
		return $this->filename;
	}
}



?>

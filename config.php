<?php
define("URL", "https://www.academics.de/wissenschaft/professur_37187.html?unpaged=true&amount=5");
define("BASEURL", "https://www.academics.de");

define("TITLEADDITIONS", [
			"W1" => 0,
			"W2" => 0,
			"W3" => 0,
			"C1" => 0,
			"C2" => 0,
			"C3" => 0,
			"C4" => 0,
			"Tenure" => 0,
			"Ass" => 0,
		]);

define("TITLEEXPRESSIONS", [
	"W1" => ["W1","Juniorprofessor","Jun.-Prof.","Juniorprofessur"],
	"W2" => ["W2","Assoc. Prof","Associate Professor"],
	"W3" => ["W3","Full Professor"],
	"C1" => ["C1"],
	"C2" => ["C2"],
	"C3" => ["C3"],
	"C4" => ["C4"],
	"Tenure" => ["Tenure"],
	"Ass" => ["Ass","Assistenz","Assistent","Assistant"],
]);

define("DB_PROPOSAL", [
			"OrgID" => "?",
			"TypeID" => "?",
			"OrgOptID" => "?",
			"TypeOptID" => "?",
			"Title" => "?",
			"Description" => "?",
			"Catchword" => "''",
			"Faculty" => "''",
			"Section" => "''",
			"LID" => "0",
			"SID" => "0",
			"SSID" => "0",
			"Current" => "''",
			"Raw" => "1",
			"Ass" => "?",
			"W1" => "?",
			"W2" => "?",
			"W3" => "?",
			"C1" => "?",
			"C2" => "?",
			"C3" => "?",
			"C4" => "?",
			"Found" => "''",
			"Tenure" => "?",
			"Note" => "''",
			"Enddate" => "?",
			"ASAP" => "''",
			"Publisher1" => "''",
			"Pdate1" => "''",
			"Pissue1" => "''",
			"Pyear1" => "''",
			"Publisher2" => "''",
			"Pdate2" => "''",
			"Pissue2" => "''",
			"Pyear2" => "''",
			"Publisher3" => "''",
			"Pdate3" => "''",
			"Pissue3" => "''",
			"Pyear3" => "''",
			"Publisher4" => "''",
			"Pdate4" => "''",
			"Pissue4" => "''",
			"Pyear4" => "''",
			"Link" => "?",
			"SaveTime" => "Null",
			"subject_culture" => "?",
			"subject_area" => "?",
			"subject" => "?"
		]);

// i = integer, s = string, d = double, b = blob
define("DB_PARAM_TYPES", [
			"i", // OrgID
			"i", // TypeID
			"i", // OrgOptID
			"i", //	TypeOptID
			"s", // Title
			"s", // Description
			"i", // Ass
			"i", // W1
			"i", // W2
			"i", // W3
			"i", // C1
			"i", // C2
			"i", // C3
			"i", // C4
			"i", // Tenure
			"s", // Enddate
			"s", // Link
			"i", // Subject_culture ID
			"i", // Subject_area ID
			"i"  // Subject ID
		]);

class Config
{

	public static function getParam(Proposal &$proposal)
	{
		$orgID = $proposal->getOrganization()->getId(); // OrgtID
		$typeID = $proposal->getOrganization()->getTypeId(); // TypeID
		$orgOptID = ($proposal->getOrgOpt() == 1 ? $proposal->getOrganizationOptional()->getId() : 0);
		$typeOptID = ($proposal->getOrgOpt() == 1 ? $proposal->getOrganizationOptional()->getTypeId() : 0);
		$title = $proposal->getTitle(); // Title
		$desc = $proposal->getDescription(); // Description
		$ass = $proposal->getTitleAdditions()['Ass']; // Ass
		$w1 = $proposal->getTitleAdditions()['W1']; // W1
		$w2 = $proposal->getTitleAdditions()['W2']; // W2
		$w3 = $proposal->getTitleAdditions()['W3']; // W3
		$c1 = $proposal->getTitleAdditions()['C1']; // C1
		$c2 = $proposal->getTitleAdditions()['C2']; // C2
		$c3 = $proposal->getTitleAdditions()['C3']; // C3
		$c4 = $proposal->getTitleAdditions()['C4']; // C4
		$tenure = $proposal->getTitleAdditions()['Tenure']; // Tenure
		$enddate = $proposal->getEnddate(); // Enddate
		$link = $proposal->getLink(); // Link
		$subject_culture = ($proposal->getSubjectCulture() !== NULL ? $proposal->getSubjectCulture() : 0); // Subject cultureID
		$subject_area = ($proposal->getSubjectArea() !== NULL ? $proposal->getSubjectArea() : 0); // Subject Area
		$subject = ($proposal->getSubject() !== NULL ? $proposal->getSubject() : 0); // Subject

		$array = array();
		array_push($array, $orgID);
		array_push($array, $typeID);
		array_push($array, $orgOptID);
		array_push($array, $typeOptID);
		array_push($array, $title);
		array_push($array, $desc);
		array_push($array, $ass);
		array_push($array, $w1);
		array_push($array, $w2);
		array_push($array, $w3);
		array_push($array, $c1);
		array_push($array, $c2);
		array_push($array, $c3);
		array_push($array, $c4);
		array_push($array, $tenure);
		array_push($array, $enddate);
		array_push($array, $link);
		array_push($array, $subject_culture);
		array_push($array, $subject_area);
		array_push($array, $subject);

		return $array;
	}
}
?>

<?php
 require_once 'classes/proposalParser.php';
 require_once 'classes/subjects.php';
 require_once 'classes/proposal.php';
 require_once 'classes/sqlHandler.php';
 require_once 'config.php';
 require_once 'db.php';

 error_reporting(E_ERROR | E_PARSE);


 $url = "http://www.dfg.de/dfg_profil/gremien/fachkollegien/faecher/";
 $domResponse = new DomDocument();
 $domResponse->loadHTMLFile($url);
 $xpath = new DOMXPath($domResponse);

 $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
 $sql->historySubjects($xpath);
?>

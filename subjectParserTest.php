<?php
 require_once 'classes/parser.php';
 require_once 'classes/subjects.php';
 require_once 'classes/sqlHandler.php';
 require_once 'config.php';
 require_once 'db.php';

 $url = "http://www.dfg.de/dfg_profil/gremien/fachkollegien/faecher/";
 $domResponse = new DomDocument();
 $domResponse->loadHTMLFile($url);
 $xpath = new DOMXPath($domResponse);

 $parser = new Parser();
 $parser->gatherSubjects($xpath);
?>

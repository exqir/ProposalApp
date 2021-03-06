<?php
 require_once 'classes/ProposalParser.php';
 require_once 'classes/SubjectGroup.php';
 require_once 'classes/Proposal.php';
 require_once 'classes/sqlHandler.php';
 require_once 'ProposalConfig.php';
 require_once 'credentials.php';

 error_reporting(E_ERROR | E_PARSE);


 $url = "http://www.dfg.de/dfg_profil/gremien/fachkollegien/faecher/";
 $domResponse = new DomDocument();
 $domResponse->loadHTMLFile($url);
 $xpath = new DOMXPath($domResponse);

 $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
 $sql->historySubjects($xpath);
?>

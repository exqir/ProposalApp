<?php
 require_once 'classes/ProposalParser.php';
 require_once 'classes/SubjectGroup.php';
 require_once 'classes/Proposal.php';
 require_once 'classes/sqlHandler.php';
 require_once 'config.php';
 require_once 'db.php';

 error_reporting(E_ERROR | E_PARSE);

 $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
 $sql->historyStates();
?>

<?php
 require_once 'classes/parser.php';
 require_once 'classes/subjects.php';
 require_once 'classes/proposal.php';
 require_once 'classes/sqlHandler.php';
 require_once 'config.php';
 require_once 'db.php';

 error_reporting(E_ERROR | E_PARSE);

 $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
 $sql->historyStates();
?>

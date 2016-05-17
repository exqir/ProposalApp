<?php
require_once 'flight/Flight.php';
require_once __DIR__ . '/../classes/sqlHandler.php';
require_once __DIR__ . '/../db.php';

Flight::route('GET /', function(){
    echo 'GET hello world!';
});

Flight::route('GET /proposals/', function(){
  $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
  echo 'GET proposal';
  //header('Content-type: application/json');
  Flight::json($sql->getProposals());
  $sql->closeConnection();
});

Flight::route('PUT /proposals/', function(){
    echo 'PUT proposal';
});

Flight::route('GET /proposals/@id/', function($id){
  $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
  echo 'GET proposal';
  //header('Content-type: application/json');
  Flight::json($sql->getProposal($id));
  $sql->closeConnection();
});

Flight::start();
?>

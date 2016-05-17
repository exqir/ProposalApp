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
  Flight::json($sql->getProposals());
  $sql->closeConnection();
});

Flight::route('PUT /proposals/', function(){
    echo 'PUT proposal';
});

Flight::route('GET /proposals/@id/', function($id){
  $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
  echo 'GET proposal';
  Flight::json($sql->getProposal($id));
  $sql->closeConnection();
});

Flight::route('PUT /proposals/@id', function(){
    //$sql = new SqlHandler(HOST,USER,PW,DB_NAME);
    echo 'PUT proposal<br />';
    $payload = json_decode(Flight::request()->getBody(), true);
    //$json = json_decode($payload, true);
    //var_dump(json_decode($payload, true));
    //echo $json["test"];
    $proposal = new Proposal();
    $proposal->setId($payload["ID"]);
    $proposal->setTitle($payload["Title"]);
    //$sql->editProposal();
    //$sql->closeConnection();
});

Flight::start();
?>

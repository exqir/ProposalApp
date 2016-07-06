<?php
require_once 'flight/Flight.php';
require_once __DIR__ . '/../classes/sqlHandler.php';
require_once __DIR__ . '/../classes/proposal.php';
require_once __DIR__ . '/../classes/organization.php';
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
    $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
    echo 'PUT proposal';
    $payload = json_decode(Flight::request()->getBody(), true);
    //$json = json_decode($payload, true);
    //var_dump(json_decode($payload, true));
    //echo $json["test"];
    $proposal = new Proposal();
    $proposal->setProposalByArray($payload);
    $sql->editProposal($proposal);
    //echo $proposal->getTitle();
});

Flight::route('GET /organizations/', function(){
  $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
  echo 'GET organizations';
  Flight::json($sql->getOrganizations());
  $sql->closeConnection();
});

Flight::route('GET /organizations/@id/', function($id){
  $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
  echo 'GET organization';
  Flight::json($sql->getOrganization($id));
  $sql->closeConnection();
});

Flight::route('PUT /organizations/@id/', function(){
  $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
  echo 'PUT organization';
  $payload = json_decode(Flight::request()->getBody(), true);
  $organization = new Organization($payload["Name"]);
  $organization->setOrganizationByArray($payload);
  $sql->editOrganization($organization);
});

Flight::route('PUT /organizations/@id/merge/@secId', function($id,$secId){
  $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
  echo 'GET MERGE';
  $res = $sql->mergeOrangization($id,$secId);
  echo 'RESULT: '. $res;
  $sql->closeConnection();
});

Flight::route('GET /statistics/organizations/', function(){
  $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
  echo 'GET organizations';
  Flight::json($sql->getOrganizationNames());
  $sql->closeConnection();
});

Flight::route('GET /subjects-lists/cultures/', function(){
  $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
  echo 'GET organizations';
  Flight::json($sql->getSubjectCultures());
  $sql->closeConnection();
});

Flight::route('GET /subjects-lists/areas/', function(){
  $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
  echo 'GET organizations';
  Flight::json($sql->getSubjectAreas());
  $sql->closeConnection();
});

Flight::route('GET /subjects-lists/subjects/', function(){
  $sql = new SqlHandler(HOST,USER,PW,DB_NAME);
  echo 'GET organizations';
  Flight::json($sql->getSubjects());
  $sql->closeConnection();
});

Flight::start();
?>

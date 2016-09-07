<?php
require_once 'flight/Flight.php';
require_once __DIR__ . '/../../db.php';

require_once __DIR__ . '/statistics/OrganizationStatistics.php';
require_once __DIR__ . '/statistics/ProposalStatistics.php';

require_once __DIR__ . '/organization/OrganizationGets.php';
require_once __DIR__ . '/organization/OrganizationPuts.php';

require_once __DIR__ . '/proposal/ProposalGets.php';
require_once __DIR__ . '/proposal/ProposalPuts.php';

require_once __DIR__ . '/subject/SubjectGets.php';


Flight::route('GET /', function(){
    echo 'GET hello world!';
});

/*********************
** /PROPOSALS
**********************/

Flight::route('GET /proposals/', function(){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'GET proposals';
  $pg = new ProposalGets($db->getConnection());
  Flight::json($pg->getProposals());
  $db->closeConnection();
});

Flight::route('GET /proposals/@id/', function($id){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'GET proposal';
  $pg = new ProposalGets($db->getConnection());
  Flight::json($pg->getProposal($id));
  $db->closeConnection();
});

Flight::route('PUT /proposals/@id', function(){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'PUT proposal';
  $payload = json_decode(Flight::request()->getBody(), true);
  $pp = new ProposalPuts($db->getConnection());
  $pp->putProposal($payload);
  $db->closeConnection();
});

/*********************
 ** /ORGANIZATIONS
 **********************/

Flight::route('GET /organizations/', function(){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'GET organizations';
  $og = new OrganizationGets($db->getConnection());
  Flight::json($og->getOrganizations());
  $db->closeConnection();
});

Flight::route('GET /organizations/@id/', function($id){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'GET organizations';
  $og = new OrganizationGets($db->getConnection());
  Flight::json($og->getOrganization($id));
  $db->closeConnection();
});

Flight::route('PUT /organizations/@id/', function(){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'PUT organizations';
  $payload = json_decode(Flight::request()->getBody(), true);
  $op = new OrganizationPuts($db->getConnection());
  $op->putOrganization($payload);
  $db->closeConnection();
});

Flight::route('PUT /organizations/@id/merge/@secId', function($id,$secId){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'GET MERGE';
  //@id: Alias of organization
  //@secId: Main organization
  $op = new OrganizationPuts($db->getConnection());
  $res = $op->mergeOrangization($secId,$id);
  echo 'RESULT: '. $res;
  $db->closeConnection();
});

Flight::route('GET /organizations/@id/alias/', function($id){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'GET ALIASES';
  $og = new OrganizationGets($db->getConnection());
  Flight::json($og->getAliasOfOrganization($id));
  $db->closeConnection();
});

/*********************
 ** /STATISTICS
 **********************/

Flight::route('GET /statistics/organizations/', function(){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'GET organizations';
  $os = new OrganizationStatistics($db->getConnection());
  Flight::json($os->getOrganizations());
  $db->closeConnection();
});

Flight::route('GET /statistics/organizations/used/', function(){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'GET organizations';
  $os = new OrganizationStatistics($db->getConnection());
  Flight::json($os->getUsedOrganizations());
  $db->closeConnection();
});

Flight::route('GET /statistics/organization-types/', function(){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'GET organization types';
  $os = new OrganizationStatistics($db->getConnection());
  Flight::json($os->getOrganizationTypes());
  $db->closeConnection();
});

Flight::route('GET /statistics/organizations/states/', function(){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'GET organizations';
  $os = new OrganizationStatistics($db->getConnection());
  Flight::json($os->getStates());
  $db->closeConnection();
});

Flight::route('GET /statistics/organizations/states/used', function(){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'GET organizations';
  $os = new OrganizationStatistics($db->getConnection());
  Flight::json($os->getUsedStates());
  $db->closeConnection();
});

Flight::route('GET /statistics/proposals/@country', function($country){
    $db = new SqlConnection(HOST,USER,PW,DB_NAME);
    echo 'GET organizations';
    $ps = new ProposalStatistics($db->getConnection());
    Flight::json($ps->getProposalsByCountry($country));
    $db->closeConnection();
});

/*********************
 ** /SUBJECTS
 **********************/

Flight::route('GET /subjects/cultures/', function(){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'GET cultures';
  $sg = new SubjectGets($db->getConnection());
  Flight::json($sg->getSubjectCultures());
  $db->closeConnection();
});

Flight::route('GET /subjects/areas/', function(){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'GET cultures';
  $sg = new SubjectGets($db->getConnection());
  Flight::json($sg->getSubjectAreas());
  $db->closeConnection();
});

Flight::route('GET /subjects/subjects/', function(){
  $db = new SqlConnection(HOST,USER,PW,DB_NAME);
  echo 'GET cultures';
  $sg = new SubjectGets($db->getConnection());
  Flight::json($sg->getSubjects());
  $db->closeConnection();
});

Flight::start();
?>

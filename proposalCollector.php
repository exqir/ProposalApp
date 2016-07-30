<?php
	require_once 'backend/Util.php';
	require_once 'backend/models/organization.php';
	require_once 'backend/models/proposal.php';
	require_once 'backend/models/subjects.php';
	require_once 'backend/parser/parser.php';
	require_once 'backend/parser/organizationParser.php';
	require_once 'backend/db/sqlHandler.php';
	require_once 'config.php';
	require_once 'db.php';

	error_reporting(E_ERROR | E_PARSE);

	$newProposals = collectProposalsFrom(URL);

	function collectProposalsFrom($url) {
		$db = new sqlConnection(HOST,USER,PW,DB_NAME);
		return array_map(function($job) {
			$proposal = Proposal::fromDOMElement($job);
			if(!$proposal->doesExistIn($db) {
				$proposal->enrichAttributes($db);
				$proposal->saveTo($db);
			}
		},Util::getJobItemsFromUrl($url));
	}

	$proposals = array();

	$domResponse = new DomDocument();
	$domResponse->loadHTMLFile(URL);
//////////
	$url = "http://www.dfg.de/dfg_profil/gremien/fachkollegien/faecher/";
  $domRes = new DomDocument();
  $domRes->loadHTMLFile($url);
  $xpath = new DOMXPath($domRes);
//////////
	$resultBox = Util::getParentElementByClass($domResponse, 'div', 'result-box');

	$jobItems = Util::getElementsByClass($resultBox, 'div', 'job-item');
	$sql = new SqlHandler(HOST,USER,PW,DB_NAME);
	$parser = new Parser();
	foreach($jobItems as $job)
	{
		$proposal = new Proposal();
		$parser->parseTitle($proposal, $job);
		$parser->parseEmployer($proposal, $job);
		$parser->gatherSubjects($xpath, $proposal);
		$sql->handleProposal($proposal);

		array_push($proposals, $proposal);
		echo $proposal->getTitle() . " / " . $proposal->getLink() . " | " . $proposal->getSubjectCulture() . " : " . $proposal->getSubjectArea() . " : " . $proposal->getSubject() . "</br>";
		//echo $proposal->getInstitut()->getName() . " / " . $proposal->getInstitut()->getCity() . " / " . $proposal->getInstitut()->getCountry() . " / " . $proposal->getEnddate() . "</br>";
		//var_dump($proposal->getTitleAdditions());
		echo "</br>";
	}
	$sql->closeConnection();

?>

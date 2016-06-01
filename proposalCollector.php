<?php
	require_once 'classes/Util.php';
	require_once 'classes/proposal.php';
	require_once 'classes/sqlHandler.php';
	require_once 'classes/parser.php';
	require_once 'config.php';
	require_once 'db.php';

	error_reporting(E_ERROR | E_PARSE);
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

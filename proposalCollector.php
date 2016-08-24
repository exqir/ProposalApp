<?php
	require_once 'backend/Util.php';
	require_once 'backend/models/organization.php';
	require_once 'backend/models/proposal.php';
	require_once 'backend/models/subjects.php';
	require_once 'backend/parser/proposalParser.php';
	require_once 'backend/parser/organizationParser.php';
	require_once 'backend/parser/SubjectParser.php';
	require_once 'backend/db/sqlConnection.php';
	require_once 'backend/db/organizationSqlQueries.php';
	require_once 'backend/db/proposalSqlQueries.php';
	require_once 'backend/db/SubjectSqlQueries.php';
	require_once 'backend/db/locationService.php';
	require_once 'config.php';
	require_once 'db.php';

	error_reporting(E_ERROR | E_PARSE);

	$newProposals = collectProposalsFrom(URL);
	printProposals($newProposals);

	function collectProposalsFrom($url) {
		$db = new SqlConnection(HOST,USER,PW,DB_NAME);
		$subjects = collectSubjectsFrom(SUBJECT_URL,$db);
		//var_dump($subjects);
		return array_map(function($job) use ($db, $subjects) {
			$proposal = Proposal::fromDOMElement($job);
			if(!$proposal->doesExistIn($db)) {
				$proposal = $proposal->getProposalWithEnrichedAttributes($db,$proposal->getLink(),$subjects);
				//$proposal->saveTo($db);
				return $proposal;
			}
		},Util::getJobItemsFromUrl($url));
	}

	function collectSubjectsFrom($url,$db) {
		return SubjectGroup::fromXPath(Util::getXPathFromUrl($url),$db);
	}

	function printProposals($proposals) {
		array_map(function($proposal) {
			print("Titel: " . $proposal->getTitle() . "<br>");
			print("Subjects: " . $proposal->getSubjectCulture() . " -> " . $proposal->getSubjectArea() . " -> " . $proposal->getSubject() . "<br>");
			print("Organisation: " . $proposal->getOrganization()->getName() . ", " . $proposal->getOrganization()->getState() . "<br>");
			if($proposal->getOrganizationOptional() !== NULL)
				print("Optionale Organisation: " . $proposal->getOrganizationOptional->getName() . ", " . $proposal->getOrganizationOptional()->getState() . "<br>");
			print("------------------------------------------------------<br>");
		},$proposals);
	}
/*	$proposals = array();

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
	$sql->closeConnection();*/

?>

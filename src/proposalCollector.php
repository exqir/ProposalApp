<?php

    require_once 'backend/db/SqlConnection.php';
    require_once 'backend/proposal/Proposal.php';
    require_once 'backend/organization/Organization.php';
    require_once 'backend/subject/SubjectGroup.php';
    require_once 'backend/util/Util.php';
    require_once 'backend/config.php';
    require_once 'credentials.php';

	error_reporting(E_ERROR | E_PARSE);

	$newProposals = collectProposalsFrom(URL);
	printProposals($newProposals);

	function collectProposalsFrom($url) {
		$db = new SqlConnection(HOST,USER,PW,DB_NAME);
		$subjects = collectSubjectsFrom(SUBJECT_URL,$db);
		return array_map(function($job) use ($db, $subjects) {
			$proposal = Proposal::fromDOMElement($job);
			if(!$proposal->doesExistIn($db)) {
				$proposal = $proposal->getProposalWithEnrichedAttributes($db,$proposal->getLink(),$subjects);
				$proposal = $proposal->saveTo($db);
			}
			return $proposal;
		},Util::getJobItemsFromUrl($url));
	}

	function collectSubjectsFrom($url,$db) {
		return SubjectGroup::fromXPath(Util::getXPathFromUrl($url),$db);
	}

	function printProposals($proposals) {
		array_map(function(Proposal $proposal) {
			if($proposal->getId() !== NULL) {
				print("Titel: " . $proposal->getTitle() . "<br>");
				print("Subjects: " . $proposal->getSubjectCulture() . " -> " . $proposal->getSubjectArea() . " -> " . $proposal->getSubject() . "<br>");
				print("Organisation: " . $proposal->getOrganization()->getAbbrev() . " - " .  $proposal->getOrganization()->getName() . ", " . $proposal->getOrganization()->getCity() . ", " . $proposal->getOrganization()->getState() . "<br>");
				if($proposal->getOrganizationOptional() !== NULL)
					print("Optionale Organisation: " . $proposal->getOrganizationOptional()->getAbbrev() . " - " .  $proposal->getOrganizationOptional()->getName() . ", " . $proposal->getOrganizationOptional()->getCity() . ", " . $proposal->getOrganizationOptional()->getState() . "<br>");
				print("------------------------------------------------------<br>");
			}
		},$proposals);
	}

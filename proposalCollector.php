<?php
	require_once 'classes/Util.php';
	require_once 'classes/proposal.php';
	require_once 'classes/sqlHandler.php';
	require_once 'classes/parser.php';
	require_once 'config.php';
	require_once 'db.php';

	$proposals = array();

	$domResponse = new DomDocument();
	$domResponse->loadHTMLFile(URL);

	$resultBox = Util::getParentElementByClass($domResponse, 'div', 'result-box');

	$jobItems = Util::getElementsByClass($resultBox, 'div', 'job-item');
	$sql = new SqlHandler(HOST,USER,PW,DB_NAME);
	$parser = new Parser();
	foreach($jobItems as $job)
	{
		$proposal = new Proposal();

		$parser->parseTitle($proposal, $job);

		$parser->parseEmployer($proposal, $job);
		$proposalCheck = $sql->checkProposalExistance($proposal);
		echo "Proposal Existance: " . $proposalCheck ."</br>";
		if($proposalCheck == 0)
		{
			$desc = $proposal->getLink();
			$proposal->setDescription($desc);
			$institutCheck = $sql->checkInstituteExistance($proposal->getInstitut());
			echo $test;
			if($proposal->getInstOpt() == 1)
			{
				$instOptCheck = $sql->checkInstituteExistance($proposal->getInstitutOptional());
				echo "Institut Optional Existance: " . $instOptCheck ."</br>";
				echo "Type ID: " . $proposal->getInstitutOptional()->getTypeId() . "</br>";
				echo "Inst ID: " . $proposal->getInstitutOptional()->getId() . "</br>";
			}
			echo "Institut Existance: " . $institutCheck ."</br>";

			echo "Type ID: " . $proposal->getInstitut()->getTypeId() . "</br>";
			echo "Inst ID: " . $proposal->getInstitut()->getId() . "</br>";

			$proposalSave = $sql->saveProposal($proposal);
			if($proposalSave == 1)
			{
				echo "Proposal saved </br>";
			}

		}

		array_push($proposals, $proposal);
		echo $proposal->getTitle() . " / " . $proposal->getLink() . "</br>";
		//echo $proposal->getInstitut()->getName() . " / " . $proposal->getInstitut()->getCity() . " / " . $proposal->getInstitut()->getCountry() . " / " . $proposal->getEnddate() . "</br>";
		//var_dump($proposal->getTitleAdditions());
		echo "</br>";
	}
	$sql->closeConnection();

?>

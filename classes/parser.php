<?php
/**
* Provides functions to parse a DOM element
**/
class Parser
{
	public function __construct()
	{

	}

	/**
	* Parses additional information (C1,C2,..) from the $title and returns an array with the type (C1,C2,..) as key and a 1 as value when found and 0 when not
	**/
	public function searchTitleAdditions($title)
	{
		$titleArray = TITLEADDITIONS;
		foreach(array_keys($titleArray) as $arrayKey)
		{
			if(strpos($title,$arrayKey) !== false)
			{
				$titleArray[$arrayKey] = 1;

				if(strpos($arrayKey, "Ass") !== false)
				{
					if(strpos($title, "Assistenz") !== false || strpos($title, "Assistent") !== false || strpos($title, "Assistant") !== false)
					{
						$titleArray[$arrayKey] = 1;
					}
					else
					{
						$titleArray[$arrayKey] = 0;
					}
				}
			}
		}
		return $titleArray;
	}
	public function searchT($title)
	{
		$resultArray = TITLEADDITIONS;
		$titleArray = [
			"W1" => ["W1"],
			"W2" => ["W2"],
			"W3" => ["W3"],
			"C1" => ["C1"],
			"C2" => ["C2"],
			"C3" => ["C3"],
			"C4" => ["C4"],
			"Tenure" => ["Tenure"],
			"Ass" => ["Ass","Assistenz","Assistent","Assistant"],
		];
		foreach (array_keys($titleArray) as $addition) {
			foreach ($titleAdditions[$addition] as $expression) {
				if(strpos($title,$expression) !== false) {
					$resultArray[$addition] = 1;
					break;
				}
			}
		}
		if($resultArray["Tenure"] == 1) {
			if(strpos($title,"ohne Tenure") !== false || strpos($title,"keine Tenure") !== false) {
				$resultArray["Tenure"] = 0;
			}
		}
		return $resultArray;
	}

	/**
	* Parses title, link and titleAdditions from an <a> Element with the class='titel' inside the $domElement
	* and sets them for the trasmitted $proposal object
	**/
	public function parseTitle(Proposal &$proposal, $domElement)
	{
		$title = Util::getParentElementByClass($domElement, 'a', 'titel');
		$titleString = $title->nodeValue;
		$titleString = trim($titleString);
		$proposal->setTitle($titleString);
		$link = $title->getAttribute('href');
		$proposal->setLink($link);
		$titleAdditions = $this->searchTitleAdditions($title->nodeValue);
		$proposal->setTitleAdditions($titleAdditions);
	}

	/**
	* Parses the institut name, city and, if existing, country of a <span> Element with the class='employer' inside the $domElement
	* and sets them for the trasmitted $proposal object
	**/
	public function parseEmployer(Proposal &$proposal, $domElement)
	{
		$employerNode = Util::getParentElementByClass($domElement, 'span', 'employer');
		$employer = $employerNode->nodeValue;

		$employer = explode(",", $employer);
		$count = count($employer);
		if(strpos($employer[0],"/") !== false)
		{
			$instTmp = explode("/", $employer[0]);
			$institut = new Institut($instTmp[0]);
			$institutOptional = new Institut($instTmp[1]);
			$proposal->setInstOpt(1);

			if($count > 3)
			{
				$this->parseCity($institut, $employer[1]);
				$this->parseCity($institutOptional, $employer[2]);
				$proposal->setEnddate(implode('-', array_reverse(explode('.', trim($employer[$count-1])))));
			}
			else
			{
				$this->parseCity($institut, $employer[1]);
				$this->parseCity($institutOptional, $employer[1]);
				$proposal->setEnddate(implode('-', array_reverse(explode('.', trim($employer[$count-1])))));
			}
			$proposal->setInstitut($institut);
			$proposal->setInstitutOptional($institutOptional);
		}
		else
		{
			$institut = new Institut($employer[0]);
			$this->parseCity($institut, $employer[1]);

			$proposal->setInstitut($institut);
			$proposal->setEnddate(implode('-', array_reverse(explode('.', trim($employer[$count-1])))));
		}
	}

	private function parseCity(Institut &$institut, $string)
	{
		if(strpos($string,"(") !== false)
		{
			$temp = explode("(", $string);
			$city = $temp[0];
			$country = explode(")", $temp[1])[0];
			$institut->setCity($city);
			$institut->setCountry($country);
		}
		else
		{
			$institut->setCity($string);
		}
	}
}
?>

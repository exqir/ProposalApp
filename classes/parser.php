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
		$resultArray = TITLEADDITIONS;
		$titleArray = TITLEEXPRESSIONS;
		foreach (array_keys($titleArray) as $addition) {
			foreach ($titleArray[$addition] as $expression) {
				if(strpos($title,$expression) !== false) {
					$resultArray[$addition] = 1;
					break;
				}
			}
		}
		if($resultArray["Tenure"] == 1) {
			if(strpos($title,"ohne Tenure") !== false || strpos($title,"keine Tenure") !== false) {
				$resultArray["Tenure"] = -1;
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
			$organization = new Organization($instTmp[0]);
			$organizationOptional = new Organization($instTmp[1]);
			$proposal->setOrgOpt(1);

			if($count > 3)
			{
				$this->parseCity($organization, $employer[1]);
				$this->parseCity($organizationOptional, $employer[2]);
				$proposal->setEnddate(implode('-', array_reverse(explode('.', trim($employer[$count-1])))));
			}
			else
			{
				$this->parseCity($organization, $employer[1]);
				$this->parseCity($organizationOptional, $employer[1]);
				$proposal->setEnddate(implode('-', array_reverse(explode('.', trim($employer[$count-1])))));
			}
			$proposal->setOrganization($organization);
			$proposal->setOrganizationOptional($organizationOptional);
		}
		else
		{
			$organization = new Organization($employer[0]);
			$this->parseCity($organization, $employer[1]);

			$proposal->setOrganization($organization);
			$proposal->setEnddate(implode('-', array_reverse(explode('.', trim($employer[$count-1])))));
		}
	}

	private function parseCity(Organization &$organization, $string)
	{
		if(strpos($string,"(") !== false)
		{
			$temp = explode("(", $string);
			$city = $temp[0];
			$country = explode(")", $temp[1])[0];
			$organization->setCity($city);
			$organization->setCountry($country);
		}
		else
		{
			$organization->setCity($string);
		}
	}

  public function parseSubjects($domObject) {
    private $subjects = new Array();
    //$tableBox = get...
    foreach($tableBox as $subject1) {
      $name = $subject1->getElementsByTagName('caption')->item(0)->nodeValue;
      $name = trim(explode('(',$name)[0]);
      $sub = new SubjectGroup($name);

      $subSubjects = get...
      foreach($subSubjects as $subject2) {
        $sname = $subject2->nodeValue;
        $ssub = new SubjectGroup($sname);
        
        $subKats = get...
        foreach($subKats as $subject3) {
          $ssname = $subject3->nodeValue;
          $ssub->addSubjectChildren($ssname);
        }
        $sub->addSubjectChildren($ssub);
      }
      $subjects[] = $sub;
    }
    return $subjects;
  }
}
?>

<?php
require_once 'classes/subjects.php';
/**
* Provides functions to parse a DOM element
**/
class Parser
{
	public function __construct()
	{

	}

	public static function getProposalFromDomElement($domElement,$proposal) {
		$i = new self();
		$titleNode = Util::getParentElementByClass($domElement, 'a', 'titel');
		$employerNode = Util::getParentElementByClass($domElement, 'span', 'employer');
		$proposal =
			$i->getProposalWithEmployerFromNode($employerNode,
				$i->getProposalWithTitleAddtionsFromNode($titleNode,
					$i->getProposalWithLinkFromNode($titleNode,
						$i->getProposalWithTitleFromNode($titleNode, $proposal))));
		return $proposal;
	}

	/**
	 *
	 **/
	private function getProposalWithTitleFromNode($node,$proposal) {
		return $proposal->setTitle(trim($node->nodeValue));
	}

	private function getProposalWithLinkFromNode($node,$proposal) {
		return $proposal->setLink($node->getAttribute('href'));
	}

	private function getProposalWithTitleAddtionsFromNode($node,$proposal) {
		return $proposal->setTitleAdditions($this->getTitleAdditionsFromTitle($node));
	}

	/**
	* Parses additional information (C1,C2,..) from the $title and returns an array with the type (C1,C2,..) as key and a 1 as value when found and 0 when not
	**/
	private function getTitleAdditionsFromTitle($node)
	{
		$resultArray = TITLEADDITIONS;
		$titleArray = TITLEEXPRESSIONS;
		foreach (array_keys($titleArray) as $addition) {
			foreach ($titleArray[$addition] as $expression) {
				if(strpos($node,$expression) !== false) {
					$resultArray[$addition] = 1;
					break;
				}
			}
		}
		if($resultArray["Tenure"] == 1) {
			if(strpos($node,"ohne Tenure") !== false || strpos($node,"keine Tenure") !== false) {
				$resultArray["Tenure"] = -1;
			}
		}
		return $resultArray;
	}

	private function getProposalWithEmployerFromNode($node, $proposal) {
		$e = explode(',',$node->nodeValue);
		$proposal = OrganizationParser::getProposalWithOrganizationsFromArray($e, $proposal);
		return $proposal;
	}

	/********  TODO: TRENNEN UND IN ANDERE/EIGENE CLASS  ***********/

	public static function getOrganizationFromDomElement($eArray,$organization) {
		$i = new self();
		$organization = $i->($domElement,$organization);
		return $organization;
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

	public function findSubjects (Proposal &$proposal, $subjectReference) {
		//$subjects = $this->parseSubjects($subjectReference);
		$this->searchSubjects($subjectReference,$proposal,$proposal->getTitle());
		if($proposal->getSubject() === NULL || $proposal->getSubjectArea === NULL) {
			echo "searching desc <br>";
			$this->searchSubjects($subjectReference,$proposal,$proposal->getDescription());
		}
	}

	private function searchSubjects($subjectCultures, &$proposal,$searchIn) {
		foreach ($subjectCultures as $subjectCulture) {
			$subjectAreas = $subjectCulture->getSubjectGroup();
			foreach ($subjectAreas as $subjectArea) {
				$subjects = $subjectArea->getSubjectGroup();
				foreach ($subjects as $subject) {
					$subjectName = $subject->getName();
					if(stripos($searchIn,$subjectName) !== false) {
						$proposal->setSubjectCulture($subjectCulture->getId());
						$proposal->setSubjectArea($subjectArea->getId());
						$proposal->setSubject($subject->getId());
					}
				}
				$area = $subjectArea->getName();
				if(stripos($searchIn,$area) !== false) {
					$proposal->setSubjectCulture($subjectCulture->getId());
					$proposal->setSubjectArea($subjectArea->getId());
				}
			}
			$culture = $subjectCulture->getName();
			if(stripos($searchIn,$culture) !== false) {
				$proposal->setSubjectCulture($subjectCulture->getId());
			}
		}
	}

	public function gatherSubjects($domObject,&$proposal) {
		$subjects = $this->parseSubjects($domObject);
		$sql = new SqlHandler(HOST,USER,PW,DB_NAME);
		$res = $sql->saveSubjects($subjects);
		$this->findSubjects($proposal, $subjects);
		if($res > 0) return 1;
		else return 0;
	}

  private function parseSubjects($domObject) {
    $subjects = array();
		//$tableBox = get...
		$expression = "(//div)[contains(concat(' ', normalize-space(@class), ' '), concat(' ', 'tabellenBox', ' '))]/table";
		$tableBox = $domObject->query($expression);
    foreach($tableBox as $subject1) {
			$tmp = $subject1->firstChild->nodeValue;
			$name = trim(explode('(',$tmp)[0]);
      $sub = new SubjectGroup($name);

			$exp1 = "tbody/tr/td[2]";
      $subSubjects = $domObject->query($exp1,$subject1);
      foreach($subSubjects as $subject2) {
        $sname = $subject2->firstChild->nodeValue;
        $ssub = new SubjectGroup($sname);

				$exp2 = "div[contains(concat(' ', normalize-space(@class), ' '), concat(' ', 'fachInhalt', ' '))]/span[contains(concat(' ', normalize-space(@class), ' '), concat(' ', 'subKat', ' '))]/a";
        $subKats = $domObject->query($exp2,$subject2);
        foreach($subKats as $subject3) {
          $ssname = $subject3->nodeValue;
          $ssub->addSubjectChildren(new SubjectGroup($ssname));
        }
        $sub->addSubjectChildren($ssub);
      }
      $subjects[] = $sub;
    }
    return $subjects;
  }
}
?>
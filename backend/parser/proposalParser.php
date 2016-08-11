<?php
/**
* Provides functions to parse a DOM element
**/
class ProposalParser
{
	public function __construct() {

	}

	public static function getProposalFromDomElement($domElement,$proposal) {
		$i = new self();
		$titleNode = Util::getParentElementByClass($domElement, 'a', 'titel');
		$employerNode = Util::getParentElementByClass($domElement, 'span', 'employer');
		$proposal =
			$i->getProposalWithEnddateFromNode($employerNode,
				$i->getProposalWithEmployerFromNode($employerNode,
					$i->getProposalWithTitleAddtionsFromNode($titleNode,
						$i->getProposalWithLinkFromNode($titleNode,
							$i->getProposalWithTitleFromNode($titleNode, $proposal)))));
		return $proposal;
	}

	public static function getDescriptionFromLink($link) {
		$domDoc = new DomDocument();
		$domDoc->loadHTMLFile(BASEURL . $link);
		return trim((Util::getParentElementByClass($domDoc, 'div', 'jw-wrapper'))->nodeValue);
	}

	public static function getProposalWithSubjects($subjects,Proposal $proposal) {
		$i = new self();
		$proposal = $i->getProposalWithSubjectsComparedTo($subjects, $proposal, $proposal->getTitle());
		if($proposal->getSubjects() == null || $proposal->getSubjectArea() == null) {
			$proposal = $i->getProposalWithSubjectsComparedTo($subjects, $proposal, $proposal->getDescription());
		}
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
	private function getTitleAdditionsFromTitle($node) {
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

	private function getProposalWithEnddateFromNode($node, $proposal) {
		$e = explode(',',$node->nodeValue);
		return $proposal->setEnddate(implode('-', array_reverse(explode('.', trim($e[count($e)-1])))));
	}

	private function getProposalWithSubjectsComparedTo($subjects,Proposal $proposal, $compareWith) {
		return 	$this->getProposalWithSubjectFromArea(
					$this->getProposalWithAreaFromCulture(
						$this->getProposalWithCultureFromSubjects($subjects, $proposal, $compareWith), $compareWith), $compareWith);
	}

	private function getProposalWithCultureFromSubjects($subjects, Proposal $proposal, $compareWith) {
		array_map(function($subjectCulture) use ($proposal, $compareWith) {
			if(stripos($compareWith, $subjectCulture->getName()) !== false) {
				return array($proposal->setSubjectCulture($subjectCulture->getId()),$subjectCulture->getSubjectChildren());
			}
		},$subjects);
		return array($proposal,null);
	}

	private function getProposalWithAreaFromCulture($arrayProposalAndCulture, $compareWith) {
		$proposal = $arrayProposalAndCulture[0];
		$areas = $arrayProposalAndCulture[1];
		if($areas !== null) {
			array_map(function($subjectArea) use ($compareWith,$proposal) {
				if(stripos($compareWith, $subjectArea->getName()) !== false) return array($proposal->setSubjectArea($subjectArea->getId()),$subjectArea->getSubjectChildren());
			},$areas);
		}
		return array($proposal, null);
	}

	private function getProposalWithSubjectFromArea($arrayProposalAndArea, $compareWith) {
		$proposal = $arrayProposalAndArea[0];
		$subjects = $arrayProposalAndArea[1];
		if($subjects !== null) {
			array_map(function($subject) use ($compareWith,$proposal) {
				if(stripos($compareWith, $subject->getName()) !== false) return $proposal->setSubject($subject->getId());
			},$subjects);
		}
		return $proposal;
	}


	/********  TODO: TRENNEN UND IN ANDERE/EIGENE CLASS  ***********/

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

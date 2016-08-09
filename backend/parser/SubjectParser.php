<?php
/**
* Provides functions to parse a DOM element
**/
class SubjectParser
{
	public function __construct() {

	}

	public static function getSubjectsFromXPath($xpath) {
		$i = new self();
		$subjects = $i->getSubjectCultures($xpath);
		return $subjects;

	}

	private function getSubjectCultures($xpath) {
		$cultures = array();
		$expression = "(//div)[contains(concat(' ', normalize-space(@class), ' '), concat(' ', 'tabellenBox', ' '))]/table";
		$domCultures = $xpath->query($expression);
		foreach($domCultures as $subjectCulture) {
			$cultureName = trim(explode('(',$subjectCulture->firstChild->nodeValue)[0]);
			$culture = new SubjectGroup($cultureName);
			print ("-" . $cultureName . "<br>");
			$cultures[] = $culture->setSubjectChildren($this->getSubjectAreas($xpath, $subjectCulture));
		}
		return $cultures;
	}

	private function getSubjectAreas($xpath, $subjectCulture) {
		$areas = array();
		$expression = "tbody/tr/td[2]";
		$domAreas = $xpath->query($expression, $subjectCulture);
		foreach($domAreas as $subjectArea) {
			$areaName = $subjectArea->firstChild->nodeValue;
			$area = new SubjectGroup($areaName);
			print ("--" . $areaName . "<br>");
			$areas[] = $area->setSubjectChildren($this->getSubjects($xpath, $subjectArea));
		}
		return $areas;
	}

	private function getSubjects($xpath, $subjectArea) {
		$subjects = array();
		$expression = "div[contains(concat(' ', normalize-space(@class), ' '), concat(' ', 'fachInhalt', ' '))]/span[contains(concat(' ', normalize-space(@class), ' '), concat(' ', 'subKat', ' '))]/a";
		$domSubjects = $xpath->query($expression, $subjectArea);
		foreach($domSubjects as $subject) {
			$subjectName = $subject->nodeValue;
			print ("---" . $subjectName . "<br>");
			$subjects = new SubjectGroup($subjectName);
		}
		return $subjects;
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
}
?>

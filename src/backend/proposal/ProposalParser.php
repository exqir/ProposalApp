<?php

require_once __DIR__ . '/../organization/OrganizationParser.php';
require_once __DIR__ . '/../util/Util.php';
require_once 'ProposalConfig.php';

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
		$desc = trim(Util::getParentElementByClass($domDoc, 'div', 'jw-wrapper')->nodeValue);
		return $desc;
	}

	public static function getProposalWithSubjects($subjects,Proposal $proposal) {
		$i = new self();
		$proposal = $i->getProposalWithSubjectsComparedTo($subjects, $proposal, $proposal->getTitle());
		if($proposal->getSubject() == null || $proposal->getSubjectArea() == null) {
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
		return $proposal->setTitleAdditions($this->getTitleAdditionsFromTitle($node->nodeValue));
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
		foreach ($subjects as $culture) {
			$proposal = $this->getProposalWithArea($culture->getSubjectChildren(), $proposal, $compareWith);
			if($proposal->getSubjectCulture() == NULL) {
				if(stripos($compareWith, $culture->getName()) !== false) return $proposal->setSubjectCulture($culture->getId());
			}
		}
		return $proposal;
	}

	private function getProposalWithArea($areas, Proposal $proposal, $compareWith) {
		foreach ($areas as $area) {
			$proposal = $this->getProposalWithSubject($area->getSubjectChildren(), $proposal, $compareWith);
			if($proposal->getSubjectArea() == NULL) {
				if(stripos($compareWith, $area->getName()) !== false) {
					$proposal = $proposal->setSubjectArea($area->getId());
					return $proposal->setSubjectCulture($area->getSubjectParent()->getId());
				}
			}
		}
		return $proposal;
	}

	private function getProposalWithSubject($subjects, Proposal $proposal, $compareWith) {
		foreach ($subjects as $subject) {
			if(stripos($compareWith, $subject->getName()) !== false) {
				$proposal = $proposal->setSubject($subject->getId());
				$proposal = $proposal->setSubjectArea($subject->getSubjectParent()->getId());
				return $proposal->setSubjectCulture($subject->getSubjectParent()->getSubjectParent()->getId());
			}
		}
		return $proposal;
	}
}

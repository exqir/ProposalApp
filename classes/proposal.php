<?php
require_once 'organization.php';

class Proposal
{
    private $id;
    private $typeId;
    private $title;
    private $description;
    private $organization;
    private $organizationOptional;
    private $orgOpt = 0;
    private $enddate;
    private $link;
    private $titleAdditions;
    private $subject_culture;
    private $subject_area;
    private $subject;

    public function __construct()
    {

    }

    public function setId($id)
    {
	    $this->id = $id;
    }

    public function getId()
    {
	    return $this->id;
    }

    public function setTypeId($typeId)
    {
	    $this->typeId = $typeId;
    }

    public function getTypeId()
    {
	    return $this->typeId;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setDescription(string $link)
    {
        $http = BASEURL . $link;
        $domDoc = new DomDocument();
		$domDoc->loadHTMLFile($http);
		$jwWrapper = Util::getParentElementByClass($domDoc, 'div', 'jw-wrapper');
        $this->description = trim($jwWrapper->nodeValue);
    }

    public function setDescriptionManually(string $desc) {
      $this->desc = $desc;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function getOrganization()
    {
        return $this->organization;
    }

    public function setOrganizationOptional(organization $organization)
    {
        $this->organizationOptional = $organization;
    }

    public function getOrganizationOptional()
    {
        return $this->organizationOptional;
    }

    public function setOrgOpt($int)
    {
	    $this->orgOpt = $int;
    }

    public function getOrgOpt()
    {
	    return $this->orgOpt;
    }

    public function setEnddate($enddate)
    {
	    $this->enddate = $enddate;
    }

    public function getEnddate()
    {
	    return $this->enddate;
    }

    public function setLink($link)
    {
	    $this->link = $link;
    }

    public function getLink()
    {
	    return $this->link;
    }

    public function setTitleAdditions(array $titleAdditions)
    {
	    $this->titleAdditions = $titleAdditions;
    }

    public function getTitleAdditions()
    {
	    return $this->titleAdditions;
    }

    public function setSubjectCulture($id)
    {
	    $this->subject_culture = $id;
    }

    public function getSubjectCulture()
    {
	    return $this->subject_culture;
    }

    public function setSubjectArea($id)
    {
	    $this->subject_area = $id;
    }

    public function getSubjectArea()
    {
	    return $this->subject_area;
    }

    public function setSubject($id)
    {
	    $this->subject = $id;
    }

    public function getSubject()
    {
	    return $this->subject;
    }

    public function setProposalByArray(array $array)
    {
        $this->setId($array["ID"]);
        $this->setTitle($array["Title"]);
        $this->description = $array["Description"];
        $this->titleAdditions = array();
        $this->titleAdditions["W1"] = $array["W1"];
        $this->titleAdditions["W2"] = $array["W2"];
        $this->titleAdditions["W3"] = $array["W3"];
        $this->titleAdditions["C1"] = $array["C1"];
        $this->titleAdditions["C2"] = $array["C2"];
        $this->titleAdditions["C3"] = $array["C3"];
        $this->titleAdditions["Tenure"]= $array["Tenure"];
        $this->titleAdditions["Ass"] = $array["Ass"];
        $this->raw = "2";
    }
}

?>

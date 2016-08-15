<?php
require_once 'organization.php';

class Proposal {
    private $id;
    private $typeId;
    private $title;
    private $description;
    private $organization;
    private $organizationId;
    private $organizationOptional;
    private $organizationOptionalId;
    private $orgOpt = 0;
    private $enddate;
    private $link;
    private $titleAdditions;
    private $subject_culture;
    private $subject_area;
    private $subject;

    public function __construct() {

    }

    public static function fromDOMElement($domElement) {
        $proposal = new self();
        $proposal = ProposalParser::getProposalFromDomElement($domElement,$proposal);
        return $proposal;
    }

    public function doesExistIn($connection) {
        $db = new ProposalSqlQueries($connection->getConnection());
        return $db->hasAnEntryFor($this);
    }

    public function getProposalWithEnrichedAttributes($connection, $link, $subjects) {
        $this->setDescription(ProposalParser::getDescriptionFromLink($link));
        //TODO subjects
        $proposal = ProposalParser::getProposalWithSubjects($subjects, $this);
        $proposal->setOrganization(
            $proposal->getOrganization()->getOrganizationWithEnrichedAttributes($connection));
        if($proposal->getOrganizationOptional() !== NULL) {
            //var_dump($proposal->getOrganizationOptional());
            $proposal->setOrganizationOptional(
                $proposal->getOrganizationOptional()->getOrganizationWithEnrichedAttrbutes($connection));
        }
        return $proposal;
    }

    public function setId($id) {
	    $this->id = $id;
    }

    public function getId() {
	    return $this->id;
    }

    public function setTypeId($typeId) {
	    $this->typeId = $typeId;
    }

    public function getTypeId() {
	    return $this->typeId;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setDescription($desc) {
        $this->desc = $desc;
    }

    public function setDescriptionManually(string $desc) {
      $this->desc = $desc;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setOrganization(Organization $organization) {
        $this->organization = $organization;
        return $this;
    }

    public function getOrganization() {
        return $this->organization;
    }

    public function setOrganizationId($id) {
        $this->organizationId = $id;
    }

    public function getOrganizationId() {
        return $this->organizationId;
    }

    public function setOrganizationOptional(organization $organization) {
        $this->organizationOptional = $organization;
        return $this;
    }

    public function getOrganizationOptional() {
        return $this->organizationOptional;
    }

    public function setOrganizationOptId($id) {
        $this->organizationOptionalId = $id;
    }

    public function getOrganizationOptId() {
        return $this->organizationOptionalId;
    }

    public function setOrgOpt($int) {
	    $this->orgOpt = $int;
    }

    public function getOrgOpt() {
	    return $this->orgOpt;
    }

    public function setEnddate($enddate) {
	    $this->enddate = $enddate;
        return $this;
    }

    public function getEnddate() {
	    return $this->enddate;
    }

    public function setLink($link) {
	    $this->link = $link;
        return $this;
    }

    public function getLink() {
	    return $this->link;
    }

    public function setTitleAdditions(array $titleAdditions) {
	    $this->titleAdditions = $titleAdditions;
        return $this;
    }

    public function getTitleAdditions() {
	    return $this->titleAdditions;
    }

    public function setSubjectCulture($id) {
	    $this->subject_culture = $id;
        return $this;
    }

    public function getSubjectCulture() {
	    return $this->subject_culture;
    }

    public function setSubjectArea($id) {
	    $this->subject_area = $id;
        return $this;
    }

    public function getSubjectArea() {
	    return $this->subject_area;
    }

    public function setSubject($id) {
	    $this->subject = $id;
        return $this;
    }

    public function getSubject() {
	    return $this->subject;
    }

    public function setProposalByArray(array $array) {
        $this->setId($array["ID"]);
        $this->setOrganizationId($array["OrgID"]);
        $this->setOrganizationOptId($array["OrgOptID"]);
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
        $this->setSubjectCulture($array["subject_culture"]);
        $this->setSubjectArea($array["subject_area"]);
        $this->setSubject($array["subject"]);
    }
}

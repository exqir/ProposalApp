<?php
require_once 'institut.php';

class Proposal
{
    private $id;
    private $typeId;
    private $title;
    private $description;
    private $institut;
    private $institutOptional;
    private $instOpt = 0;
    private $enddate;
    private $link;
    private $titleAdditions;

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

    public function getDescription()
    {
        return $this->description;
    }

    public function setInstitut(Institut $institut)
    {
        $this->institut = $institut;
    }

    public function getInstitut()
    {
        return $this->institut;
    }

    public function setInstitutOptional(Institut $institut)
    {
        $this->institutOptional = $institut;
    }

    public function getInstitutOptional()
    {
        return $this->institutOptional;
    }

    public function setInstOpt($int)
    {
	    $this->instOpt = $int;
    }

    public function getInstOpt()
    {
	    return $this->instOpt;
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

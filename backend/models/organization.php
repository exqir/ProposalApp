<?php
/**
*/
class Organization {
  	private $id;
  	private $typeId;
	private $name;
	private $abbrev;
	private $city;
	private $state = "";
	private $country = "";

	public function __construct($name) {
		$this->name = $name;
	}

	public function doesExistIn($connection) {
		$db = new OrganizationSqlQueries($connection->getConnection());
		return $db->hasAnEntryFor($this);
	}

	public function getOrganizationWithEnrichedAttributes($connection) {
		$db = new OrganizationSqlQueries($connection->getConnection());
		$id = $db->hasAnEntryFor($this);
		if($id > 0) {
			$this->setId();
			return $this;
		}
		else {
			$organization = $db->getOrganizationWithTypeAndAbbrev($this);
			$ls = new LocationService();
			return $ls->getOrganizationWithStateAndCountry($organization);
		}
	}

	public function setId($id) {
	    $this->id = $id;
    }

    public function getId() {
	    return $this->id;
    }

    public function setTypeId($typeId) {
	    $this->typeId = $typeId;
		return $this;
    }

    public function getTypeId() {
	    return $this->typeId;
    }

	public function setName($name) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function setAbbrev($abbrev) {
		$this->abbrev = $abbrev;
		return $this;
	}

	public function getAbbrev() {
		return $this->abbrev;
	}

	public function setCity($city) {
		$this->city = $city;
		return $this;
	}

	public function getCity() {
		return $this->city;
	}

	public function setState($state) {
		$this->state = $state;
		return $this;
	}

	public function getState() {
		return $this->state;
	}

	public function setCountry($country) {
		$this->country = $country;
		return $this;
	}

	public function getCountry() {
		return $this->country;
	}

  	public function setOrganizationByArray(array $array) {
		$this->setId($array["ID"]);
		$this->setTypeId($array["TypeID"]);
		$this->setAbbrev($array["Abbrev"]);
		$this->setCity($array["City"]);
		$this->setState($array["State"]);
		$this->setCountry($array["Country"]);
	}
}

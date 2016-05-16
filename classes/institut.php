<?php
//require_once 'type.php';
/**
* Institut Object
*/
class Institut
{
    private $id;
    private $typeId;
	private $name;
	private $abbrev;
	private $city;
	private $state = "";
	private $country = "";
	private $type;

	public function __construct($name)
	{
		$this->name = $name;
		//$this->city = $city;
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

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setAbbrev($abbrev)
	{
		$this->abbrev = $abbrev;
	}

	public function getAbbrev()
	{
		return $this->abbrev;
	}

	public function setCity($city)
	{
		$this->city = $city;
	}

	public function getCity()
	{
		return $this->city;
	}

	public function setState($state)
	{
		$this->state = $state;
	}

	public function getState()
	{
		return $this->state;
	}

	public function setCountry($country)
	{
		$this->country = $country;
	}

	public function getCountry()
	{
		return $this->country;
	}

	public function setInstType(Type $type)
	{
		$this->type = $type;
	}

	public function getInstType()
	{
		return $this->type;
	}
}

?>

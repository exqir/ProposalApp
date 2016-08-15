<?php

class SubjectGroup {
  private $subjectChildren = [];
  private $name;
  private $id;

  public function __construct($name) {
    $this->name = $name;
  }

  public static function fromXPath($xpath, $connection) {
    $ssq = new SubjectSqlQueries($connection->getConnection());
    return $ssq->getSubjectsWithId(SubjectParser::getSubjectsFromXPath($xpath));
  }

  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    $this->id = $id;
    return $this;
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  public function addSubjectChildren($child) {
    $this->subjectChildren[] = $child;
  }

  public function setSubjectChildren($children) {
    $this->subjectChildren = $children;
    return $this;
  }

  public function getSubjectChildren() {
    return $this->subjectChildren;
  }
}

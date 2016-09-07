<?php

require_once 'SubjectSqlQueries.php';
require_once 'SubjectParser.php';
require_once __DIR__ . '/../db/SqlConnection.php';

class SubjectGroup {
  private $subjectChildren = [];
  private $subjectParent;
  private $name;
  private $id;

  public function __construct($name) {
    $this->name = $name;
  }

  public static function fromXPath(\DOMXPath $xpath, SqlConnection $connection) {
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

  public function getSubjectParent() {
      return $this->subjectParent;
  }

  public function setSubjectParent($parent) {
      $this->subjectParent = $parent;
      return $this;
  }
}

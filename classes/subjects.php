<?php

class SubjectGroup {
  private $subjectChildren = [];
  private $name;

  public function __construct($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function setName() {
    $this->name = $name;
  }

  public function addSubjectChildren($child) {
    $this->subjectChildren[] = $child;
  }

  public function getSubjectGroup() {
   return $this->subjectChildren;
  }
}
?>

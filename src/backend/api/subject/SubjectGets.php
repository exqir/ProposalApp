<?php

require_once __DIR__ . '/../../db/SqlConnection.php';

class SubjectGets extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function getSubjectCultures() {
        return $this->selectArrayQuery("SELECT ID, Name FROM subject_culture");
    }

    public function getSubjectAreas() {
        return $this->selectArrayQuery("SELECT ID, Name, ParentID FROM subject_area");
    }

    public function getSubjects() {
        return $this->selectArrayQuery("SELECT ID, Name, ParentID FROM subject");
    }
}
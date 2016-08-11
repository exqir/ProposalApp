<?php

class SubjectSqlQueries extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function getSubjectsWithId($subjects) {
        array_map(function($cultures) {
            $this->getCultureWithId($cultures);
        },$subjects);
    }

    private function has(SubjectGroup $subject, $tableName) {
        $query = SELECT ID FROM $table WHERE Name = ?;
        $this->sqlQuery("SELECT ID FROM $table WHERE Name = ?", array($subject->getName()),'getSubjectId',$subject);
    }

    protected function getSubjectId($stmt) {
        $stmt->store_result();
        $stmt->bind_result($id);
        if($stmt->num_rows === 0) {
            return 0; //Found no subject with the given name
        } else {
            $stmt->fetch();
            return $id;
        }
    }

    private function getCultureWithId($cultures) {
        $query = "INSERT INTO subject_culture (Name) VALUES (?)";
    }


}
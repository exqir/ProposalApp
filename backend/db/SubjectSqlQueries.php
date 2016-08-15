<?php

class SubjectSqlQueries extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function getSubjectsWithId($subjects) {
        return array_map(function($culture) {
            return $this->getCultureWithId($culture);
        },$subjects);
    }

    private function has(SubjectGroup $subject, $tableName) {
        //$query = "SELECT ID FROM subject_culture WHERE Name = ?";
        $query = "SELECT ID FROM " . $tableName . " WHERE Name = ?";
        return $this->sqlQuery($query, array($subject->getName()),'getSubjectId', $subject);
    }

    protected function getSubjectId($stmt, $subject) {
        $stmt->store_result();
        $stmt->bind_result($id);
        if($stmt->num_rows === 0) {
            return 0; //Found no subject with the given name
        } else {
            $stmt->fetch();
            return $id;
        }
    }

    protected function getInsertId($stmt, $culture) {
        return $this->mysqli->insert_id;
    }

    private function getCultureWithId(SubjectGroup $culture) {
        $cultureId = $this->has($culture, "subject_culture");
        if($cultureId <= 0) $cultureId = $this->sqlQuery("INSERT INTO subject_culture (Name) VALUES (?)", array($culture->getName()),'getInsertId', $culture);
        $culture->setId($cultureId);
        return $this->getSubjectGroupeWithSubjectChildren($culture->getSubjectChildren(), $cultureId);
    }

    private function getAreaWithId($area, $parentId) {
        $areaId = $this->has($area, "subject_area");
        if($areaId <= 0) $areaId = $this->sqlQuery("INSERT INTO subject_area (Name, ParentID) VALUES (?,?)", array($area->getName(),$parentId),'getInsertId', $area);
        $area->setId($areaId);
        return $this->getSubjectGroupeWithSubjectChildren($area->getSubjectChildren(), $areaId);
    }

    private function getSubjectWithId($subject, $parentId) {
        $subjectId = $this->has($subject, "subject");
        if($subjectId <= 0) $subjectId = $this->sqlQuery("INSERT INTO subject (Name, ParentID) VALUES (?,?)", array($subject->getName(),$parentId),'getInsertId',$subject);
        $subject->setId($subjectId);
        return $subject;
    }

    private function getSubjectGroupeWithSubjectChildren($subjectGroup, $parentId) {
        return array_map(function($subject) use ($parentId) {
            if($subject->getSubjectChildren() === NULL )return $this->getSubjectWithId($subject, $parentId);
            else return $this->getAreaWithId($subject, $parentId);
        }, $subjectGroup);
    }
}
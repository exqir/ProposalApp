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
        $culture = $culture->setId($cultureId);
        //var_dump($culture);
        return $culture->setSubjectChildren($this->getSubjectGroupeWithSubjectChildren($culture->getSubjectChildren(), $culture));
    }

    private function getAreaWithId(SubjectGroup $area, $parent) {
        $areaId = $this->has($area, "subject_area");
        if($areaId <= 0) $areaId = $this->sqlQuery("INSERT INTO subject_area (Name, ParentID) VALUES (?,?)", array($area->getName(),$parent->getId()),'getInsertId', $area);
        $area = $area->setId($areaId);
        $area = $area->setSubjectParent($parent);
        return $area->setSubjectChildren($this->getSubjectGroupeWithSubjectChildren($area->getSubjectChildren(), $area));
    }

    private function getSubjectWithId($subject, $parent) {
        $subjectId = $this->has($subject, "subject");
        if($subjectId <= 0) $subjectId = $this->sqlQuery("INSERT INTO subject (Name, ParentID) VALUES (?,?)", array($subject->getName(),$parent->getId()),'getInsertId',$subject);
        $subject = $subject->setId($subjectId);
        $subject = $subject->setSubjectParent($parent);
        return $subject;
    }

    private function getSubjectGroupeWithSubjectChildren($subjectGroup, $parent) {
        return array_map(function($subject) use ($parent) {
            if(count($subject->getSubjectChildren()) === 0 )return $this->getSubjectWithId($subject, $parent);
            else return $this->getAreaWithId($subject, $parent);
        }, $subjectGroup);
    }
}
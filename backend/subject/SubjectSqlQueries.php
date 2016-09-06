<?php
namespace ProposalApp;

require_once __DIR__ . '/../db/SqlConnection.php';

use mysqli_stmt;

class SubjectSqlQueries extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function getSubjectsWithId(Array $subjects) {
        return array_map(function($culture) {
            return $this->getCultureWithId($culture);
        },$subjects);
    }

    private function has(SubjectGroup $subject, $tableName) {
        $query = "SELECT ID FROM " . $tableName . " WHERE Name = ?";
        return $this->sqlQuery($query, array($subject->getName()),'getSubjectId', $subject);
    }

    protected function getSubjectId(mysqli_stmt $stmt, $subject) {
        $stmt->store_result();
        $stmt->bind_result($id);
        if($stmt->num_rows === 0) {
            return 0; //Found no subject with the given name
        } else {
            $stmt->fetch();
            return $id;
        }
    }

    protected function getInsertId(mysqli_stmt $stmt, $culture) {
        return $this->mysqli->insert_id;
    }

    private function getCultureWithId(SubjectGroup $culture) {
        $cultureId = $this->has($culture, "subject_culture");
        if($cultureId <= 0) $cultureId = $this->sqlQuery("INSERT INTO subject_culture (Name) VALUES (?)", array($culture->getName()),'getInsertId', $culture);
        $culture = $culture->setId($cultureId);
        return $culture->setSubjectChildren($this->getSubjectGroupWithSubjectChildren($culture->getSubjectChildren(), $culture));
    }

    private function getAreaWithId(SubjectGroup $area, SubjectGroup $parent) {
        $areaId = $this->has($area, "subject_area");
        if($areaId <= 0) $areaId = $this->sqlQuery("INSERT INTO subject_area (Name, ParentID) VALUES (?,?)", array($area->getName(),$parent->getId()),'getInsertId', $area);
        $area = $area->setId($areaId);
        $area = $area->setSubjectParent($parent);
        return $area->setSubjectChildren($this->getSubjectGroupWithSubjectChildren($area->getSubjectChildren(), $area));
    }

    private function getSubjectWithId(SubjectGroup $subject, SubjectGroup $parent) {
        $subjectId = $this->has($subject, "subject");
        if($subjectId <= 0) $subjectId = $this->sqlQuery("INSERT INTO subject (Name, ParentID) VALUES (?,?)", array($subject->getName(),$parent->getId()),'getInsertId',$subject);
        $subject = $subject->setId($subjectId);
        $subject = $subject->setSubjectParent($parent);
        return $subject;
    }

    private function getSubjectGroupWithSubjectChildren(Array $subjectGroup, SubjectGroup $parent) {
        return array_map(function($subject) use ($parent) {
            if(count($subject->getSubjectChildren()) === 0 )return $this->getSubjectWithId($subject, $parent);
            else return $this->getAreaWithId($subject, $parent);
        }, $subjectGroup);
    }
}
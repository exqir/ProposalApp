<?php

class OrganizationSqlQueries extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function hasAnEntryFor(Organization $organization) {
        $query = "SELECT ID, AliasOf FROM organizations WHERE Name = ?";
        return $this->sqlQuery($query,array($organization->getName()),'existsInDB',$organization);
    }

    public function getOrganizationWithTypeAndAbbrev(Organization $organization) {
        $organization = $this->sqlQuery("SELECT TypeID, Keyword, Strict FROM keywords",NULL,'getOrganizationWithType',$organization);
        return $this->sqlQuery("SELECT Abbrev FROM types WHERE ID=?",array($organization->getTypeId()),'getOrganizationWithAbbrev', $organization);
    }

    protected function existsInDB($stmt, Organization $organization) {
        $stmt->store_result();
        //$stmt->bind_result($orgId,$orgOptId);
        if($stmt->num_rows === 0) {
            return 0; // Found no organization with the given name
        } else if($stmt->num_rows === 1 ){
            return $this->getOrganizationIdFromDB($stmt); // Found an organization with the given name, returning it's id
        } else return -1; // No unique organization identified
    }

    private function getOrganizationIdFromDB($stmt) {
        $stmt->bind_result($id,$alias);
        $stmt->fetch();
        if($alias !== NULL) return $alias; // Organization is an alias, returning the id of the main organization
        else return $id;
    }

    protected function getOrganizationWithType($stmt, Organization $organization) {
        $stmt->store_result();
        $stmt->bind_result($typeId,$keyword,$strict);
        $type = 0;
        while($stmt->fetch()) {
            if($strict) {
                //Compares the name and keyword stricly per regex, only when keyword is a single word it matches
                if(preg_match("/\b" . preg_quote($keyword, '/') . "\b/i", $name)) $type = $typeId;
            } else {
                //Compares the name name and keyword, keyword can also be a substring of name
                if(stripos($name,$keyword) !== false) $type = $typeId;
            }
        }
        $stmt->close(); //TODO adding close to other functions
        return $organization->setTypeId($type);
    }

    protected function getOrganizationWithAbbrev($stmt, Organization $organization) {
        $stmt->store_result();
        $stmt->bind_result($abbrev);
        $stmt->fetch();
        if($stmt->num_rows !== 1) return null;
        else return $organization->setAbbrev($abbrev);
    }
}
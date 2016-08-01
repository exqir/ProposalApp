<?php

class OrganizationSqlQueries extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function hasAnEntryFor($organization) {
        $query = "SELECT ID, AliasOf FROM organizations WHERE Name = ?";
        return $this->sqlQuery($query,array($organization->getName()),'existsInDB',$organization);
    }

    protected function existsInDB($stmt, $organization) {
        $stmt->store_result();
        //$stmt->bind_result($orgId,$orgOptId);
        if($stmt->num_rows === 0) {
            return 0; // Found no organizatoin with the given name
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
}
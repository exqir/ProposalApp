<?php

require_once __DIR__ . '/../../db/SqlConnection.php';

class OrganizationStatistics extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function getOrganizations() {
        return $this->selectArrayQuery("SELECT * FROM organizations ORDER BY Name");
    }

    public function getUsedOrganizations() {
        return $this->selectArrayQuery("SELECT DISTINCT organizations.Name FROM proposal 
          LEFT JOIN organizations 
            ON proposal.OrgID = organizations.ID 
            OR organizations.ID = proposal.OrgOptID
          ORDER BY organizations.Name
         ");
    }

    public function getOrganizationTypes() {
        return $this->selectArrayQuery("SELECT ID, Abbrev FROM types ORDER BY ID");
    }

    public function getStates() {
        return $this->selectArrayQuery("SELECT DISTINCT State FROM organizations ORDER BY State");
    }

    public function getUsedStates() {
        return $this->selectArrayQuery("SELECT DISTINCT organizations.State FROM proposal 
          LEFT JOIN organizations 
            ON proposal.OrgID = organizations.ID 
            OR organizations.ID = proposal.OrgOptID
          ORDER BY organizations.State
         ");
    }
}
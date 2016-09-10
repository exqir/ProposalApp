<?php

require_once __DIR__ . '/../../db/SqlConnection.php';

class OrganizationStatistics extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function getOrganizationCount() {
        return $this->selectQuery("SELECT COUNT(*) AS number FROM organizations");
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

    public function getOrganizationTypeCount() {
        return $this->selectQuery("SELECT 
	      SUM(CASE WHEN organizations.TypeID = 1 OR organizations.TypeID = 5 THEN 1 ELSE 0 END) AS U,
          SUM(CASE WHEN organizations.TypeID = 2 THEN 1 ELSE 0 END) AS FH,
          SUM(CASE WHEN organizations.TypeID = 3 THEN 1 ELSE 0 END) AS UK,
          SUM(CASE WHEN organizations.TypeID = 4 THEN 1 ELSE 0 END) AS PH,
          SUM(CASE WHEN organizations.TypeID = 6 THEN 1 ELSE 0 END) AS AK,
          SUM(CASE WHEN organizations.TypeID = 7 THEN 1 ELSE 0 END) AS FZ
          FROM proposal
          LEFT JOIN organizations
            ON proposal.OrgID = organizations.ID
        ");
    }

    public function getStates() {
        return $this->selectArrayQuery("SELECT DISTINCT State FROM organizations WHERE State != '' ORDER BY State");
    }

    public function getUsedStates() {
        return $this->selectArrayQuery("SELECT DISTINCT organizations.State FROM proposal 
          LEFT JOIN organizations 
            ON proposal.OrgID = organizations.ID 
            OR organizations.ID = proposal.OrgOptID
          WHERE organizations.State != ''
          ORDER BY organizations.State
         ");
    }

    public function getUsedStatesByCountry($country) {
        $query = "SELECT DISTINCT organizations.State FROM proposal 
          LEFT JOIN organizations 
            ON proposal.OrgID = organizations.ID 
            OR organizations.ID = proposal.OrgOptID
          WHERE organizations.Country = '" . $country . "' 
          AND organizations.State != ''
          ORDER BY organizations.State";
        return $this->selectArrayQuery($query);
    }

    public function getUsedStatesCountByCountry($country) {
        $stateRows = $this->getUsedStatesByCountry($country);
        $query = $this->createQueryUsedStatesCountByCountry($stateRows);
        $stateCounts = $this->selectQuery($query);
        return $this->combineStateNamesAndNumbers($stateRows, $stateCounts);
    }

    private function createQueryUsedStatesCountByCountry($stateRows) {
        $query = "SELECT ";
        for($i = 0, $statesLength = count($stateRows); $i < $statesLength; $i++) {
            $row = $stateRows[$i];
            $query .= "SUM(CASE WHEN organizations.State = '" . $row["State"] . "' THEN 1 ELSE 0 END) AS S" . $i . "";
            if($i !== $statesLength - 1 ) $query .= ", ";
        }
        $query .= " FROM proposal
            LEFT JOIN organizations
                ON proposal.OrgID = organizations.ID";
        return $query;
    }

    private function combineStateNamesAndNumbers($stateRows, $stateCounts) {
        $states = [];
        for($i = 0, $statesLength = count($stateRows); $i < $statesLength; $i++) {
            $stateName = $stateRows[$i]["State"];
            $states[$stateName] = $stateCounts["S" . $i];
        }
        return $states;
    }
}
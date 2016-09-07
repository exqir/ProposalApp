<?php

require_once __DIR__ . '/../../db/SqlConnection.php';

class ProposalStatistics extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function getProposals() {
        return $this->selectArrayQuery("SELECT * FROM proposal");
    }

    public function getProposalsByCountry($country) {
        $query = "SELECT * FROM proposal 
          LEFT JOIN organizations
          ON proposal.OrgID = organizations.ID
          OR proposal.OrgOptID = organizations.ID
          WHERE organizations.Country = '" . $country . "'";
        return $this->selectArrayQuery($query);
    }
}
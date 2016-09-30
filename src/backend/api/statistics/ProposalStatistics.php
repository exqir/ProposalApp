<?php

require_once __DIR__ . '/../../db/SqlConnection.php';

class ProposalStatistics extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function getProposalCount() {
        return $this->selectQuery("SELECT COUNT(*) AS number FROM proposal WHERE RAW != -1");
    }

    public function getProposalCountByCountry($country) {
        $query = "SELECT COUNT(*) AS number FROM proposal 
          LEFT JOIN organizations
          ON proposal.OrgID = organizations.ID
          OR proposal.OrgOptID = organizations.ID
          WHERE organizations.Country = '" . $country . "' AND proposal.Raw != -1";
        return $this->selectQuery($query);
    }

    public function getProposalTypeCount() {
        return $this->selectQuery("SELECT
	      SUM(CASE WHEN W1 != 0 THEN 1 ELSE 0 END) AS W1,
          SUM(CASE WHEN W2 != 0 THEN 1 ELSE 0 END) AS W2,
          SUM(CASE WHEN W3 != 0 THEN 1 ELSE 0 END) AS W3
          FROM proposal
          WHERE Raw != -1
        ");
    }
}
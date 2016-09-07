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
        return $this->selectArrayQuery("SELECT * FROM organizations ORDER BY Name");
    }

    public function getOrganizationTypes() {
        return $this->selectArrayQuery("SELECT ID, Abbrev FROM types ORDER BY ID");
    }
}
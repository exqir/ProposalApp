<?php

require_once __DIR__ . '/../../db/SqlConnection.php';

class OrganizationGets extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function getOrganizations() {
        return $this->selectArrayQuery("SELECT * FROM organizations WHERE AliasOf IS NULL");
    }

    public function getOrganization($id) {
        $query = "SELECT * FROM organizations WHERE organizations.ID = " . $id ."";
        return $this->selectArrayQuery($query);
    }

    public function getAliasOfOrganization($id) {
        $query = "SELECT * FROM organizations WHERE organizations.AliasOf = " . $id ."";
        return $this->selectArrayQuery($query);
    }
}
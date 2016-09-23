<?php

require_once __DIR__ . '/../../db/SqlConnection.php';

class OrganizationGets extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function getOrganizations() {
        return $this->selectArrayQuery("SELECT organizations.*, types.Abbrev FROM organizations
        LEFT JOIN types
          ON organizations.TypeID = types.ID
        WHERE AliasOf IS NULL");
    }

    public function getOrganization($id) {
        $query = "SELECT organizations.*, types.Abbrev FROM organizations
        LEFT JOIN types
          ON organizations.TypeID = types.ID
        WHERE organizations.ID = " . $id ."";
        return $this->selectQuery($query);
    }

    public function getAliasOfOrganization($id) {
        $query = "SELECT organizations.*, types.Abbrev FROM organizations
        LEFT JOIN types
          ON organizations.TypeID = types.ID
        WHERE organizations.AliasOf = " . $id ."";
        return $this->selectArrayQuery($query);
    }
}
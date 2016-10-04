<?php

require_once __DIR__ . '/../../db/SqlConnection.php';
require_once __DIR__ . '/../../organization/Organization.php';

class OrganizationPuts extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function putOrganization($payload) {
        $organization = new Organization($payload["Name"]);
        $organization = $organization->setOrganizationByArray($payload);
        $query =
            "UPDATE organizations SET
			TypeID = ?,
			Name = ?,
			City = ?,
			State = ?,
			Country = ?
			WHERE id = ?";
        $params = array(
            $organization->getTypeId(),
            $organization->getName(),
            $organization->getCity(),
            $organization->getState(),
            $organization->getCountry(),
            $organization->getId()
        );
        return $this->sqlQuery($query, $params,'put', NULL);
    }

    public function mergeOrangization($firstId,$secondId) {
        $res1 = $this->switchValue($firstId,$secondId,"proposal","OrgID","i");
        $res2 = $this->switchValue($firstId,$secondId,"proposal","OrgOptID","i");
        //TODO change to setAlias($firstId,$secondId)
        //setting $firstId as AliasOf for $secondId
        $res3 = $this->setAliasOf($secondId,$firstId);
        if($res1 === 1 && $res2 === 1 && $res3 === 1) return 1;
        else return 0;
    }

    private function switchValue($newValue,$oldValue,$table,$columName) {
        $query = "UPDATE $table SET $columName = ? WHERE $columName = ? ";
        $this->sqlQuery($query, array($newValue, $oldValue),'put', NULL);
    }

    private function setAliasOf($id,$alias) {
        $query = "UPDATE organizations SET AliasOf = ? WHERE ID = ?";
        $this->sqlQuery($query, array($alias, $id),'put', NULL);
    }
}
<?php

class OrganizationSqlQueries {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function existsIn($mysqli,$organization) {
        $name = $organization->getName();
        $query = "SELECT ID, TypeID, AliasOf FROM organizations WHERE Name = ?";
        if($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param("s",$name);
            if($stmt->execute()) {
                $stmt->store_result();
                $stmt->bind_result($id,$typeId,$aliasId);
                if($stmt->num_rows === 0) {
                    //Found no organization with the given name
                    $res = 0;
                } else if($stmt->num_rows === 1){
                    //Found a organization with the given name
                    $stmt->fetch();
                    if($aliasId !== NULL) {
                        //Found organization is an alias of another organization
                        //Sets the id to the id of the main organization
                        //TODO Change name to the name of the main organization
                        echo "WRONG TURN" . $id . " <br />";
                        $organization->setId($aliasId);
                        $organization->setTypeId($typeId);
                        $res = 1;
                    } else {
                        //Found organization is the main organization
                        echo "Setting ID to: " . $id . "<br />";
                        $organization->setId($id);
                        $organization->setTypeId($typeId);
                        $res = 1;
                    }
                } else $res = -3;
                $stmt->close();
                echo "checkOrganizationExistance Intern: " . $res . "<br />";
                echo "checkIntern ID: " . $organization->getId() . "<br />";
                return $res;
            } else return -2;
        } else {
            printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
            return -1;
        }
    }
}
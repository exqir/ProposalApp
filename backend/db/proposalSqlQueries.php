<?php

class ProposalSqlQueries extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    private function existsInDB($stmt, $proposal) {
        $stmt->store_result();
        $stmt->bind_result($orgId,$orgOptId);
        if($stmt->num_rows === 0) {
            return 0; // Found no proposal with the given title and enddate
        } else {
            $this->existsWithOrganization($stmt,$proposal); // Found a proposal with the given title and enddate, checking if organization matches also
        }
    }

    private function existsWithOrganization($stmt, $proposal) {
        $stmt->bind_result($orgId,$orgOptId);
        $stmt->fetch();
        if($proposal->getOptionalOrganization() !== NULL) {
            if ($proposal->getOptionalOrganization()->getId() === $orgOptId && $proposal->getOrganization()->getId() === $orgId)
                return 1; // Both Organizations match, it's the same proposal
            else return 0; // It's not the same proposal
        } else {
            if($proposal->getOrganization()->getId() === $orgId) return 1; // The organization matches, it's the same proposal
            else {
                echo "hello"; //TODO Testing! Flow does not end here because no entries with matching title and enddate found
                return 0;
            } // It's not the same organizations, so it's not the same proposal
        }
    }

    public function hasAnEntryFor($proposal){
        //$title = $proposal->getTitle();
        //$organization = $proposal->getOrganization();
        //$enddate = $proposal->getEnddate();

        $query = "SELECT `OrgID`, `OrgOptID` FROM proposal WHERE Title = ? AND Enddate = ?";
        $this->sqlQuery($query,array($proposal->getTitle(),$proposal->getEnddate()),'existsInDB',$proposal);

//        if($stmt = $this->mysqli->prepare($query)) {
//            $stmt->bind_param("ss",$title,$enddate);
//            if($stmt->execute()) {
//                $stmt->store_result();
//                $stmt->bind_result($orgId,$orgOptId);
//                if($stmt->num_rows === 0) {
//                    //Found no proposal with the given title and enddate
//                    echo "No such proposal found</br>";
//                    $res = 0;
//                } else {
//                    //Found a proposal with the given title and enddate
//                    $stmt->fetch();
//                    echo "Found such proposal, checking Organization: ";
//                    $res = -3;
//                    if($proposal->getOrgOpt() === 1) {
//                        //The parsed propsal has an optional organization
//                        echo "Optional organization found:";
//                        if($this->checkProposalOrganizationConnection($orgId,$organization->getName()) === 1
//                            && $this->checkProposalOrganizationConnection($orgOptId,$organization->getName()) === 1){
//                            //Both organizations match, it's the same proposal
//                            $res = 1;
//                        }
//                    } else {
//                        //The parsed proposal has only one organization
//                        echo "No optional organization found: ";
//                        echo "<br />OrgID: " . $orgId . "<br />";
//                        if($this->checkProposalOrganizationConnection($orgId,$organization->getName()) === 1) {
//                            //The organization matches, it's the same proposal
//                            $res = 1;
//                        }
//                    }
//                }
//                echo "CheckingProposalExistance: " . $res . "</br>";
//                $stmt->close();
//                return $res;
//            } else return -2;
//        } else {
//            printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
//            return -1;
//        }
    }

//    public function hasAnEntryOf($proposal){
//        $title = $proposal->getTitle();
//        $organization = $proposal->getOrganization();
//        $enddate = $proposal->getEnddate();
//        $query = "SELECT `OrgID`, `OrgOptID` FROM proposal WHERE Title = ? AND Enddate = ?";
//        if($stmt = $this->mysqli->prepare($query)) {
//            $stmt->bind_param("ss",$title,$enddate);
//            if($stmt->execute()) {
//                $stmt->store_result();
//                $stmt->bind_result($orgId,$orgOptId);
//                if($stmt->num_rows === 0) {
//                    //Found no proposal with the given title and enddate
//                    echo "No such proposal found</br>";
//                    $res = 0;
//                } else {
//                    //Found a proposal with the given title and enddate
//                    $stmt->fetch();
//                    echo "Found such proposal, checking Organization: ";
//                    $res = -3;
//                    if($proposal->getOrgOpt() === 1) {
//                        //The parsed propsal has an optional organization
//                        echo "Optional organization found:";
//                        if($this->checkProposalOrganizationConnection($orgId,$organization->getName()) === 1
//                            && $this->checkProposalOrganizationConnection($orgOptId,$organization->getName()) === 1){
//                            //Both organizations match, it's the same proposal
//                            $res = 1;
//                        }
//                    } else {
//                        //The parsed proposal has only one organization
//                        echo "No optional organization found: ";
//                        echo "<br />OrgID: " . $orgId . "<br />";
//                        if($this->checkProposalOrganizationConnection($orgId,$organization->getName()) === 1) {
//                            //The organization matches, it's the same proposal
//                            $res = 1;
//                        }
//                    }
//                }
//                echo "CheckingProposalExistance: " . $res . "</br>";
//                $stmt->close();
//                return $res;
//            } else return -2;
//        } else {
//            printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
//            return -1;
//        }
//    }
}
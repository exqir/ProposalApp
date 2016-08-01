<?php

class ProposalSqlQueries extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function hasAnEntryFor($proposal) {
        //$proposal->setTitle("Assistant (tenure track) / Associate Professorships in Electrical and Computer Engineering");
        //$proposal->setEnddate("2016-04-22");
        //$proposal->getOrganization()->setName("Aarhus University");

        $query = "SELECT `OrgID`, `OrgOptID` FROM proposal WHERE Title = ? AND Enddate = ?";
        return $this->sqlQuery($query, array($proposal->getTitle(), $proposal->getEnddate()), 'existsInDB', $proposal);
    }

    protected function existsInDB($stmt, $proposal) {
        $stmt->store_result();
        if($stmt->num_rows === 0) {
            return 0; // Found no proposal with the given title and enddate
        } else {
            return $this->existsWithOrganization($stmt,$proposal); // Found a proposal with the given title and enddate, checking if organization matches also
        }
    }

    private function existsWithOrganization($stmt, $proposal) {
        $stmt->bind_result($orgId,$orgOptId);
        $stmt->fetch();
        if($proposal->getOrganizationOptional() !== NULL) {
            if ($this->compareOrganizationWithId($proposal->getOrganizationOptional(),$orgOptId) &&
                $this->compareOrganizationWithId($proposal->getOrganization(), $orgId))
                return 1; // Both Organizations match, it's the same proposal
            else return 0; // It's not the same proposal
        } else {
            if($this->compareOrganizationWithId($proposal->getOrganization(),$orgId)) return 1; // The organization matches, it's the same proposal
            else {
                echo "<br>Not the same proposal <br>"; //TODO Testing! Flow does not end here because no entries with matching title and enddate found
                return 0;
            } // It's not the same organizations, so it's not the same proposal
        }
    }

    private function compareOrganizationWithId(Organization $organization,$id) {
        $dbId = $organization->doesExistIn($this);
        if($dbId > 0 && $dbId === $id) return true;
        else return false;
    }
}
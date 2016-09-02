<?php
namespace ProposalApp\Proposal;

require_once __DIR__ . '/../db/SqlConnection.php';
require_once __DIR__ . '/../organization/Organization.php';

use ProposalApp\db\SqlConnection;
use ProposalApp\Organization\Organization;

class ProposalSqlQueries extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function hasAnEntryFor(Proposal $proposal) {
        //$proposal->setTitle("Assistant (tenure track) / Associate Professorships in Electrical and Computer Engineering");
        //$proposal->setEnddate("2016-04-22");
        //$proposal->getOrganization()->setName("Aarhus University");

        $query = "SELECT `OrgID`, `OrgOptID` FROM proposal WHERE Title = ? AND Enddate = ?";
        return $this->sqlQuery($query, array($proposal->getTitle(), $proposal->getEnddate()), 'existsInDB', $proposal);
    }

    public function save(Proposal $proposal) {
        $colums = "(";
        foreach(array_keys(DB_PROPOSAL) as $key) {
            if($key === end(array_keys(DB_PROPOSAL))) $colums .= $key;
            else $colums .= $key . ", ";
        }
        $colums .= ")";
        $values = "(";
        $i = 0;
        foreach(DB_PROPOSAL as $value) {
            if(++$i === count(DB_PROPOSAL)) $values .= $value;
            else $values .= $value . ", ";
        }
        $values .= ")";
        $query = "INSERT INTO proposal " . $colums . " VALUES " . $values . "";
        $id = $this->sqlQuery($query,Config::getParam($proposal),'getInsertId',$proposal);
        return $proposal->setId($id);
    }

    protected function existsInDB($stmt, $proposal) {
        $stmt->store_result();
        if($stmt->num_rows === 0) {
            return 0; // Found no proposal with the given title and enddate
        } else {
            return $this->existsWithOrganization($stmt,$proposal); // Found a proposal with the given title and enddate, checking if organization matches also
        }
    }

    private function existsWithOrganization($stmt, Proposal $proposal) {
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

    private function getColumesAsString() {
        $s = implode(',', $this->getColumes());
        return '(' . $s . ')';
    }

    private function getColumes() {
        return array(
            "OrgID",
            "TypeID",
			"OrgOptID",
			"TypeOptID",
			"Title",
			"Description",
			"Catchword",
			"Faculty",
			"Section",
			"LID",
			"SID",
			"SSID",
			"Current",
			"Raw",
			"Ass",
			"W1",
			"W2",
			"W3",
			"C1",
			"C2",
			"C3",
			"C4",
			"Found",
			"Tenure",
			"Note",
			"Enddate",
			"ASAP",
			"Publisher1",
			"Pdate1",
			"Pissue1",
			"Pyear1",
			"Publisher2",
			"Pdate2",
			"Pissue2",
			"Pyear2",
			"Publisher3",
			"Pdate3",
			"Pissue3",
			"Pyear3",
			"Publisher4",
			"Pdate4",
			"Pissue4",
			"Pyear4",
			"Link",
			"SaveTime",
			"subject_culture",
			"subject_area",
			"subject"
        );
    }
}
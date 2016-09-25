<?php

require_once __DIR__ . '/../../db/SqlConnection.php';

class ProposalGets extends SqlConnection {
    protected $mysqli;

    public function __construct($db) {
        $this->mysqli = $db;
    }

    public function getProposals() {
        return $this->selectArrayQuery("SELECT proposal.ID,
            proposal.OrgID,
            proposal.OrgOptID,
            proposal.Title,
            proposal.Link,
            proposal.Raw,
            proposal.Ass,
            proposal.W1,
            proposal.W2,
            proposal.W3,
            proposal.C1,
            proposal.C2,
            proposal.C3,
            proposal.Tenure,
            proposal.Enddate,
            proposal.SaveTime,
            proposal.subject_culture,
            proposal.subject_area,
            proposal.subject,
            organizations.Name AS orgName, 
            organizations.State AS State, 
            organizations.Country AS Country,
            types.Abbrev as orgAbbrev,
            subject_culture.Name AS Culture,
            subject_area.Name AS Area,
            subject.Name AS SubjectName
			FROM proposal
			INNER JOIN organizations
				ON proposal.OrgID = organizations.ID
            LEFT JOIN types
            	ON organizations.TypeID = types.ID
            LEFT OUTER JOIN subject_culture
      	        ON proposal.subject_culture = subject_culture.ID
            LEFT OUTER JOIN subject_area
      	        ON proposal.subject_area = subject_area.ID
            LEFT OUTER JOIN subject
      	        ON proposal.subject = subject.ID
			ORDER BY id
			DESC");
    }

    public function getProposal($id) {
        $query = "SELECT proposal.*, 
            organizations.Name AS orgName,
            organizations.State AS State,
            organizations.Country AS Country,
            types.Abbrev as orgAbbrev,
            subject_culture.Name AS Culture,
            subject_area.Name AS Area,
            subject.Name AS Subject
			FROM proposal
			INNER JOIN organizations
				ON proposal.OrgID = organizations.ID
            LEFT JOIN types
            	ON organizations.TypeID = types.ID
            LEFT OUTER JOIN subject_culture
      	        ON proposal.subject_culture = subject_culture.ID
            LEFT OUTER JOIN subject_area
      	        ON proposal.subject_area = subject_area.ID
            LEFT OUTER JOIN subject
      	        ON proposal.subject = subject.ID
			WHERE proposal.ID = " . $id ."";
        return $this->selectQuery($query);
    }

    public function getProposalDescription($id) {
        $query = "SELECT proposal.Description
			FROM proposal
			WHERE proposal.ID = " . $id ."";
        return $this->selectQuery($query);
    }

    public function getAliasOfOrganization($id) {
        $query = "SELECT * FROM organizations WHERE organizations.AliasOf = " . $id ."";
        return $this->selectArrayQuery($query);
    }
}
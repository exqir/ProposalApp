<?php

require_once __DIR__ . '/../../db/SqlConnection.php';
require_once __DIR__ . '/../../proposal/Proposal.php';

class ProposalPuts extends SqlConnection
{
    protected $mysqli;

    public function __construct($db)
    {
        $this->mysqli = $db;
    }

    public function putProposal($payload)
    {
        $proposal = new Proposal();
        $proposal = $proposal->setProposalByArray($payload);
        $query =
            "UPDATE proposal SET
			Title = ?,
			OrgID = ?,
			OrgOptID = ?,
			Description = ?,
			W1 = ?,
			W2 = ?,
			W3 = ?,
			C1 = ?,
			C2 = ?,
			C3 = ?,
			Tenure = ?,
			Ass = ?,
			Raw = ?,
			subject_culture = ?,
			subject_area = ?,
			subject = ?
			WHERE id = ?";
        $titleAdditions = $proposal->getTitleAdditions();
        $params = array(
            $proposal->getTitle(),
            $proposal->getOrganizationId(),
            $proposal->getOrganizationOptId(),
            $proposal->getDescription(),
            $titleAdditions["W1"],
            $titleAdditions["W2"],
            $titleAdditions["W3"],
            $titleAdditions["C1"],
            $titleAdditions["C2"],
            $titleAdditions["C3"],
            $titleAdditions["Tenure"],
            $titleAdditions["Ass"],
            $proposal->raw,
            $proposal->getSubjectCulture(),
            $proposal->getSubjectArea(),
            $proposal->getSubject(),
            $proposal->getId()
        );
        return $this->sqlQuery($query, $params, 'put', NULL);
    }
}
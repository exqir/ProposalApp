<?php

class OrganizationParser {

    public function __construct()
    {

    }

    public static function getProposalWithOrganizationsFromArray($a, $proposal) {
        $p = new self();
        $organizationName = $a[0];
        $o = explode('/',$organizationName);
        $proposal->setOrganization($p->getOrganizationFromArray($o[0],$a));
        if(count($o) > 1) $proposal->setOptionalOrganization($p->getOptionalOrganizationFromArray($o[1],$a));
        return $proposal;
    }

    private function getOrganizationFromArray($name, $a) {
        $organization = new Organization($name);
        $organization = $this->getOrganizationWithCity($a[1], $organization);
        return $organization;
    }

    private function getOrganizationWithCity($string, $organization) {
        $organization->setCity(explode('(',$string)[0]);
        return $organization;
    }
}
?>
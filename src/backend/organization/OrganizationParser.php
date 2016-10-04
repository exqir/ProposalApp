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
        if(count($o) > 1) $proposal->setOrganizationOptional($p->getOptionalOrganizationFromArray($o[1],$a));
        return $proposal;
    }

    private function getOrganizationFromArray($name, $a) {
        return $this->getOrganizationWithCity($a[1], new Organization($name));
    }

    private function getOptionalOrganizationFromArray($name, $a) {
        return count($a) > 3 ?
            $this->getOrganizationWithCity($a[2], new Organization($name)) :
            $this->getOrganizationWithCity($a[1], new Organization($name));
    }

    private function getOrganizationWithCity($string, $organization) {
        return $organization->setCity(explode('(',$string)[0]);
    }
}
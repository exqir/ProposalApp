<?php

class LocationService {
    private $serviceUrl = "https://maps.googleapis.com/maps/api/place/";
    private $language = "de";

    public function __construct() {

    }

    public function getOrganizationWithStateAndCountry(Organization $organization) {
        $placeId = $this->getPlaceIdFromAPI($organization->getCity());
        $placeDetails = $this->getPlaceDetailsFromAPI($placeId);
        return $this->getOrganizationWithCountry(
            $this->getOrganizationWithState($organization, $placeDetails),$placeDetails);
    }

    private function getOrganizationWithState(Organization $organization,$placeDetails) {
        return $organization->setState($this->getStateFromPlaceDetails($placeDetails));
    }

    private function getOrganizationWithCountry(Organization $organization,$placeDetails) {
        return $organization->setCountry($this->getCountryFromPlaceDetails($placeDetails));
    }

    private function getPlaceIdFromAPI($city) {
        $searchUrl = $this->serviceUrl . "textsearch/xml?query=" . urlencode($city) . "&key=" . APIKEY . "&language=" . $this->language;
        $s = file_get_contents($searchUrl);
        $dom = new DomDocument();
        $dom->loadXML($s);
        $xpath = new DOMXPath($dom);
        $place = $xpath->query("/PlaceSearchResponse/result/place_id");
        $placeId = $place->item(0)->nodeValue;
        return $placeId;
    }

    private function getPlaceDetailsFromAPI($placeId) {
        $detailUrl = $this->serviceUrl . "details/xml?placeid=" . $placeId . "&key=" . APIKEY . "&language=" . $this->language;
        $s = file_get_contents($detailUrl);
        $dom = new DomDocument();
        $dom->loadXML($s);
        return new DOMXPath($dom);
    }

    private function getStateFromPlaceDetails($xpath) {
        $componentList = $xpath->query("/PlaceDetailsResponse/result/address_component");
        $state = "";
        foreach ($componentList as $comp) {
            $type = $comp->getElementsByTagName('type')->item(0)->nodeValue;
            if(stripos($type,"administrative_area_level_1") !== false) {
                $state = $comp->getElementsByTagName('long_name')->item(0)->nodeValue;
            }
        }
        return trim($state);
    }

    private function getCountryFromPlaceDetails($xpath) {
        $componentList = $xpath->query("/PlaceDetailsResponse/result/address_component");
        $state = "";
        foreach ($componentList as $comp) {
            $type = $comp->getElementsByTagName('type')->item(0)->nodeValue;
            if(stripos($type,"country") !== false) {
                $state = $comp->getElementsByTagName('long_name')->item(0)->nodeValue;
            }
        }
        return trim($state);
    }
}
<?php
 require_once 'classes/parser.php';
 require_once 'classes/subjects.php';
 require_once 'classes/sqlHandler.php';
 require_once 'config.php';
 require_once 'db.php';

 define("APIKEY", "AIzaSyDimV9AXKs7p5DsWNz1C1pWcoTkNi-bWqk");
 $organization = "HSD Hochschule Döpfer";
 $searchUrl = "https://maps.googleapis.com/maps/api/place/textsearch/xml?query=" . urlencode($organization) . "&key=" . APIKEY;
 $string = file_get_contents($searchUrl);
 // echo "String: <br />" . $string;
 // $xml = simplexml_load_string($string);
 //xml = simplexml_load_file($searchUrl);
 //echo "XML: <br />" . var_dump($xml);
 $dom = new DomDocument();
 $dom->loadXML($string);
 //echo "DOM: <br />" . var_dump($dom);
 $xpath = new DOMXPath($dom);
 $placeId = $xpath->query("/PlaceSearchResponse/result/place_id");
 $pId = $placeId->item(0)->nodeValue;

 $detailUrl = "https://maps.googleapis.com/maps/api/place/details/xml?placeid=" . $pId . "&key=" . APIKEY;
 $string = file_get_contents($detailUrl);
 $dom = new DomDocument();
 $dom->loadXML($string);
 $xpath = new DOMXPath($dom);
 $details = $xpath->query("/PlaceDetailsResponse/result/address_component[6]/long_name");
 $state = $details->item(0)->nodeValue;
 echo $state;
?>

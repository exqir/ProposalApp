<?php
/**
* Static Util Class
*/
class Util {
	public static function getElementsByClass(&$parentNode, $tagName, $className) {
    	$nodes=array();

	    $childNodeList = $parentNode->getElementsByTagName($tagName);
	    for ($i = 0; $i < $childNodeList->length; $i++)  {
	        $temp = $childNodeList->item($i);
	        if (stripos($temp->getAttribute('class'), $className) !== false) {
	            $nodes[]=$temp;
	        }
    	}
        return $nodes;
	}
	
	public static function getParentElementByClass(&$domResult, $tagName, $className) {
		$result;
		$parentNodes = $domResult->getElementsByTagName($tagName);
		
		foreach($parentNodes as $parentNode) {
			$parentNodeClass = $parentNode->getAttribute('class');
			if(stripos($parentNodeClass, $className) !== false) {
				$result = $parentNode;
				return $result;
			}	
		}		
	}

	public static function getJobItemsFromUrl($url) {
        $dom = new DOMDocument();
        $dom->loadHTMLFile($url);
        $parent = Util::getParentElementByClass($dom,'div','result-box');
        return Util::getElementsByClass($parent,'div','job-item');
    }
}
<?php

namespace App\Helpers;

class PreviousPageExtractor{
	public static function getPreviousPage($previousPageURL, $pageParameterName){
		// A fail-safe, in case of invalid input.
		$pageNumberMatch = 1;

		// Matches "services/###"
		preg_match("/services\/[0-9]+$/", $previousPageURL, $pageStringMatch);
		dump($pageStringMatch);
		if(count($pageStringMatch) == 1){
			// Matches only ### from "services/###"
			preg_match( "/[0-9]+/", $pageStringMatch[0], $pageNumberMatch );
		}

		return $pageNumberMatch[0];
	}
}
<?php

namespace App\Helpers;

class PreviousPageExtractor{
	public static function getPreviousPage(string $previousPageURL, string $routeBeforeNumber){
		// A fail-safe, in case of invalid input.
		$pageNumberMatch = 1;

		// Matches "$routeBeforeNumber/###"
		preg_match("/".$routeBeforeNumber."\/[0-9]+$/", $previousPageURL, $pageStringMatch);
		if(count($pageStringMatch) == 1){
			// Matches only ### from "$routeBeforeNumber/###"
			preg_match( "/[0-9]+/", $pageStringMatch[0], $pageNumberMatch );
		}

		return $pageNumberMatch[0];
	}
}
<?php

	class Mighty_Utilities {
		
		public static function breadcrumbs($arrayB = ''){
			$url = Mighty::request();
			$url = $url->urlParts;

			if(is_array($url)) {
				unset($url[0]);

				foreach ($url as $value) {
					$value = str_replace('-', ' ', $value);
					$pageName .= ucfirst($value);
					$pageName .= ' / ';
				}

				if(count($arrayB) > 0) {
					foreach ($arrayB as $value) {
						$pageName .= ucfirst($value);
						$pageName .= ' / ';
					}
				}

				return substr($pageName, 0,-3);
			}
		}

		public static function urlify($string){
			 $string = trim($string);
			 $string = strtolower($string);
			 $string = str_replace(array('å', 'ä', 'ö', ' '), array('a', 'a', 'o', '-'), $string);
			 $string = preg_replace("/[^a-z0-9-]/", "", $string);
			 $string = preg_replace("/[-]+/", "-", $string);
			 return $string;
		}

		public static function isimage($url) {
			if(@is_array(getimagesize($url))){
				$image = true;
			} else {
				$image = false;
			}

			return $image;
		}
		
	}

?>
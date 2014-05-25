<?php

	class Mighty {

		public static function request(){
			return new mighty_request;
		}

		public static function breadcrumbs($arrayB = ''){
			$url = self::request();
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
		


		public static function Pages(){
			return new Pages;
		}

		public static function Article(){
			return new Article;
		}

		public static function Users(){
			return new Users;
		}

		public static function Snippets(){
			return new Snippets;
		}

		public static function Uploadit(){
			return new Mighty_Upload;
		}

		public static function Filemanager(){
			return new Filemanager;
		}

		public static function Activities(){
			return new Mighty_Activities;
		}

		public static function Auth(){
			return new Mighty_Auth;
		}
	}

?>
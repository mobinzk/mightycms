<?php

	class Mighty {

		public static function request(){
			return new Mighty_Request;
		}

		public static function Pages(){
			return new Mighty_Pages;
		}

		public static function Article(){
			return new Mighty_Article;
		}

		public static function Shop(){
			return new Mighty_Shop;
		}

		public static function Users(){
			return new Mighty_Users;
		}

		public static function Snippets(){
			return new Mighty_Snippets;
		}

		public static function Uploadit(){
			return new Mighty_Upload;
		}

		public static function Filemanager(){
			return new Mighty_Filemanager;
		}

		public static function Activities(){
			return new Mighty_Activities;
		}

		public static function Auth(){
			return new Mighty_Auth;
		}
	}

?>
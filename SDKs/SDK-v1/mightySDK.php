<?php
	/**
	 * Helpers
	 */
	include 'helpers/MightyNav.php';
	include 'helpers/MightyPages.php';
	include 'helpers/MightyArticles.php';
	include 'helpers/MightySnippets.php';
	include 'helpers/MightyUtility.php';
	include 'helpers/MightyShop.php';


	class mightySDK {

		public function __construct(){
			$this->nav = new MightyNav;
			$this->pages = new MightyPages;
			$this->articles = new MightyArticles;
			$this->snippets = new MightySnippets;
			$this->utility = new MightyUtility;
			$this->shop = new MightyShop;
		}

	}

?>
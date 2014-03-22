<?php

	class Mighty_Request {
		
		public $url;
		public $urlParts;

		public function __construct(){
			$this->url   = $this->getUrl();
			$this->urlParts = $this->getParts();
		}

		public function getUrl(){
			return $_SERVER['REQUEST_URI'];
		}

		public function getParts(){
			$url = substr($this->url, 1, strlen($this->url) - 1);
			$urlParts = explode('/',$url);
			return $urlParts;
		}

	}

?>

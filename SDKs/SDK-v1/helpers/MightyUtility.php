<?php

	class MightyUtility {

		public function __construct(){
		}

		public function getURL($uri=''){

			$return = array();

			$parts = explode('/', $uri);
			/**
			 * Reset parts index order
			 */
			
			foreach ($parts as $key => $part){
				if (!$part){
					unset($parts[$key]);
				}
			}

			$parts = array_merge($parts, array());

			$return =  array_merge($return, array(
				'parts'     => $parts,
				'full_path' => $uri,
			));

			
			if(count($parts) > 0) {
				$checkURL = $this->checkURL($return['parts']);

				if($checkURL == 'blog_inner' || $checkURL == 'blog') {
					$return['temp'] = $checkURL;
				}
			} else {
				$checkURL = 'home';
			}

			if($checkURL){
				return (object) $return;
			} else {
				return (object) array('error' => 404);
			}

		}

		public function checkURL($urls){
			if(count($urls) == 1) {
				$query = DBi::getRow('SELECT * FROM pages WHERE url ="'.$urls[0].'" AND parentid IS NULL');
			} else if(count($urls) == 2) {
				$query = DBi::getRow('SELECT * FROM pages WHERE url ="'.$urls[0].'" AND parentid IS NULL');
				$query = DBi::getRow('SELECT * FROM pages WHERE url ="'.$urls[1].'" AND parentid ="'.$query->id.'"');

				// Check if it's on blog
				if(!$query) {
					$query = DBi::getRow('SELECT * FROM pages WHERE url ="'.$urls[0].'" AND parentid IS NULL');
					$query = DBi::getRow('SELECT * FROM articles WHERE url ="'.$urls[1].'"');
					if($query) {
						$query = 'blog_inner';
					}
				}
			} else if(count($urls) == 3) {
				$query = DBi::getRow('SELECT * FROM pages WHERE url ="'.$urls[0].'" AND parentid IS NULL');
				$query = DBi::getRow('SELECT * FROM pages WHERE url ="'.$urls[1].'" AND parentid ="'.$query->id.'"');
				$query = DBi::getRow('SELECT * FROM pages WHERE url ="'.$urls[2].'" AND parentid ="'.$query->id.'"');

				// Check if it's next page or category
				if(!$query && ($urls[1] == 'page' || $urls[1] == 'category')) {
					$query = 'blog';
				}
			}

			return $query;
		}

		public function thumb($url){
			$urlExplode = explode('.', $url);
			if(count($urlExplode) > 1) {
				$url = $urlExplode[0].'_thumb.'.$urlExplode[1];
			}

			return $url;
		}

		public function shorttext($text, $limit){
			$length   = strlen($text);
			
			$content  = substr($text, 0, $limit);
			$content .= ($length > $limit) ? '...' : ''; 

			return $content;
		}


	}

?>
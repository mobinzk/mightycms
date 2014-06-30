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

				if($checkURL == 'blog_inner' || $checkURL == 'blog'  || $checkURL == 'products' || $checkURL == 'product') {
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
				$query = DBi::getRow('SELECT * FROM `pages` WHERE `url` ="'.$urls[0].'" AND parentid IS NULL');
			} else if(count($urls) == 2) {
				$query = DBi::getRow('SELECT * FROM `pages` WHERE `url` ="'.$urls[0].'" AND parentid IS NULL');
				$query = DBi::getRow('SELECT * FROM `pages` WHERE `url` ="'.$urls[1].'" AND parentid ="'.$query->id.'"');

				// Check if it's on blog
				if(!$query) {
					$query = DBi::getRow('SELECT * FROM `pages` WHERE `url` ="'.$urls[0].'" AND parentid IS NULL');
					$query = DBi::getRow('SELECT * FROM `articles` WHERE `url` ="'.$urls[1].'"');
					if($query) {
						$query = 'blog_inner';
					}
				}

				// Check for product categories
				if(!$query) {
					$query = DBi::getRow('SELECT * FROM `pages` WHERE `url` ="'.$urls[0].'" AND parentid IS NULL');
					$query = DBi::getRow('SELECT * FROM `shop_products_categories` WHERE `url` ="'.$urls[1].'"');
					if($query) {
						$query = 'products';
					}
				}


			} else if(count($urls) == 3) {
				$query = DBi::getRow('SELECT * FROM `pages` WHERE `url` ="'.$urls[0].'" AND parentid IS NULL');
				$query = DBi::getRow('SELECT * FROM `pages` WHERE `url` ="'.$urls[1].'" AND parentid ="'.$query->id.'"');
				$query = DBi::getRow('SELECT * FROM `pages` WHERE `url` ="'.$urls[2].'" AND parentid ="'.$query->id.'"');

				// Check if it's next page or category
				if(!$query && ($urls[1] == 'page' || $urls[1] == 'category')) {
					$query = 'blog';
				}

				// Check if it's product page
				if(!$query) {
					$query1 = DBi::getRow('SELECT * FROM `pages` WHERE `url` ="'.$urls[0].'" AND parentid IS NULL');
					
					if($query1) {
						$query2 = DBi::getRow('SELECT * FROM `shop_products_categories` WHERE `url` ="'.$urls[1].'"');
						
						if($query2) {
							$query3 = DBi::getRow('SELECT * FROM `shop_products` WHERE `url` ="'.$urls[2].'" AND `categoryid` = '.$query2->categoryid.' ');
							
							if($query3) {

								$query = 'product';
							}
						}
					}
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

		public function sortXMLgooglemap($results, $ignore = array()) {
			
				foreach ($results as $value) {
					
					if(!in_array($value->id, $ignore)) {
						$URL = '';
						if($value->parentid) {
							$parentURL = MightyPages::cascadeURL($value->parentid);

							foreach ($parentURL as $p) {
								$URL .= '/'.$p->url;
							}
						}

						// It's product
						if($value->instock) {
							$URL = '/products/'.$value->category->url;
						}

						// It's product categories
						if($value->categoryid && !$value->instock) {
							$URL = '/products';
						}

						$xml .= '<url>';
						$xml .= "\r\n";
						$xml .= '<loc>http://'.$_SERVER['SERVER_NAME'].$URL.'/'.$value->url.'</loc>';
						$xml .= "\r\n";
						$xml .= '<lastmod>'.date('c', time()).'</lastmod>';
						$xml .= "\r\n";
						$xml .= '<changefreq>monthly</changefreq>';
						$xml .= "\r\n";
						$xml .= '<priority>0.8</priority>';
						$xml .= "\r\n";
						$xml .= '</url>';
						$xml .= "\r\n";

					}
						

					if($value->sub_pages) {
						$xml .= $this->sortXML($value->sub_pages, $ignore);
					}

				}
				return $xml;
		}

		public function googlesitemap($ignore = array()) {
			$pages = MightyPages::getAll();
			$mightyshop = new MightyShop;
			$products = $mightyshop->getProducts()['products'];
			$productCategories = $mightyshop->getCategories();

			$ignorePages = array('5');

			$xml = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

				if($pages){
					$xml .= MightyUtility::sortXMLgooglemap($pages, $ignorePages);
				}

				if($products){
					$xml .= MightyUtility::sortXMLgooglemap($products);
				}

				if($productCategories){
					$xml .= MightyUtility::sortXMLgooglemap($productCategories);
				}

			$xml .= '</urlset>';

			header ("Content-Type:text/xml");
			
			return $xml;
		}


	}

?>
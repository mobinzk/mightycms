<?php

	class MightyPages {

		public function __construct(){
			
		}

		public function getAll($parentid = '') {
			
			if($parentid == 0) {
				$parent = "WHERE parentid is NULL";
			} else {
				$parent = "WHERE parentid = '$parentid'";
			}

			$pages = DBi::getAll("SELECT * FROM pages $parent ORDER BY position ASC");

			if($pages)
			foreach ($pages as $key => $page) {
				$pages_detail[$key] = $page;
				
					$sub_pages = self::getAll($page->id);
					if ($sub_pages){
						$pages_detail[$key]->sub_pages = $sub_pages;				
					}
			}

			return $pages_detail;

		}

		public function get($url = ''){

			if(is_numeric($url)) {
				return DBi::getRow("SELECT * FROM `pages` WHERE `id` = '$url' AND `published` = '1'");
			} else {
				return DBi::getRow("SELECT * FROM `pages` WHERE `url` = '$url' AND `published` = '1'");
			}
		}

		public function getInner($parentid = ''){

			if($parentid)
			return DBi::getAll("SELECT * FROM `pages` WHERE `parentid` = '$parentid' AND `published` = '1' ORDER BY position");
		}

		public function getParentChild($parentid = ''){

			if($parentid)
			$parent = DBi::getAll("SELECT * FROM `pages` WHERE `id` = '$parentid' AND `published` = '1'");
			$child  = DBi::getAll("SELECT * FROM `pages` WHERE `parentid` = '$parentid' AND `published` = '1' ORDER BY position");

			if($parent && $child) {
				return array_merge($parent, $child);
			}
		}

		public function snippets($pageid) {

			if($pageid)
			$data = DBi::getAll("SELECT 
						name,
						value
					FROM 
						`page_snippets`
					WHERE 
						`pageid` = '$pageid'
					");

			if ($data){
				foreach ($data as $snippet){
					$response[$snippet->name] = stripslashes($snippet->value);
				}
			}

			return (object) $response;

		}

		public function cascadeURL($parentid) {
			$results[] = $this->get($parentid);

			foreach ($results as $value) {
				if($value->parentid) {
					$results[] = $this->get($value->parentid);
				}
			}

			return array_reverse($results);
		}

		public function sortXML($results, $ignore = array()) {
			
				foreach ($results as $value) {
					
					if(!in_array($value->id, $ignore)) {
						$URL = '';
						if($value->parentid) {
							$parentURL = $this->cascadeURL($value->parentid);

							foreach ($parentURL as $p) {
								$URL .= '/'.$p->url;
							}
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
			$results = $this->getAll();

			$xml = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

				$xml .= $this->sortXML($results, $ignore);

			$xml .= '</urlset>';

			header ("Content-Type:text/xml");
			
			return $xml;
		}
	}

?>
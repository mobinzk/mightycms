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

			$pages = DBi::getAll("SELECT * FROM pages $parent AND `published` = '1' ORDER BY position ASC");

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

			if($pageid) {
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

					return (object) $response;
				}

			}

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

	}

?>
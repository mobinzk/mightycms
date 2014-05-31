<?php

	class MightyArticles {

		public function __construct(){
			
		}

		public function getAll($options=''){

			/**
			 * Default options
			 */

			$option = array(
				'published' => 1,
				'category' => '',
				'orderBy'   => '`date` DESC',
				'page'      => '1',
				'per_page'   => '10'
			);

			if (isset($options) && is_array($options)){
				foreach ($options as $key => $val){
					$option[$key] = $val;
				}
			}

			$option = (object) $option;

			/**
			 * Categories
			 */

			if($option->category) {
				$category = ' AND b.categoryid ='.$option->category.' ';
			}

			/**
			 * Pagination Limit
			 */

			if(!$option->category) {
				if ($option->page == 1){
					$rowStart = 0;	
				} else {
					$rowStart = ($option->page - 1) * $option->per_page;
				}

				$limit = 'LIMIT '.$rowStart.','.$option->per_page;


				/**
				 * Total rows
				 */

				$totalRows = DBi::getRow("SELECT count(id) as count FROM `articles` WHERE `published` = '".$option->published."'");
				$totalPages = ceil($totalRows->count/$option->per_page);
			}


			// Get articles

			$articles = DBi::getAll("
				SELECT 
					b.id AS id,
					b.name AS name,
					b.url AS url,
					b.date AS date,
					b.categoryid AS category,
					CONCAT(u.firstname, ' ', u.surname) as author 
				FROM 	articles b
				LEFT JOIN mighty_users u
					ON		b.author = u.id
					AND		b.published = '".$option->published."' 
				WHERE b.published = '1'
				$category
				ORDER BY ".$option->orderBy."
				$limit"
			);

			if($articles)
			foreach ($articles as $key => $article) {
				$articles[$key] = $article;
				$articles[$key]->snippets = $this->snippets($article->id);
			}

			$next_page = ($option->page < $totalPages ? $option->page + 1 : '');
			$prev_page = ($option->page == 1 ? '' : $option->page - 1);

			$return = array(
				'page'      => $option->page,
				'rows'      => $totalRows->count,
				'page_rows' => count($articles),
				'pages'     => $totalPages,
				'per_page'  => $option->per_page,
				'next_page' => $next_page,
				'prev_page' => $prev_page,
				'articles'  => $articles
			);

			return $return;

		}

		public function get($url = ''){

			if(is_numeric($url)) {
				return DBi::getRow("SELECT * FROM `articles` WHERE `id` = '$url' AND `published` = '1'");
			} else {
				return DBi::getRow("SELECT * FROM `articles` WHERE `url` = '$url' AND `published` = '1'");
			}
		}

		public function getRelated($category, $id, $limit = '5'){

			if($category && $id) {
				//return DBi::getAll("SELECT * FROM `articles` WHERE `categoryid` = '$category' AND `id` != $id AND `published` = '1' LIMIT $limit");
				$articles = DBi::getAll("
					SELECT 
						b.id AS id,
						b.name AS name,
						b.url AS url,
						b.date AS date,
						b.categoryid AS category,
						CONCAT(u.firstname, ' ', u.surname) as author 
					FROM 	articles b
					LEFT JOIN mighty_users u
						ON		b.author = u.id
						AND		b.published = '1' 
					WHERE b.published = '1'
					AND b.categoryid = '$category' 
					AND b.id != $id
					ORDER BY b.id desc
					LIMIT $limit"
				);

				if($articles)
				foreach ($articles as $key => $article) {
					$articles[$key] = $article;
					$articles[$key]->snippets = $this->snippets($article->id);
				}

				return $articles;
			}
		}

		public function snippets($articleid) {

			if($articleid)
			$data = DBi::getAll("SELECT 
						name,
						value
					FROM 
						`articles_snippets`
					WHERE 
						`articleid` = '$articleid'
					");

			if ($data){
				foreach ($data as $snippet){
					$response[$snippet->name] = stripslashes($snippet->value);
				}
			}

			return (object) $response;

		}

		public function getCategories() {

			$categories = DBi::getAll("SELECT 
						*
					FROM 
						`articles_categories`
					WHERE 
						`published` = '1'
					ORDER BY `position` DESC
					");

			return (object) $categories;

		}

		public function getCategory($url = ''){

			if(is_numeric($url)) {
				return DBi::getRow("SELECT * FROM `articles_categories` WHERE `categoryid` = '$url' AND `published` = '1'");
			} else {
				return DBi::getRow("SELECT * FROM `articles_categories` WHERE `url` = '$url' AND `published` = '1'");
			}
		}
	}

?>
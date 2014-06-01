<?php

	class MightyShop {

		public function __construct(){
			
		}

		public function getProducts($options = '') {

			/**
			 * Default options
			 */

			$option = array(
				'published' => 1,
				'category' => '',
				'ignore'	=> '',
				'page'      => '1',
				'per_page'   => '999999',
				'orderBy'   => '`position` DESC'
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
				$category = ' AND categoryid ='.$option->category.' ';
			}


			/**
			 * Pagination Limit
			 */

			// if(!$option->category) {
				if ($option->page == 1){
					$rowStart = 0;	
				} else {
					$rowStart = ($option->page - 1) * $option->per_page;
				}

				$limit = 'LIMIT '.$rowStart.','.$option->per_page;


				/**
				 * Total rows
				 */

				$totalRows = DBi::getRow("SELECT count(id) as count FROM `shop_products` WHERE `published` = '".$option->published."'");
				$totalPages = ceil($totalRows->count/$option->per_page);
			// }


			/**
			 * Ignore a page
			 */

			if($option->ignore) {
				$ignore = 'AND id != '.$option->ignore;
			}

			
			$products = DBi::getAll("SELECT * FROM 
										shop_products 
									WHERE 
										`published` = '".$option->published."'
									
									$category

									$ignore

									ORDER BY 
										".$option->orderBy." 
									$limit");
			if($products)
			foreach ($products as $key => $product) {
				$product_detail[$key] = $product;
				$product_detail[$key]->category = $this->getCategory($product->categoryid);

					$snippets = $this->productSnippets($product->id);
					if ($snippets){
						$product_detail[$key]->snippets = $snippets;
					}
			}

			$next_page = ($option->page < $totalPages ? $option->page + 1 : '');
			$prev_page = ($option->page == 1 ? '' : $option->page - 1);

			$return = array(
				'page'      => $option->page,
				'rows'      => $totalRows->count,
				'page_rows' => count($product_detail),
				'pages'     => $totalPages,
				'per_page'  => $option->per_page,
				'next_page' => $next_page,
				'prev_page' => $prev_page,
				'products'  => $product_detail
			);

			return $return;

		}

		public function productSnippets($productid) {

			if($productid)
			$data = DBi::getAll("SELECT 
						name,
						value
					FROM 
						`shop_products_snippets`
					WHERE 
						`productid` = '$productid'
					");

			if ($data){
				foreach ($data as $snippet){
					$response[$snippet->name] = stripslashes($snippet->value);
				}
			}

			return (object) $response;

		}

		public function getProduct($url = ''){

			if(is_numeric($url)) {
				return DBi::getRow("SELECT * FROM `shop_products` WHERE `id` = '$url' AND `published` = '1'");
			} else {
				return DBi::getRow("SELECT * FROM `shop_products` WHERE `url` = '$url' AND `published` = '1'");
			}
		}

		public function getCategories() {
			
			$categories = DBi::getAll("SELECT * FROM shop_products_categories WHERE `published` = '1' ORDER BY position ASC");

			if($categories)
			foreach ($categories as $key => $cateory) {
				$category_detail[$key] = $cateory;

					$snippets = $this->categorySnippets($cateory->categoryid);
					if ($snippets){
						$category_detail[$key]->snippets = $snippets;
					}
			}

			return $category_detail;

		}

		
		public function categorySnippets($categoryid) {

			if($categoryid)
			$data = DBi::getAll("SELECT 
						name,
						value
					FROM 
						`shop_products_categories_snippets`
					WHERE 
						`categoryid` = '$categoryid'
					");

			if ($data){
				foreach ($data as $snippet){
					$response[$snippet->name] = stripslashes($snippet->value);
				}
			}

			return (object) $response;

		}

		public function getCategory($url = ''){

			if(is_numeric($url)) {
				return DBi::getRow("SELECT * FROM `shop_products_categories` WHERE `categoryid` = '$url' AND `published` = '1'");
			} else {
				return DBi::getRow("SELECT * FROM `shop_products_categories` WHERE `url` = '$url' AND `published` = '1'");
			}
		}

		public function addVisit($productid) {
			if($productid) {
				DBi::query("UPDATE `shop_products` SET `visit` = visit + 1 WHERE `id` = $productid");
			}
		}


	}

?>
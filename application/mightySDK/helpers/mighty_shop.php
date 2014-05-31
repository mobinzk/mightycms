<?php

	class Mighty_Shop {

		public function __construct() {
		}

		public static function getAll(){
			
			return DBi::getAll("SELECT 
									* 
								FROM 
									shop_products  
								ORDER BY position DESC");
		}

		public function addNewProduct() {
			extract($_POST);

			if($name) {

				foreach ($_POST as $key => $value) {
					if($value)
					$fields[$key] = $value;
				}

				if ($_FILES){
					$fields['file_upload_list'] = $_FILES;
				}

				// ====== Handle file uploads
				if ($fields['file_upload_list']){

					// Loop through each file that needs to be uploaded
					if (is_array($fields['file_upload_list'])){

						$upload_response = Mighty::Uploadit()->uploadTemplateFiles($fields['file_upload_list'], $fields);

						if ($upload_response->result){
							if ($upload_response->fields){
								$fields = $upload_response->fields;
							}
						}

					}
				}
				// ====== Handle file uploads

				$fields = Mighty::Pages()->concatenateFieldVariations($fields);

				$date = explode ('/',$date);
				$date = $date[2].'-'.$date[1].'-'.$date[0];
			    
			    $position = DBi::getRow("SELECT 
												`position` + 1
										AS
												position
										FROM 
												`shop_products` 
										ORDER BY 
												position 
										DESC LIMIT 0,1");
			    
			    $pageid = DBi::query("INSERT INTO 
				    				`shop_products` 
				    			SET 
				    				`name` 			= '".dbi::mysqli()->real_escape_string($name)."', 
				    				`url` 			= '".dbi::mysqli()->real_escape_string(Mighty_Utilities::urlify($url))."', 
				    				`position` 		= '$position->position',
				    				`categoryid` 	= '".$categoryid."'
				    			");

			    $fields['template_variation_options_END_data_link_value'] = $pageid['id'];

			    $this->addSnippets($fields, $pageid['id']);			    
				Mighty::Pages()->saveTemplateVariations($fields);

				Mighty::activities()->log('added a new product ('.stripslashes($name).')', 'add');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> 'New product has been added <a href="/mightycms/shop">Return to Products</a> - 
									<form method="post" action="/mightycms/shop/edit-product">
										<input type="hidden" value="edit" name="action">
										<input type="hidden" value="'.$pageid['id'].'" name="id">
										<button type="submit">Edit <strong>"'. stripslashes($name) .'"</strong></button>
									</form>',
				);
			}

			return (object) $response;
		}

		public function editProduct() {
			extract($_POST);

			foreach ($_POST as $key => $value) {
				if($value)
				$fields[$key] = $value;
			}

			if ($_FILES){
				$fields['file_upload_list'] = $_FILES;
			}

			if($name) {

				// ====== Handle file uploads
				if ($fields['file_upload_list']){

					// Loop through each file that needs to be uploaded
					if (is_array($fields['file_upload_list'])){

						$upload_response = Mighty::Uploadit()->uploadTemplateFiles($fields['file_upload_list'], $fields);

						if ($upload_response->result){
							if ($upload_response->fields){
								$fields = $upload_response->fields;
							}
						}

					}
				}
				// ====== Handle file uploads

				$fields = Mighty::Pages()->concatenateFieldVariations($fields);

				$date = explode ('/',$date);
				$date = $date[2].'-'.$date[1].'-'.$date[0];

			   DBi::query("UPDATE 
				    				`shop_products` 
				    			SET 
				    				`name` 			= '".dbi::mysqli()->real_escape_string($name)."', 
				    				`url` 			= '".dbi::mysqli()->real_escape_string(Mighty_Utilities::urlify($url))."', 
				    				`categoryid` 	= '".$categoryid."'
				    			WHERE
			    					`id`			= '$id'
				    			");

			    $this->addSnippets($fields, $id);
				Mighty::Pages()->saveTemplateVariations($fields);

				Mighty::activities()->log('updated a product ('.stripslashes($name).')', 'update');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> 'Your changes have been saved. <a href="/mightycms/shop">Return to Products</a>',
				);

			}

			return (object) $response;
		}


		public function addSnippets($fields, $productid) {

			$ignore_fields = array( 'file_upload_list', 
						'.*_option_.*', 
						'.*_file_upload_item.*', 
						'template_variation_.*', 
						'template', 
						'deletable', 
						'editable', 
						'publishable',
						'subpagable', 
						'position', 
						'published', 
						'parentid', 
						'categoryid', 
						'name',
						'url',
						'nav_location',
						'id',
						'date');

			// Delete existing snippets
			DBi::query("
				DELETE FROM 
					`shop_products_snippets` 
				WHERE 
					`productid` = '$productid'
			");

			foreach ($fields as $key => $val){
							
				if (!preg_match('/^('.implode('|', $ignore_fields).')$/si', $key) & !empty($val)){

					DBi::query("INSERT INTO 
									`shop_products_snippets` 
								SET 
									`productid`  = '".$productid."',
									`name` = '".dbi::mysqli()->real_escape_string($key)."',
									`value` = '".dbi::mysqli()->real_escape_string($val)."'
						");

					$parser = new Markdown;
					DBi::query("INSERT INTO 
									`shop_products_snippets` 
								SET 
									`productid`  = '".$productid."',
									`name` = '".dbi::mysqli()->real_escape_string($key)."_html',
									`value` = '".dbi::mysqli()->real_escape_string($parser->transform($val))."'
						");

				}
			}

		}

		public function delete() {
			extract($_POST);

			$page = DBi::getRow("SELECT `name` FROM `shop_products` WHERE id = '$id'");

			DBi::query("DELETE FROM `shop_products` WHERE `id` = '$id'");

			Mighty::activities()->log('deleted a product ('.stripslashes($page->name).')', 'delete');
			$response['ui_alert'] = (object) array(
				'type' 		=> 'success',
				'heading' 	=> 'Success!',
				'message' 	=> '<strong>"'.stripslashes($page->name).'"</strong> has been Deleted.',
			);

			return (object) $response;
		}

		public function publish() {
			extract($_POST);
			$currentPublished = DBi::getRow("SELECT published, name FROM shop_products WHERE id = '$id'");
			$published = ($currentPublished->published == '0') ? 1 : 0;
			DBi::query("UPDATE `shop_products` SET `published` = '$published' WHERE `id` = '$id'");

			if($published == 0) {
				Mighty::activities()->log('unpublished a product ('.stripslashes($currentPublished->name).')', 'unpublished');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> '<strong>"'.stripslashes($currentPublished->name).'"</strong> has been Unpublished.',
				);
			} else if($published == 1) {
				Mighty::activities()->log('published a product ('.stripslashes($currentPublished->name).')', 'published');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> '<strong>"'.stripslashes($currentPublished->name).'"</strong> has been Published.',
				);
			}

			return (object) $response;
		}


		public function getProduct($id) {
			$article = DBi::getRow("SELECT 
										id,
										id as pid,
										categoryid,
										name,
										url,
										published,
										instock 
									FROM 
										`shop_products` 
									WHERE 
										`id` = $id");

				$snippets = DBi::getAll("
					SELECT * 
					FROM `shop_products_snippets`
					WHERE `productid` = '$id'
				");

				if (is_array($snippets)){
					foreach ($snippets as $snippet){
						$fields[$snippet->name] = stripslashes($snippet->value);
					}
					$article->fields = (object) $fields;
				}

				return (object) $article;
		}

		public function getField($name, $id){
			$field = DBi::getRow("SELECT * FROM `shop_products_snippets` WHERE `productid` = '$id' AND `name` = '$name'");
			return stripslashes($field->value);
		}

		public function sort_products(){
			$order = $_POST['ids'];
			$position = 0;
			foreach ($order as $id) {
			    $position = $position + 1;
			    DBi::query("UPDATE `shop_products` SET `position` = '$position' WHERE `id` = '$id'");
			}

			Mighty::activities()->log('updated the products structure.', 'sort');
				
		}

		public function sort_categories(){
			$order = $_POST['ids'];
			$position = 0;
			foreach ($order as $id) {
			    $position = $position + 1;
			    DBi::query("UPDATE `shop_products_categories` SET `position` = '$position' WHERE `categoryid` = '$id'");
			}

			Mighty::activities()->log('updated the product categories structure.', 'sort');
				
		}



		// Categories ----------------------------------
		public static function getCategories(){
			
			return DBi::getAll("SELECT 
									* 
								FROM 
									shop_products_categories 
								ORDER BY position ASC");
		}

		public function getCategory($id) {

			$category = DBi::getRow("SELECT 
										categoryid,
										categoryid as bid,
										name,
										url,
										published
									FROM 
										`shop_products_categories` 
									WHERE 
										`categoryid` = $id");

				$snippets = DBi::getAll("
					SELECT * 
					FROM `shop_products_categories_snippets`
					WHERE `categoryid` = '$id'
				");

				if (is_array($snippets)){
					foreach ($snippets as $snippet){
						$fields[$snippet->name] = stripslashes($snippet->value);
					}
					$category->fields = (object) $fields;
				}

				return (object) $category;
		}

		public function getNoProducts($categoryid) {
			$category = DBi::getRow("SELECT 
										count(id) as count
									FROM 
										`shop_products` 
									WHERE 
										`categoryid` = $categoryid");

				return $category->count;
		}

		public function addNewCategory() {
			extract($_POST);

			if($name) {

				foreach ($_POST as $key => $value) {
					if($value)
					$fields[$key] = $value;
				}

				if ($_FILES){
					$fields['file_upload_list'] = $_FILES;
				}

				// ====== Handle file uploads
				if ($fields['file_upload_list']){

					// Loop through each file that needs to be uploaded
					if (is_array($fields['file_upload_list'])){

						$upload_response = Mighty::Uploadit()->uploadTemplateFiles($fields['file_upload_list'], $fields);

						if ($upload_response->result){
							if ($upload_response->fields){
								$fields = $upload_response->fields;
							}
						}

					}
				}
				// ====== Handle file uploads

				$fields = Mighty::Pages()->concatenateFieldVariations($fields);

				$date = explode ('/',$date);
				$date = $date[2].'-'.$date[1].'-'.$date[0];
			    
			    $position = DBi::getRow("SELECT 
												`position` + 1
										AS
												position
										FROM 
												`shop_products_categories` 
										ORDER BY 
												position 
										DESC");

				$categoryid = DBi::query("INSERT INTO 
				    				`shop_products_categories` 
				    			SET 
				    				`name` 			= '".dbi::mysqli()->real_escape_string($name)."', 
				    				`url` 			= '".dbi::mysqli()->real_escape_string(Mighty_Utilities::urlify($url))."',
				    				`position` 		= '$position->position'
				    			");


			    $this->addCategorySnippets($fields, $categoryid['id']);




			    

				Mighty::activities()->log('added a new product category ('.stripslashes($name).')', 'add');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> 'New category has been added <a href="/mightycms/shop/categories">Return to Categories</a> - 
									<form method="post" action="/mightycms/shop/edit-category">
										<input type="hidden" value="edit" name="action">
										<input type="hidden" value="'.$categoryid['id'].'" name="id">
										<button type="submit">Edit <strong>"'. stripslashes($name) .'"</strong></button>
									</form>',
				);
			}

			return (object) $response;
		}

		public function addCategorySnippets($fields, $categoryid) {

			$ignore_fields = array( 'file_upload_list', 
						'.*_option_.*', 
						'.*_file_upload_item.*', 
						'template_variation_.*', 
						'template', 
						'deletable', 
						'editable', 
						'publishable',
						'subpagable', 
						'position', 
						'published', 
						'parentid', 
						'categoryid', 
						'name',
						'url',
						'nav_location',
						'id',
						'date');

			// Delete existing snippets
			DBi::query("
				DELETE FROM 
					`shop_products_categories_snippets` 
				WHERE 
					`categoryid` = '$categoryid'
			");

			foreach ($fields as $key => $val){
							
				if (!preg_match('/^('.implode('|', $ignore_fields).')$/si', $key) & !empty($val)){

					DBi::query("INSERT INTO 
									`shop_products_categories_snippets` 
								SET 
									`categoryid`  = '".$categoryid."',
									`name` = '".dbi::mysqli()->real_escape_string($key)."',
									`value` = '".dbi::mysqli()->real_escape_string($val)."'
						");

					$parser = new Markdown;
					DBi::query("INSERT INTO 
									`shop_products_categories_snippets` 
								SET 
									`categoryid`  = '".$categoryid."',
									`name` = '".dbi::mysqli()->real_escape_string($key)."_html',
									`value` = '".dbi::mysqli()->real_escape_string($parser->transform($val))."'
						");

				}
			}

		}

		public function editCategory() {
			extract($_POST);
			
			foreach ($_POST as $key => $value) {
				if($value)
				$fields[$key] = $value;
			}

			if ($_FILES){
				$fields['file_upload_list'] = $_FILES;
			}

			if($name) {

				// ====== Handle file uploads
				if ($fields['file_upload_list']){

					// Loop through each file that needs to be uploaded
					if (is_array($fields['file_upload_list'])){

						$upload_response = Mighty::Uploadit()->uploadTemplateFiles($fields['file_upload_list'], $fields);

						if ($upload_response->result){
							if ($upload_response->fields){
								$fields = $upload_response->fields;
							}
						}

					}
				}
				// ====== Handle file uploads

				$fields = Mighty::Pages()->concatenateFieldVariations($fields);

				$date = explode ('/',$date);
				$date = $date[2].'-'.$date[1].'-'.$date[0];

				DBi::query("UPDATE 
			    				`shop_products_categories` 
			    			SET 
			    				`name` 			= '".dbi::mysqli()->real_escape_string($name)."', 
			    				`url` 			= '".dbi::mysqli()->real_escape_string(Mighty_Utilities::urlify($url))."'
			    			WHERE
			    				`categoryid`			= '$id'
			    			");


			    $this->addCategorySnippets($fields, $id);


				Mighty::activities()->log('updated a category ('.stripslashes($name).')', 'update');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> 'Your changes have been saved. <a href="/mightycms/shop/categories">Return to Categories</a>',
				);

			}

			return (object) $response;
		}


		public function deleteCategory() {
			extract($_POST);

			$page = DBi::getRow("SELECT `name` FROM `shop_products_categories` WHERE categoryid = '$id'");

			DBi::query("DELETE FROM `shop_products_categories` WHERE `categoryid` = '$id'");

			Mighty::activities()->log('deleted a category ('.stripslashes($page->name).')', 'delete');
			$response['ui_alert'] = (object) array(
				'type' 		=> 'success',
				'heading' 	=> 'Success!',
				'message' 	=> '<strong>"'.stripslashes($page->name).'"</strong> has been Deleted.',
			);

			return (object) $response;
		}

		public function publishCategory() {
			extract($_POST);
			$currentPublished = DBi::getRow("SELECT published, name FROM shop_products_categories WHERE categoryid = '$id'");
			$published = ($currentPublished->published == '0') ? '1' : '0';
			DBi::query("UPDATE `shop_products_categories` SET `published` = '$published' WHERE `categoryid` = '$id'");

			if($published == 0) {
				Mighty::activities()->log('unpublished a category ('.stripslashes($currentPublished->name).')', 'unpublished');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> '<strong>"'.stripslashes($currentPublished->name).'"</strong> has been Unpublished.',
				);
			} else if($published == 1) {
				Mighty::activities()->log('published a category ('.stripslashes($currentPublished->name).')', 'published');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> '<strong>"'.stripslashes($currentPublished->name).'"</strong> has been Published.',
				);
			}

			return (object) $response;
		}


	}

?>
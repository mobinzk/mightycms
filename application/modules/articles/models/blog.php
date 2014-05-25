<?php

	class Blog {

		public function __construct() {
		}

		public static function getAll(){
			
			return DBi::getAll("SELECT 
									* 
								FROM 
									blog 
								ORDER BY date DESC");
		}

		public function addNewArticle() {
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
			    
			    $pageid = DBi::query("INSERT INTO 
				    				`blog` 
				    			SET 
				    				`name` 			= '".dbi::mysqli()->real_escape_string($name)."',
				    				`url` 			= '".dbi::mysqli()->real_escape_string(Mighty::urlify($url))."', 
				    				`categoryid` 	= '".$categoryid."',
				    				`author`		= '".Mighty::Auth()->userId()."',
				    				`date`			= '".$date."'
				    			");


			    $this->addSnippets($fields, $pageid['id']);

			    

				Mighty::activities()->log('added a new article ('.stripslashes($name).')', 'add');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> 'New article has been added <a href="/mightycms/articles">Return to Blog</a> - 
									<form method="post" action="/mightycms/articles/edit">
										<input type="hidden" value="edit" name="action">
										<input type="hidden" value="'.$pageid['id'].'" name="id">
										<button type="submit">Edit <strong>"'. stripslashes($name) .'"</strong></button>
									</form>',
				);
			}

			return (object) $response;
		}

		public function editArticle() {
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
			    				`blog` 
			    			SET 
			    				`name` 			= '".dbi::mysqli()->real_escape_string($name)."', 
			    				`url` 			= '".dbi::mysqli()->real_escape_string(Mighty::urlify($url))."',
				    			`categoryid` 	= '".$categoryid."',
			    				`date`			= '".$date."'
			    			WHERE
			    				`id`			= '$id'
			    			");

			    $this->addSnippets($fields, $id);

				Mighty::activities()->log('updated an article ('.stripslashes($name).')', 'update');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> 'Your changes have been saved. <a href="/mightycms/articles">Return to Blog</a>',
				);

			}

			return (object) $response;
		}


		public function addSnippets($fields, $blogid) {

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
					`blog_snippets` 
				WHERE 
					`blogid` = '$blogid'
			");

			foreach ($fields as $key => $val){
							
				if (!preg_match('/^('.implode('|', $ignore_fields).')$/si', $key) & !empty($val)){

					DBi::query("INSERT INTO 
									`blog_snippets` 
								SET 
									`blogid`  = '".$blogid."',
									`name` = '".dbi::mysqli()->real_escape_string($key)."',
									`value` = '".dbi::mysqli()->real_escape_string($val)."'
						");

					$parser = new Markdown;
					DBi::query("INSERT INTO 
									`blog_snippets` 
								SET 
									`blogid`  = '".$blogid."',
									`name` = '".dbi::mysqli()->real_escape_string($key)."_html',
									`value` = '".dbi::mysqli()->real_escape_string($parser->transform($val))."'
						");

				}
			}

		}

		public function delete() {
			extract($_POST);

			$page = DBi::getRow("SELECT `name` FROM `blog` WHERE id = '$id'");

			DBi::query("DELETE FROM `blog` WHERE `id` = '$id'");

			Mighty::activities()->log('deleted an article ('.stripslashes($page->name).')', 'delete');
			$response['ui_alert'] = (object) array(
				'type' 		=> 'success',
				'heading' 	=> 'Success!',
				'message' 	=> '<strong>"'.stripslashes($page->name).'"</strong> has been Deleted.',
			);

			return (object) $response;
		}

		public function publish() {
			extract($_POST);
			$currentPublished = DBi::getRow("SELECT published, name FROM blog WHERE id = '$id'");
			$published = ($currentPublished->published == '0') ? 1 : 0;
			DBi::query("UPDATE `blog` SET `published` = '$published' WHERE `id` = '$id'");

			if($published == 0) {
				Mighty::activities()->log('unpublished a page ('.stripslashes($currentPublished->name).')', 'unpublished');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> '<strong>"'.stripslashes($currentPublished->name).'"</strong> has been Unpublished.',
				);
			} else if($published == 1) {
				Mighty::activities()->log('published a page ('.stripslashes($currentPublished->name).')', 'published');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> '<strong>"'.stripslashes($currentPublished->name).'"</strong> has been Published.',
				);
			}

			return (object) $response;
		}


		public function getArticle($id) {
			$article = DBi::getRow("SELECT 
										id,
										id as bid,
										categoryid,
										name,
										url,
										author,
										published,
										date 
									FROM 
										`blog` 
									WHERE 
										`id` = $id");

				$snippets = DBi::getAll("
					SELECT * 
					FROM `blog_snippets`
					WHERE `blogid` = '$id'
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
			$field = DBi::getRow("SELECT * FROM `blog_snippets` WHERE `blogid` = '$id' AND `name` = '$name'");
			return stripslashes($field->value);
		}



		// Categories ----------------------------------
		public static function getCategories(){
			
			return DBi::getAll("SELECT 
									* 
								FROM 
									blog_categories 
								ORDER BY position DESC");
		}

		public function getCategory($id) {
			$category = DBi::getRow("SELECT 
										categoryid,
										categoryid as bid,
										name,
										url,
										published
									FROM 
										`blog_categories` 
									WHERE 
										`categoryid` = $id");

				return (object) $category;
		}

		public function addNewCategory() {
			extract($_POST);

			if($name) {

			    $category = DBi::query("INSERT INTO 
				    				`blog_categories` 
				    			SET 
				    				`name` 			= '".dbi::mysqli()->real_escape_string($name)."', 
				    				`url` 			= '".dbi::mysqli()->real_escape_string(Mighty::urlify($url))."'
				    			");
			    

				Mighty::activities()->log('added a new article category ('.stripslashes($name).')', 'add');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> 'New category has been added <a href="/mightycms/articles/categories">Return to Categories</a> - 
									<form method="post" action="/mightycms/articles/edit-category">
										<input type="hidden" value="edit" name="action">
										<input type="hidden" value="'.$category['id'].'" name="id">
										<button type="submit">Edit <strong>"'. stripslashes($name) .'"</strong></button>
									</form>',
				);
			}

			return (object) $response;
		}

		public function editCategory() {
			extract($_POST);
			
			if($name) {

				DBi::query("UPDATE 
			    				`blog_categories` 
			    			SET 
			    				`name` 			= '".dbi::mysqli()->real_escape_string($name)."', 
			    				`url` 			= '".dbi::mysqli()->real_escape_string(Mighty::urlify($url))."'
			    			WHERE
			    				`categoryid`			= '$id'
			    			");

				Mighty::activities()->log('updated a category ('.stripslashes($name).')', 'update');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> 'Your changes have been saved. <a href="/mightycms/articles/categories">Return to Categories</a>',
				);

			}

			return (object) $response;
		}


		public function deleteCategory() {
			extract($_POST);

			$page = DBi::getRow("SELECT `name` FROM `blog_categories` WHERE categoryid = '$id'");

			DBi::query("DELETE FROM `blog_categories` WHERE `categoryid` = '$id'");

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
			$currentPublished = DBi::getRow("SELECT published, name FROM blog_categories WHERE categoryid = '$id'");
			$published = ($currentPublished->published == '0') ? '1' : '0';
			DBi::query("UPDATE `blog_categories` SET `published` = '$published' WHERE `categoryid` = '$id'");

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
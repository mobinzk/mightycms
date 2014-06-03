<?php

	class Mighty_Pages {

		public function __construct() {
		}
		
		public static function getPages($parentid){
			
			if($parentid == 0) {
				$parent = "WHERE parentid is NULL";
			} else {
				$parent = "WHERE parentid = '$parentid'";
			}

			$pages = DBi::getAll("SELECT * FROM pages $parent ORDER BY position ASC");

			if($pages)
			foreach ($pages as $key => $page) {
				$pages_detail[$key] = $page;
				
					$sub_pages = self::getPages($page->id);
					if ($sub_pages){
						$pages_detail[$key]->sub_pages = $sub_pages;				
					}
			}

			return $pages_detail;

		}

		public static function echoPages($pages) {
			$permissions = self::permissions();
			foreach ($pages as $page) {
				$content .= '
					<li id="'.$page->id.'" '.(!$page->published ? 'class="unpublished-row"' : '').'>
						<section class="page-wrapper">
							<div class="icon-pages"></div>
							<div class="name">'.stripslashes($page->name).'</div>

							<div class="actions">';

								if($page->subpagable && $permissions->add_page)
								$content .= '<form action="/mightycms/pages/new" method="POST">
									<input type="hidden" value="subpage" name="action">
									<input type="hidden" value="'.$page->id.'" name="parentid">
									<input type="hidden" value="'.$page->template.'-inner" name="template">
									<button type="submit" class="uim-button subpage green">Add Sub Page</button>
								</form>';
								
								if($page->deletable && $permissions->delete_page)
								$content .= '<form action="" method="POST">
									<input type="hidden" value="delete" name="action">
									<input type="hidden" value="'.$page->id.'" name="id">
									<button type="submit" class="uim-button delete red">Delete</button>
								</form>';
							
								if($page->publishable && $permissions->publish_page) {
								$content .= '<form action="" method="POST">
									<input type="hidden" value="publish" name="action">
									<input type="hidden" value="'.$page->id.'" name="id">';

									$content .= '<button type="submit" class="uim-button published '.($page->published ? "green" : "red").'">'.($page->published ? "Published" : "Unpublished").'</button>';

								$content .= '</form>';
								}

								if($page->editable && $permissions->edit_page)
								$content .= '<form action="/mightycms/pages/edit" method="POST">
									<input type="hidden" value="edit" name="action">
									<input type="hidden" value="'.$page->id.'" name="id">
									<button type="submit" class="uim-button edit blue">Edit</button>
								</form>';
								
							$content .= '</div>';

							if($permissions->sort_page) {
							$content .= '<div class="move">
											<div class="icon-move"></div>
										</div>';
							}
						$content .= '</section>';

						if($page->sub_pages) {
							$content .= '<ul>';
							$content .= self::echoPages($page->sub_pages);
							$content .= '</ul>';
						}

					$content .= '</li>';
			}

			return $content;

		}

		public function addNewPage() {
			extract($_POST);

			$parent 	= ($parentid) ? " WHERE `parentid` = '$parentid' " : " WHERE `parentid` is NULL ";
			$parentid 	= ($parentid) ? " ,`parentid` = '$parentid' " : "";

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

				$fields = $this->concatenateFieldVariations($fields);
				
				$position = DBi::getRow("SELECT 
												`position` + 1
										AS
												position
										FROM 
												`pages` 
												
												$parent
										ORDER BY 
												position 
										DESC");
			    
			    $pageid = DBi::query("INSERT INTO 
				    				`pages` 
				    			SET 
				    				`name` 			= '".dbi::mysqli()->real_escape_string($name)."', 
				    				`url` 			= '".dbi::mysqli()->real_escape_string(Mighty_Utilities::urlify($url))."', 
				    				`position` 		= '$position->position', 
				    				`deletable` 	= '$deletable', 
				    				`editable` 		= '$editable',
				    				`publishable` 	= '$publishable',
				    				`subpagable` 	= '$subpagable',
				    				`template`		= '$template'
				    				$parentid
				    			");

			    $fields['template_variation_options_END_data_link_value'] = $pageid['id'];


			    $this->addSnippets($fields, $pageid['id']);
				$this->saveTemplateVariations($fields);

			    
			    $response['id'] = $pageid['id'];

				Mighty::activities()->log('added a new page ('.stripslashes($name).')', 'add');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> 'New page has been added <a href="/mightycms/pages">Return to Pages</a> - 
									<form method="post" action="/mightycms/pages/edit">
										<input type="hidden" value="edit" name="action">
										<input type="hidden" value="'.$pageid['id'].'" name="id">
										<button type="submit">Edit <strong>"'. stripslashes($name) .'"</strong></button>
									</form>',
				);
			}

			return (object) $response;
		}

		public function editPage() {
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

				$fields = $this->concatenateFieldVariations($fields);

				DBi::query("UPDATE 
			    				`pages` 
			    			SET 
			    				`name` 			= '".dbi::mysqli()->real_escape_string($name)."', 
			    				`url` 			= '".dbi::mysqli()->real_escape_string(Mighty_Utilities::urlify($url))."', 
			    				`deletable` 	= '$deletable', 
			    				`editable` 		= '$editable',
			    				`publishable` 	= '$publishable',
			    				`subpagable` 	= '$subpagable',
			    				`template`		= '$template'
			    			WHERE
			    				`id`			= '$id'
			    			");

			    $this->addSnippets($fields, $id);
				$this->saveTemplateVariations($fields);

				Mighty::activities()->log('updated a page ('.stripslashes($name).')', 'update');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> 'Your changes have been saved. <a href="/mightycms/pages">Return to Pages</a>',
				);

			}

			return (object) $response;
		}


		public function addSnippets($fields, $pageid) {

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
						'name',
						'url',
						'nav_location',
						'id');

			if($fields['file_upload_list'])
			foreach ($fields['file_upload_list'] as $key => $file) {
				if(!empty($file['name'])) {
					$Ufiles[] = $key;
				}
			}

			// if(count($Ufiles) > 0) {
			// 	foreach ($this->getSnippets($pageid) as $key => $file) {
			// 		foreach ($Ufiles as $ff) {
			// 			if(preg_match('/'.$file->name.'/i', $ff)) {
			// 				if (!preg_match('/html/i', $file->name) && preg_match('/dyn/i', $file->value)){
			// 					unlink(MIGHTY_CONFIG_DIR.$file->value);
			// 				}
			// 			}
			// 		}
			// 	}
			// } else {
			// 	foreach ($this->getSnippets($pageid) as $key => $file) {
			// 		if (!preg_match('/html/i', $file->name) && preg_match('/dyn/i', $file->value)){
			// 			if(!array_key_exists($file->name, $fields)) {
			// 				unlink(MIGHTY_CONFIG_DIR.$file->value);
			// 			}
			// 		}
			// 	}
			// }
				if($this->getSnippets($pageid))
				foreach ($this->getSnippets($pageid) as $key => $file) {
					if(count($Ufiles) > 0) {
						foreach ($Ufiles as $ff) {
							if(preg_match('/'.$file->name.'/i', $ff)) {
								if (!preg_match('/html/i', $file->name) && preg_match('/dyn/i', $file->value)){
									@unlink(MIGHTY_CONFIG_DIR.$file->value);

									$thumb = explode('.', $file->value);
									$thumb = str_replace('.'.end($thumb), '_thumb.'.end($thumb), $file->value);
									@unlink(MIGHTY_CONFIG_DIR.$thumb);
								}
							}
						}
					}

					if (!preg_match('/html/i', $file->name) && preg_match('/dyn/i', $file->value)){
						if(!array_key_exists($file->name, $fields)) {
							@unlink(MIGHTY_CONFIG_DIR.$file->value);

							$thumb = explode('.', $file->value);
							$thumb = str_replace('.'.end($thumb), '_thumb.'.end($thumb), $file->value);
							@unlink(MIGHTY_CONFIG_DIR.$thumb);
						}
					}
				}

			// pocket::Debug($Ufiles);
			// pocket::Debug($Sfiles);
			// pocket::Debug($fields);

			// Delete existing snippets
			DBi::query("
				DELETE FROM 
					`page_snippets` 
				WHERE 
					`pageid` = '$pageid'
			");

			foreach ($fields as $key => $val){
							
				if (!preg_match('/^('.implode('|', $ignore_fields).')$/si', $key) & !empty($val)){

					DBi::query("INSERT INTO 
									`page_snippets` 
								SET 
									`pageid`  = '".$pageid."',
									`name` = '".dbi::mysqli()->real_escape_string($key)."',
									`value` = '".dbi::mysqli()->real_escape_string($val)."'
						");
					
					$parser = new Markdown;
					DBi::query("INSERT INTO 
									`page_snippets` 
								SET 
									`pageid`  = '".$pageid."',
									`name` = '".dbi::mysqli()->real_escape_string($key)."_html',
									`value` = '".dbi::mysqli()->real_escape_string($parser->transform($val))."'
						");

				}
			}

		}

		public function delete() {
			extract($_POST);

			$page = DBi::getRow("SELECT `name` FROM `pages` WHERE id = '$id'");

			if($page->name) {
				// Get snippets to delete images and delete them
				$files = $this->getSnippets($id);
				if($files) {
					foreach ($files as $file) {
						if (!preg_match('/html/i', $file->name) && preg_match('/dyn/i', $file->value) && !empty($file->value)){
							@unlink(MIGHTY_CONFIG_DIR.$file->value);

							$thumb = explode('.', $file->value);
							$thumb = str_replace('.'.end($thumb), '_thumb.'.end($thumb), $file->value);
							@unlink(MIGHTY_CONFIG_DIR.$thumb);
						}
					}
				}

				DBi::query("DELETE FROM `pages` WHERE `id` = '$id'");

				Mighty::activities()->log('deleted a page ('.stripslashes($page->name).')', 'delete');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> '<strong>"'.stripslashes($page->name).'"</strong> has been Deleted.',
				);
			} else {
				$response['ui_alert'] = (object) array(
					'type' 		=> 'failed',
					'heading' 	=> 'Failed!',
					'message' 	=> 'Page cannot be deleted.',
				);
			}

			return (object) $response;
		}

		public function publish() {
			extract($_POST);
			$currentPublished = DBi::getRow("SELECT published, name FROM pages WHERE id = '$id'");
			$published = ($currentPublished->published == '0') ? 1 : 0;
			DBi::query("UPDATE `pages` SET `published` = '$published' WHERE `id` = '$id'");

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

		public function sort(){
			$order = $_POST['ids'];
			$position = 0;
			foreach ($order as $id) {
			    $position = $position + 1;
			    DBi::query("UPDATE `pages` SET `position` = '$position' WHERE `id` = '$id'");
			}

			Mighty::activities()->log('updated the site structure.', 'sort');
				
		}

		public function getPage($id) {
			return DBi::getRow("SELECT 
									id,
									id as pid,
									name,
									url,
									template,
									nav_location,
									deletable,
									editable,
									publishable,
									subpagable,
									published,
									position 
								FROM 
									`pages` 
								WHERE 
									`id` = $id");
		}

		public function getField($name, $id){
			$field = DBi::getRow("SELECT * FROM `page_snippets` WHERE `pageid` = '$id' AND `name` = '$name'");
			return stripslashes($field->value);
		}

		public function getSnippets($id){
			$fields = DBi::getAll("SELECT * FROM `page_snippets` WHERE `pageid` = '$id'");
			return $fields;
		}
		
		/** 
		* Field variations
		*/

		public function concatenateFieldVariations($fields){

			// List of possible field variations
			$concatenations =  array(
				(object) array(
					'field_name_separator' => '_checkbox_variation_',
					'regex' => '.*_checkbox_variation_[0-9]+'
				),
				(object) array(
					'field_name_separator' => '_select_variation_',
					'regex' => '.*_select_variation_[0-9]+'
				)
			);

			$concat_array = array();

			foreach ($concatenations as $concat){

				foreach ($fields as $key => $val){

					if (preg_match('/^('.$concat->regex.')$/si', $key, $matches)){
						$field_name = explode($concat->field_name_separator, $key);
						$field_name = $field_name[0];
						$concat_array[$field_name][] = $val;
						unset($fields[$matches[0]]);
					}

				}

			}

			// add concatenations to field list
			foreach($concat_array as $key => $val){
				$fields[$key] = implode(',', $val);
			}

			return $fields;
			
		}


		/**
		 * Parse tempalte variations
		 */

		public function saveTemplateVariations($fields){

			/**
			 * This method really needs splitting down into cunks, there are far to many points of failure.
			 */

			$object = array();

			foreach ($fields as $key => $val){

				if (!preg_match('/^template_variation_.*$/', $key)){
					unset($fields[$key]);
				} else {

					// There may be multiple variation tables check for a parent attribute
					preg_match('/^template_variation_(.*?)_END.*$/', $key, $matches);
					$parent = $matches[1];

					// Find source table
					if (preg_match('/_data_table/', $key)){
						$object[$parent]['data_table'] = $val;
						unset($fields[$key]);
					}

					// Find source table pk
					if (preg_match('/_data_pk_field/', $key)){
						$object[$parent]['data_pk_field'] = $val;
						unset($fields[$key]);
					}

					// Remove file upload items
					if (preg_match('/_file_upload_item/', $key)){
						unset($fields[$key]);
					}

					// Find position field
					if (preg_match('/_data_position_field/', $key)){
						$object[$parent]['data_position_field'] = $val;
						unset($fields[$key]);
					}

					// Variation link field
					if (preg_match('/_data_link_field/', $key)){
						$object[$parent]['data_link_field'] = $val;
						unset($fields[$key]);
					}

					if (preg_match('/_data_link_field_key/', $key)){
						$object[$parent]['data_link_field_key'] = $val;
						unset($fields[$key]);
					}

					// Variation link value
					if (preg_match('/_data_link_value/', $key)){
						$object[$parent]['data_link_value'] = $val;
						unset($fields[$key]);
					}

					// Find new fields
					if (preg_match('/_new([0-9]+)_/', $key, $matches)){
						preg_match('/_new[0-9]+_(.*?)_$/', $key, $column);
						if ($column[1]){
							$object[$parent]['new_fields'][$matches[1]][$column[1]] = addslashes($val);
						}
					}

				}

			} 

			// The remaining $fields should only contin fields for variations
			foreach($fields as $key => $val){

				// Which variation set to add the field to
				preg_match('/^template_variation_(.*?)_END.*$/', $key, $matches);
				$parent = $matches[1];				

				// Get the field name
				preg_match('/^template_variation_.*_END_(.*?)_[0-9]+.*$/', $key, $matches);
				$field_name = $matches[1];	

				// Get the row pk
				preg_match('/^template_variation_.*_END_.*_(.*?)$/', $key, $matches);
				$pk = $matches[1];			

				if (is_numeric($pk)){
					$object[$parent]['fields'][$pk][$field_name] = $val; 
				}

			}


			foreach ($object as $field => $table){

				$position = 0;

				$table_fields = array();

				$table_info = DBi::getAll("DESCRIBE `".$table['data_table']."`");
				
				if($table_info)
				foreach ($table_info as $f) {
					if (!$f->Key){
						$table_fields[$f->Field] = '';
					}
				}

				if (isset($table['data_link_field'])){
					if (count($table['new_fields']) > 0){
						foreach ($table['new_fields'] as $key => $val){
							$link = array($table['data_link_field']=>$table['data_link_value']);
							if ($table['data_link_field_key']){
								$link = array($table['data_link_field_key']=>$table['data_link_value']);
							}
							$table['new_fields'][$key] = array_merge($link, $val);
						}
					}
					$link_field = "WHERE `".$table['data_link_field']."` = '".$table['data_link_value']."'";
					if ($table['data_link_field_key']){
						$link_field = "WHERE `".$table['data_link_field_key']."` = '".$table['data_link_value']."'";
					}
				}

				/**
				 *  Rows to be deleted 
				 */

				$table_rows = DBi::getAll("SELECT `".$table['data_pk_field']."` FROM `".$table['data_table']."` $link_field");



				if (is_array($table_rows)){
					foreach ($table_rows as $id){
						$to_delete[$id->id] = $id->id; 
					}
				}

				if (is_array($to_delete)){
					if (is_array($table['fields'])){
						foreach($table['fields'] as $key => $val){
							unset($to_delete[$key]);
						}
					}
				}

				if (count($to_delete) > 0){
					DBi::query("DELETE FROM `".$table['data_table']."` WHERE `".$table['data_pk_field']."` IN (".implode(',', $to_delete).")");
				}

				// Add rows				
				if (is_array($table['new_fields'])){
					foreach ($table['new_fields'] as $c => $r){
						$new_row_fields = array_merge($table_fields, $r);
						$query = "INSERT INTO `".$table['data_table']."` (".$table['data_pk_field'].", ".implode(', ', array_keys($table_fields)).") VALUES (NULL, '".implode('\', \'', $new_row_fields)."')";
						DBi::query($query);
					}
				}

				if (is_array($table['fields'])){

					foreach ($table['fields'] as $pk => $fields){

						$fields = array_merge($table_fields, $fields);

						if (is_numeric($pk) && $pk != '0'){

							$position++;

							if ($table['data_position_field']){
								$item_position = ",`".$table['data_position_field']."` = '$position'";
							} else {
								$item_position = '';
							}

							// If a link field key is present we'll need to make sure we set the value
							if ($table['data_link_field_key']){
								$fields[trim($table['data_link_field_key'])] = $table['data_link_value'];
							}

							$set = array();
							foreach (array_keys($fields) as $key){
								if ($key != $table['data_link_field']){
									$set[] = "`$key` = '".addslashes($fields[$key])."'";
								}
							}

							$query = "
								UPDATE `".$table['data_table']."`
								SET ".implode(',', $set).$item_position."
								WHERE `".$table['data_pk_field']."` = '".$pk."'
							";

							DBi::query($query);
							
						}

					}
				}

			}

			return $object;

		}

		public static function permissions() {
			return Mighty::Users()->getPermissions();
		}


	}

?>
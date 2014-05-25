<?php

	class Mighty_Snippets {

		public function __construct() {
		
		}
		
		public function getAll() {
			$data = DBi::getAll('SELECT * FROM `snippets`');

			if($data)
			return $data;
		}

		public function getSnippet($id) {
			$snippets = DBi::getRow("SELECT 
										id,
										id as sid,
										description,
										name,
										selector,
										template
									FROM 
										`snippets` 
									WHERE 
										`id` = $id");

				$snippets_data = DBi::getAll("
					SELECT * 
					FROM `snippets_data`
					WHERE `snippetsid` = '$id'
				");

				if (is_array($snippets_data)){
					foreach ($snippets_data as $snippet){
						$fields[$snippet->name] = stripslashes($snippet->value);
					}
					$snippets->fields = (object) $fields;
				}

				return (object) $snippets;
		}


		public function editSnippet() {
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

			    $this->addSnippets($fields, $id);

				Mighty::activities()->log('updated ('.stripslashes($name).') snippets', 'update');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> 'Your changes have been saved. <a href="/mightycms/snippets">Return to Snippets/Settings</a>',
				);
			}

			return (object) $response;

		}


		public function addSnippets($fields, $snippetsid) {

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
						'id',
						'date',
						'action');

			// Delete existing snippets
			DBi::query("
				DELETE FROM 
					`snippets_data` 
				WHERE 
					`snippetsid` = '$snippetsid'
			");

			foreach ($fields as $key => $val){
							
				if (!preg_match('/^('.implode('|', $ignore_fields).')$/si', $key) & !empty($val)){

					DBi::query("INSERT INTO 
									`snippets_data` 
								SET 
									`snippetsid`  = '".$snippetsid."',
									`name` = '".dbi::mysqli()->real_escape_string($key)."',
									`value` = '".dbi::mysqli()->real_escape_string($val)."'
						");

					$parser = new Markdown;
					DBi::query("INSERT INTO 
									`snippets_data` 
								SET 
									`snippetsid`  = '".$snippetsid."',
									`name` = '".dbi::mysqli()->real_escape_string($key)."_html',
									`value` = '".dbi::mysqli()->real_escape_string($parser->transform($val))."'
						");

				}
			}

		}


	}

?>
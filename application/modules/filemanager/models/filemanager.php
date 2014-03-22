<?php

	class Filemanager {

		public function __construct() {
		
		}
		
		public static function getAll() {
			$data = DBi::getAll('SELECT * FROM `filemanager` ORDER BY id DESC');

			if($data)
			return $data;
		}

		public function upload() {
			
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
			if($fields['files']) {
				$id = DBi::query("INSERT INTO 
					    				`filemanager` 
					    			SET 
					    				`url` 			= '".dbi::mysqli()->real_escape_string($fields['files'])."'
					    			");


				Mighty::activities()->log('added a new file ('.stripslashes($fields['files']).')', 'add');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> 'New file has been added.'
				);
			} else {
				$response['ui_alert'] = (object) array(
					'type' 		=> 'failed',
					'heading' 	=> 'Failed!',
					'message' 	=> 'The file could not be uploaded.'
				);
			}

			return (object) $response;
		}

		public function delete() {
			extract($_POST);
			$file = DBi::getRow("SELECT `url` FROM `filemanager` WHERE id = '$id'");

			DBi::query("DELETE FROM `filemanager` WHERE `id` = '$id'");

			Mighty::activities()->log('deleted a file ('.stripslashes($file->url).')', 'delete');
			$response['ui_alert'] = (object) array(
				'type' 		=> 'success',
				'heading' 	=> 'Success!',
				'message' 	=> '<strong>"'.stripslashes($file->url).'"</strong> has been Deleted.',
			);

			@unlink(MIGHTY_CONFIG_DIR.$file->url);

			return (object) $response;
		}


	}

?>
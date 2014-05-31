<?php

	class Mighty_Upload {

		/**
		 * File upload
		 */

		public function uploadTemplateFiles($files, $fields){

			$response = array();

			if (is_array($files) && count($files) > 0){

				foreach ($files as $key => $file){

					// Unset the upload var a file not actually get uploaded
					if ($upload){
						unset($upload);
					}

					// Set options for the particular file
					$opts = array();
					foreach ($fields as $option_key => $option_val){

						$base_name = explode('_file_upload_item', $key);
						$base_name = $base_name[0];

						if (preg_match('/^'.$base_name.'_option_([a-z_]+)/si', $option_key, $match)){
							$opts[$match[1]] = $option_val;
						}
					}

					$opts['field_name'] = $key;

					// Upload the file
					if (!$file['error'] > 0){

						$upload = $this->upload($file, $opts);

						// Record any upload errors
						if (!$upload->result){
							$response['file_upload_errors'][] = $upload->error;
						} 

					} 

					// If a file was not uploaded, use existing name
					if (!isset($upload)){
						$upload['field_name'] = $key;
						$upload['file']['path'] = $fields[$key];
						$upload = json_decode(json_encode($upload));
					}

					if ($upload->file->path){
						echo $upload_response->field_name;
						if (preg_match('/_file_upload_item/si', $upload->field_name)){
							$variation_field = explode('_file_upload_item', $upload->field_name);
							$variation_field = $variation_field[0];
							$variations[$variation_field][] = $upload->file->path;
						}
					}

				}

				if (count($variations) > 0){
					foreach ($variations as $key => $val){
						$fields[$key] = implode(',', $val);
					}
				}

				$response['fields'] = $fields;
				
			}

			if (isset($response['file_upload_errors'])){
				$response['result'] = false;
			} else {
				$response['result'] = true;
			}

			return (object) $response;

		}

		public function upload($file, $opts=''){

			// Set response object
			$response = array();

			// Set upload options
			$options = $this->setUploadOptions($opts);

			// Get file path info
			$path_info = $this->getUploadedFileInfo($file['name']);

			// Get file extension
			$extension = $path_info->extension;

			// Path info
			$filename = $path_info->filename;

			// Check upload dir exists, if not create it
			if (!is_dir($options->upload_dir)){
				mkdir($options->upload_dir, 0777, true);
			}

			// Check if file within max_file_size
			if ($file['size'] > $options->max_file_size){
				$response['error'] = 'File '.$file['name'].' exceeded the maximum allowed file size of: '.number_format(($options->max_file_size/1048576), 1).'MB';
			}

			// Check the file is a valid type
			if (is_array($options->allowed_file_types)){
				if (!in_array($extension, $options->allowed_file_types)){
					$response['error'] = 'Only the following extensions are allowed ('.implode(', ', $options->allowed_file_types).') You uploaded a '.$extension;
 				}
			}

			// If there is no errors upload/move the file
			if (!isset($response['error'])){

				// Set the input file (tmp file path)
				$input_file = $file['tmp_name'];

				// Urlify the output file 
				$output_file = $options->upload_dir.'/'.Mighty_Utilities::urlify($filename).'.'.strtolower($extension);

				// Upload 
				$upload_response = $this->moveUploadedFile($input_file, $output_file, $options->overwrite_existing_file);

				/*
				 * Upload and crop thumbnail
				 */

				if($options->thumb) {

					$thumb = explode('x', $options->thumb);

					if(count($thumb) > 1) {
						$thumbWidth 	= $thumb[0];
						$thumbHeight 	= $thumb[1];
						
						list($img_width, $img_height) = getimagesize($upload_response->details->path);

						// Only resize and crop the image if it is not the desired size
						$resizeObj = new resize($upload_response->details->path);
						$resizeObj->resizeImage($thumbWidth, $thumbHeight, 'crop');

						$uploadP = str_replace('.'.$upload_response->details->extension, '_thumb.'.$upload_response->details->extension, $upload_response->details->path);
						$resizeObj->saveImage($uploadP, 100);
					}

				}

	

				// Resize the image if width and height is set
				if ($options->width && $options->height){
					
					// Get image attributes
					list($img_width, $img_height) = getimagesize($upload_response->details->path);

					// Only resize and crop the image if it is not the desired size
					if ($img_width > $options->width && $img_height > $options->height){
						$resizeObj = new resize($upload_response->details->path);
						$resizeObj->resizeImage($options->width, $options->height, 'crop');
						$resizeObj->saveImage($upload_response->details->path, 100);
					}
					// No upload response given - how do we know if it was successful?
					// Refactor the upload and resize process
				}

				// Resize the image by width only 
				if ($options->width){
					
					// Get image attributes
					list($img_width, $img_height) = getimagesize($upload_response->details->path);

					// Only resize and crop the image if it is not the desired size
					if ($img_width > $options->width){
						$resizeObj = new resize($upload_response->details->path);
						$resizeObj->resizeImage($options->width, $resizeObj->getSizeByFixedWidth($options->width), 'crop');
						$resizeObj->saveImage($upload_response->details->path, 100);
					}
					// No upload response given - how do we know if it was successful?
					// Refactor the upload and resize process
				}

				if (!$upload_response->result){

					$response['result'] = false;
					$response['error'] = 'Could not move uploaded file';

				} else {

					// Add uploadad file details to the response
					foreach ($upload_response->details as $key => $val){
						$val = explode('/', $val);
						$response['file'][$key] = '/'.$val[count($val)-2].'/'.end($val);
					}

					// Return a field name. The field name is the name attribute set on the file input when the file was uploaded
					if ($options->field_name){
						$response['field_name'] = $options->field_name;
					}

				}

			}

			if (isset($response['error'])){
				$response['result'] = false;
			} else {
				$response['result'] = true;
			}

			return json_decode(json_encode($response));

		}

		protected function moveUploadedFile($input, $output, $overwrite){

			$response = array();

			$file = $this->getUploadedFileInfo($output);

			$filename = $file->filename;

			// Check if the file shoud be overwritten or not
			if ($overwrite == 'false'){
				if (is_file($output)){
					$filename = $file->filename.'-'.substr(md5(time()), 0, 4);
					$output = $file->dirname.'/'.$filename.'.'.$file->extension;
				}
			}

			// Upload the file
			if (move_uploaded_file($input, $output)){
				$response['result'] = true;
				$response['details'] = (object) array(
					'full_name' => $filename.'.'.$file->extension,
					'name'      => $filename,
					'extension' => $file->extension,
					'path'      => $file->dirname.'/'.$filename.'.'.$file->extension
				);
			} else {
				$response['result'] = false;
			}

			return (object) $response;

		}

		protected function getUploadedFileInfo($file){
			return (object) pathinfo($file);
		}

		protected function setUploadOptions($opts=''){

			// Set default options
			$options = array(
				'upload_dir'              => 'dyn',
				'max_file_size'           => 100,
				'allowed_file_types'      => '',
				'overwrite_existing_file' => 'false',
				'thumb'					  => ''
			);

			// If options is set, overide defaults options
			if (is_array($opts)){
				$options = array_merge($options, $opts);
			}

			// Convert file types to an array
			if ($options['allowed_file_types'] != ''){
				$options['allowed_file_types'] = explode(',', $options['allowed_file_types']);
			}

			// Set the max file size in bytes
			$options['max_file_size'] = $options['max_file_size'] * 1048576;

			// Convert options to an object
			$options = json_decode(json_encode($options));

			return $options;

		}

		/////////// End file upload
	}

?>
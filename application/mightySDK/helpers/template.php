<?php

	class Template {
		
		public function __construct($template, $templates_dir = ''){

			if (empty($templates_dir)){
				$templates_dir = MIGHTY_CONFIG_DIR.'/templates/';
			}

			$this->templates_dir = $templates_dir;
			$this->loadTemplate($this->templates_dir.$template);
			
		}

		public function getSections(){

			$sections = array();

			if (!is_array($this->json->section)){
				$section = $this->json->section;
				unset($this->json->section);
				$this->json->section[] = $section;
			}

			foreach ($this->json->section as $s){
				$sections[] = (object) $s->attr;
			}

			return  $sections;

		}

		public function getSection($url){

			// If only one section is found still return an array of items
			if (!is_array($this->json->section)){
				$section = $this->json->section;
				unset($this->json->section);
				$this->json->section[] = $section;
			}

			$section = $this->json->section[$this->findSection($url)];

			// If only one input is found still return an array of items
			if (!$section->input){
				$section->input[] = $section->{0};
				unset($section->{0});
			}

			// If only one input is found add to attr object
			if (!is_array($section->input)){
				$input_data = $section->input;
				unset($section->input);
				$section->input[] = $input_data;
			}

			return $section;
		}

		public function buildInput($input, $value='', $options=''){

			$content = '';
			$field_name = ($input->attr->variation_name ? $input->attr->variation_name : $input->attr->name);

			if (isset($input->attr->label)){
				$content .= '<label for="'.$field_name.'" '.($input->attr->is_required == 'true' ? 'data-title="*"' : '').' >'.$input->attr->label.'</label>';
			}

			switch ($input->attr->type){
				case 'hidden':

					if (!$value){
						$value = $input->attr->value;
					}

					$content .= '<input value="'.$value.'" type="hidden" name="'.$field_name.'" id="'.$field_name.'" />';

				break;

				case 'text':
				case 'url':

					$options = array(
						'is_required',
						'urlify',
						'urlify_field',
						'maxlength'
					);

					$content .= '<input value="'.$value.'" type="text" name="'.$field_name.'" id="'.$field_name.'" '.$this->applyInputOptions($options, $input->attr).'/>';

				break;

				case 'date': 

					$options = array(
						'is_required',
						'maxlength'
					);

					if ($value){
						$value = explode ('-',$value);
						
						if(count($value) == 3) {
							$value = $value[2].'/'.$value[1].'/'.$value[0];
						}
					} else {
						$value = date('d/m/Y');
					}

					$content .= '<input value="'.$value.'" type="text" name="'.$field_name.'" id="'.$field_name.'" '.$this->applyInputOptions($options, $input->attr).'/>';

					$content .= '<script>
								$(function() {
									$("#'.$field_name.'").datepicker({
										showButtonPanel: true,
										"dateFormat": "dd/mm/yy"
									});
								});
								</script>';

				break;

				case 'db_date': 

					$options = array(
						'is_required',
						'maxlength'
					);

					if (!$value){
						$value = date('Y-m-d');
					}

					$content .= '<input value="'.$value.'" type="text" name="'.$field_name.'" id="'.$field_name.'" '.$this->applyInputOptions($options, $input->attr).'/>';

				break;

				case 'password': 

					$options = array(
						'is_required',
						'maxlength'
					);

					$content .= '<input type="password" name="'.$field_name.'" id="'.$field_name.'" '.$this->applyInputOptions($options, $input->attr).'/>';

				break;

				case 'textarea': 

					$options = array(
						'is_required',
						'maxlength'
					);

					// Ability to set a height for textarea inputs
					if ($input->attr->height){
						$height = $input->attr->height;
					} else {
						$height = '350';
					}

					$content .= '<textarea style="height:'.$height.'px" cols="" rows="" name="'.$field_name.'" id="'.$field_name.'" '.$this->applyInputOptions($options, $input->attr).'>'.$value.'</textarea>';

				break;

				case 'file': 

					$options = array(
						'max_file_size',
						'allowed_file_types',
						'upload_dir',
						'thumb',
						'variations',
						'overwrite_existing_file',
						'display',
						'width',
						'height',
						'min-height',
						'prefix'
					);

					// Set default display option
					if (!$input->attr->display){
						$input->attr->display = 'list';
					}

					// Options that will determine the file containers style
					$container_options = trim($input->attr->display.' '.($input->attr->variations == 'true' ? 'variations' : 'single'));

					$content .= '<section class="uim_file_list '.$container_options.'">';

					$content .= '<ul data-field="'.$field_name.'">';

						if ($value){

							$variations = explode(',', $value);

							foreach ($variations as $key => $variation){

									$imageInfo = @getimagesize(MIGHTY_CONFIG_DIR.$variation);

									$content .= '<li style="';
									foreach($options as $option){
										$height = ($option == 'height') ? 'min-' : '';
										$content .= ( ($option == 'height' && $input->attr->{$option} ) || ($option == 'width' && $input->attr->{$option}) ) ? $height.$option.':'.$input->attr->{$option}.'px;' : "";
									}
									if($imageInfo[1]) {
										$content .= 'min-height:'.$imageInfo[1].'px;';
									}
									$content .= '" ';
									foreach($options as $option){
										$content .= ( ($option == 'height' && $input->attr->{$option} ) || ($option == 'width' && $input->attr->{$option}) ) ? $option.'="'.$input->attr->{$option}.'px" ' : "";
									}
									$content .= '>';
										$content .= '<a href="'.$variation.'" target="_blank">';
											$content .= '<section class="existing_file" style="background-image:url('.$variation.');background-color:#fff; ';
											foreach($options as $option){
												$height = ($option == 'height') ? 'min-' : '';
												$content .= ( ($option == 'height' && $input->attr->{$option} ) || ($option == 'width' && $input->attr->{$option}) ) ? $height.$option.':'.$input->attr->{$option}.'px;' : "";
											}
											if($imageInfo[1]) {
												$content .= 'min-height:'.$imageInfo[1].'px;';
											}
											$content .= '">';

										switch($input->attr->display){
											case 'image':												
												$content .= '<input type="hidden" name="'.$field_name.'_file_upload_item_'.$key.'" value="'.$variation.'" />';
											break;
											case 'list':
											default:
												$content .= '<a href="'.$variation.'" target="_blank">'.$variation.'</a> ';
												$content .= '<input type="hidden" name="'.$field_name.'_file_upload_item_'.$key.'" value="'.$variation.'" />';
											break;
										}

									$content .= '</section>';
									$content .=	'</a> ';
									$content .= '<span class="uim-button red remove delete">Remove</span>';
									$content .= '<input class="file" type="file" name="'.$field_name.'_file_upload_item_'.$key.'" id="'.$field_name.'" />';
								$content .= '</li>';

							}

						} else {

							$content .= '<li style="';
							foreach($options as $option){
								$height = ($option == 'height') ? 'min-' : '';
								$content .= ( ($option == 'height' && $input->attr->{$option} ) || ($option == 'width' && $input->attr->{$option}) ) ? $height.$option.':'.$input->attr->{$option}.'px;' : "";
							}
							$content .= '" ';
							foreach($options as $option){
								$content .= ( ($option == 'height' && $input->attr->{$option} ) || ($option == 'width' && $input->attr->{$option}) ) ? $option.'="'.$input->attr->{$option}.'px" ' : "";
							}
							$content .= '>';


								$content .= '<section class="existing_file" style="';
								foreach($options as $option){
									$height = ($option == 'height') ? 'min-' : '';
									$content .= ( ($option == 'height' && $input->attr->{$option} ) || ($option == 'width' && $input->attr->{$option}) ) ? $height.$option.':'.$input->attr->{$option}.'px;' : "";
								}
								$content .= '">';
								$content .= '</section>';

								$content .= '<input class="file" type="file" name="'.$field_name.'_file_upload_item" id="'.$field_name.'" />';
							$content .= '</li>';

						}

					$content .= '</ul>';

						// Variation button
						if ($input->attr->variations == 'true'){
							$content .= '<section class="add_variation_button">';
								$content .= '<span class="button ui_action_button add_variation">';
									// $content .= '<span class="icons-save-page"></span>';
	                    			$content .= '<span class="uim-button green add-new-file">Add another</span>';
								$content .= '</span>';
							$content .= '</section>';
						}

					foreach($options as $option){
						if ($input->attr->{$option}){
							$content .= '<input type="hidden" name="'.$field_name.'_option_'.$option.'" value="'.$input->attr->{$option}.'" />';
						}
					}

					$content .= '</section>';

				break;
				
				case 'variations':

					$variations = $input;

					// Get variation table options
					$data_table = $variations->attr->data_table;
					$data_list_name_field = $variations->attr->data_list_name_field;
					$data_pk_field = $variations->attr->data_pk_field;
					$data_position_field = $variations->attr->data_position_field;
					$data_sort_field = $variations->attr->data_sort_field;
					$data_sort = $variations->attr->data_sort;
					$data_published_field = $variations->attr->data_published_field;
					$data_link_field = $variations->attr->data_link_field;
					$data_link_field_key = $variations->attr->data_link_field_key;

					// Only fetch variations for a specific column
					if (isset($data_link_field)){
						$link_field = "WHERE `".$data_link_field."` = '$value'";
						// It may also be a requirement to use a different column 'key' perhaps the database tables
						// use different column names for some reason
						if (isset($data_link_field_key)){
							$link_field = "WHERE `".$data_link_field_key."` = '".$value."'";
						}
					}
 
					// If input is not array create an array with one item
					if (!is_array($variations->input)){
						$i = $variations->{0};
						unset($variations->input);
						$variations->input = array($i);
					}

					// Get table rows
					$rows = DBi::getAll("
						SELECT * 
						FROM `".$data_table."` 
						$link_field
						ORDER BY `".($data_sort_field ? $data_sort_field : 'id')."` ".($data_sort ? $data_sort : 'ASC')."
					");

					if (!$variations->attr->no_add){
						$content .= '<a class="uim-button green add-new-item" href="#">Add a new item</a>';
					}

					$content .= '<section class="variations">';

						$variation_prefix = 'template_variation_'.$variations->attr->name.'_END_'; 

						$content .= '<input type="hidden" value="'.$data_table.'" name="'.$variation_prefix.'data_table" />';
						$content .= '<input type="hidden" value="'.$data_position_field.'" name="'.$variation_prefix.'data_position_field" />';
						$content .= '<input type="hidden" value="'.($data_pk_field ? $data_pk_field : 'id').'" name="'.$variation_prefix.'data_pk_field" />';

						if (isset($data_link_field_key)){
							$content .= '<input type="hidden" value="'.$data_link_field_key.'" name="'.$variation_prefix.'data_link_field_key" />';
						}

						// Link the variation to multiple pages
						if (isset($data_link_field)){
							$content .= '<input type="hidden" value="'.$data_link_field.'" name="'.$variation_prefix.'data_link_field" />';
							$content .= '<input type="hidden" value="'.$value.'" name="'.$variation_prefix.'data_link_value" />';
						}

						$content .= '<section class="template">';
							$content .= '<span class="uim-button red remove delete">Remove</span>';
							$content .= '<ul>';
								foreach ($variations->input as $field){
									$content .= '<li>';
										$field->attr->variation_name = $variation_prefix.$field->attr->name.'_'.($data_pk_field ? $row->{$data_pk_field} : $row->id);
										$content .= $this->buildInput($field);
									$content .= '</li>';
								}
							$content .= '</ul>';
						$content .= '</section>';

						if (is_array($rows)){

							foreach ($rows as $row){
								$content .= '<section class="variation">';
									$content .= '<section class="item_wrapper">';
										$content .= '<span class="name">'.$row->{$data_list_name_field}.'</span>';
										if ($data_position_field){
											$content .= '<div class="move"><span class="icon-move"></span></div>';
										}
										$content .= '<div class="collapse"><span class="icon-collapse-down icon-collapse-up"></span></div>';

										$content .= '<span class="actions">';
											if ($data_published_field){
												$content .= '<a href="#" class="uim-button published '.($row->{$data_published_field} == '1' ? 'green' : 'red').'">'.($row->{$data_published_field} == '1' ? 'Published' : 'Unpublished').'</a>';
												$content .= '<input type="hidden" value="'.$row->{$data_published_field}.'" name="'.$variation_prefix.$data_published_field.'_'.($data_pk_field ? $row->{$data_pk_field} : $row->id).'" />';
											}
											if (!$variations->attr->no_delete){
												$content .= '<a href="#" class="uim-button delete red">Delete</a>';
											}
										$content .= '</span>';

									$content .= '</section>';
									$content .= '<ul class="fields">';
										foreach ($variations->input as $field){
											$content .= '<li>';
												$field->attr->variation_name = $variation_prefix.$field->attr->name.'_'.($data_pk_field ? $row->{$data_pk_field} : $row->id);
												$content .= $this->buildInput($field, $row->{$field->attr->name});
											$content .= '</li>';
										}
									$content .= '</ul>';
								$content .= '</section>';
							}

						}

					$content .= '</section>';

				break;	
				
				case 'select': 
				case 'checkbox': 
				case 'radio': 

					$options = array(
						'data_table',
						'data_label_field',
						'data_value_field',
						'data_sort_field',
						'data_sort',
						'variations'
					);

					// Ensure that variations returns a boolean value
					if ($input->attr->variations){
						$input->attr->variations = (boolean) (in_array($input->attr->variations, array('true', '1')) ? true : false);
					}

					// Check to see if the template has defined dynamic data (loaded via database)
					if ($input->attr->data_table && $input->attr->data_label_field && $input->attr->data_value_field){

						if ($input->attr->data_filter){

							// Set the query to nothing incase something goes wrong
							$filter_query = '';
							
							// Get the filtered value
							$data_filter = $input->attr->data_filter;

							// Get keys 
							$keys = explode(',', $input->attr->data_filter);
							
							if ($keys){
								foreach ($keys as $pair){
									$pair = explode(':', $pair);
									$filter_keys[] = "`".trim($pair[0])."` = '".trim($pair[1])."'";
								}
								if (is_array($filter_keys)){
									$filter_query = " WHERE ".implode(' AND ', $filter_keys);
								}
							}

						} else {
							$filter_query = '';
						}

						$data = DBi::getAll('
							SELECT 
								`'.$input->attr->data_label_field.'` AS name,
								`'.$input->attr->data_value_field.'` AS value
							FROM  
								`'.$input->attr->data_table.'`'.
							$filter_query.
							($input->attr->data_sort_field ? 'ORDER BY `'.$input->attr->data_sort_field.'`' : '').($input->attr->data_sort_field  && $input->attr->data_sort ? ' '.$input->attr->data_sort : '')
						);

						$input->option = $data;

					// Check to see if the template has defined static data options, if not return an error
					} else if (!isset($input->option) && isset($input->{0})){
						$option[] = $input->{0};
						unset($input->{0});
						$input->option = $option;
					} else if (!isset($input->option) && !isset($input->{0})){

						throw new Exception('No static or dynamic data set for input: "'.$field_name.'"');

					}

					// Different input types will return different html
					switch ($input->attr->type) {
						case 'select':

							$content .= '<select name="'.$field_name.'" id="'.$field_name.'">';
							$content .= '<option value="" selected="selected">Select an item</option>';

							if (count($input->option) > 0){ 
								foreach ($input->option as $option){

									if (isset($data)){
										$option->attr = (object) array(
											'value' => $option->value,
											'name' => $option->name
										);
									}

									$content .= '<option '.($value == $option->attr->value && $input->attr->variations == false ? 'selected="selected"' : '').' value="'.$option->attr->value.'">'.($option->attr->name ? $option->attr->name : $option->attr->label).'</option>';

								}
							}

							$content .= '</select>';

							// Select boxes support variations
							if ($input->attr->variations == true){

								$values = array();
								foreach ($input->option as $option){
									$values[$option->value] = $option->name;
								}

								// If no value is not set, convert it to an empty array for use with the in_array function
								if (!$value && !is_array($value)){
									$value = array();
								} else {
									$value = explode(',', $value);
								}

								$content .= '<ul class="ui_select_variations">';

									$count = 0;
									foreach ($value as $val){
										$count ++;

										if (array_key_exists($val, $values)){
											$content .= '<li data-val="'.$val.'" data-text="'.$values[$val].'">'.
															'<span class="label">'.$values[$val].'</span>'.
															'<span class="select remove_variation">Remove</span>'.
                            								'<input type="hidden" name="'.$field_name.'_select_variation_'.$count.'" value="'.$val.'" />'.
														'</li>';
										}

									}

								$content .= '</ul>';

								$content .= '<span class="ui_select button add_variation">';
									$content .= '<span class="text">Add</span>';
								$content .= '</span>';

							}

						break;
						case 'radio':

							$content .= '<ul class="radio">';

							if (count($input->option) > 0){ 
								$count = 0;
								foreach ($input->option as $option){
									$count ++;

									if (isset($data)){
										$option->attr = (object) array(
											'value' => $option->value,
											'name' => $option->name
										);
									}
									$content .= '<li>';
										$content .= '<input id="'.$field_name.'_'.$count.'" name="'.$field_name.'" type="radio" '.($value == $option->attr->value ? 'checked="checked"' : '').' value="'.$option->attr->value.'" />';
										$content .= '<label for="'.$field_name.'_'.$count.'">'.($option->attr->name ? $option->attr->name : $option->attr->label).'</label>';
									$content .= '</li>';

								}
							}

							$content .= '</ul>';

						break;
						case 'checkbox':

							// A checkbox value returns a comma deliminated list of 'checked' values
							// its easier and more reliable to traverse an array over a string

							// If no value is not set, convert it to an empty array for use with the in_array function
							if (!$value && !is_array($value)){
								$value = array();
							} else {
								$value = explode(',', $value);
							}

							$content .= '<ul class="checkbox">';

							if (count($input->option) > 0){ 
								$count = 0;
								foreach ($input->option as $option){
									$count ++;

									if (isset($data)){
										$option->attr = (object) array(
											'value' => $option->value,
											'name' => $option->name
										);
									}									

									$content .= '<li>';
										$content .= '<input id="'.$field_name.'_checkbox_variation_'.$count.'" name="'.$field_name.'_checkbox_variation_'.$count.'" type="checkbox" '.(in_array($option->attr->value, $value) ? 'checked="checked"' : '').' value="'.$option->attr->value.'" />';
										$content .= '<label for="'.$field_name.'_checkbox_variation_'.$count.'">'.($option->attr->name ? $option->attr->name : $option->attr->label).'</label>';
									$content .= '</li>';

								}
							}

							$content .= '</ul>';

						break;
					}

					if (count($input->option) < 1){
						$content = false;
					} 

				break;

				default: 

					throw new Exception('Unkown input type: "'.$input->attr->type.'"');
					
				break;

			}

			return $content;

		}

		protected function applyInputOptions($options, $attr, $ignore=''){
			$return_options = '';
			if (!is_array($ignore)){
				$ignore = array();
			}
			foreach ($attr as $option => $val){
				if (in_array($option, $options)){
					if (!in_array($option, $ignore)){
						$return_options .= $option.'="'.$val.'" ';
					}
				}
			}
			return $return_options;
		}

		protected function findSection($url){
			foreach ($this->json->section as $key => $section){
				if ($section->attr->name == $url){
					return $key;
				}
			}
			throw new Exception('Could not find section: "'.$url.'" in XML');
		}

		protected function isFile($file){
			if (is_file($file)){
				return true;
			} else {
				return false;
			}
		}

		protected function loadTemplate($file){
			$this->loadXML($file);
			$this->XMLtoJSON();
			if (!$this->isReady()){
				throw new Exception('JSON or XML not loaded/malformed');
			}
		}

		protected function isReady(){
			if ((isset($this->xml) && $this->xml != '') && (isset($this->json) && $this->json != '')){
				return true;
			} else {
				return false;
			}
		}

		protected function XMLtoJSON(){
			$xml = str_replace(array("\n", "\r", "\t"), '', $this->xml);
			$xml = trim(str_replace('"', "'", $xml));
			$simpleXml = simplexml_load_string($xml);
			$json = json_encode($simpleXml);
			$json = str_replace('@attributes', 'attr', $json);
			$this->json = json_decode($json);
		}

		protected function loadXML($file){
			if ($this->isFile($file)){
				$this->xml = file_get_contents($file);
			} else {
				throw new Exception('Could not find template file: "'.$file.'"');
			}
		}

	}

?>

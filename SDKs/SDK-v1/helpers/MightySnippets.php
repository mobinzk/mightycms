<?php

	class MightySnippets {

		public function __construct(){
			
		}

		public function get($selector) {
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
										`selector` = '".$selector."'");

			$snippets_data = DBi::getAll("
				SELECT * 
				FROM `snippets_data`
				WHERE `snippetsid` = $snippets->id
			");

			if (is_array($snippets_data)){
				foreach ($snippets_data as $snippet){
					// $fields[$snippet->name] = stripslashes($snippet->value);
					$name = $snippet->name;
					$snippets->$name = stripslashes($snippet->value);
				}
				// $snippets->$fields = (object) $fields;
			}

			return (object) $snippets;

		}
	}

?>
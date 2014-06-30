<?php

	class MightyNav {

		public function __construct(){
			
		}

		public function get($location = ''){

			if($location) {
				if(is_array($location)) {
					foreach ($location as $key => $loc) {
						$nav .=  " nav_location = '$loc'";

						if($key < count($location) -1) {
							$nav .= ' or ';
						}
					}
					return DBi::getAll("SELECT * FROM `pages` WHERE $nav AND `published` = '1' ORDER BY position");
				} else {
					return DBi::getAll("SELECT * FROM `pages` WHERE `nav_location` = '$location' AND `published` = '1' ORDER BY position");
				}
			}
		}
	}

?>
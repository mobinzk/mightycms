<?php

	class MightyNav {

		public function __construct(){
			
		}

		public function get($location = ''){

			if($location)
			return DBi::getAll("SELECT * FROM `pages` WHERE `nav_location` = '$location' AND `published` = '1' ORDER BY position");
		}
	}

?>
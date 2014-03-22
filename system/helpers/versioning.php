<?php

	class Versioning {
	
		public static function auto($file_name){

		  if(!file_exists($file_name)){
				return $file_name;

		  } else {
		  		$file_time = substr(md5(filemtime($file_name)) ,0, 8);
		  		return $file_name.'?v='.$file_time;
		  		
		  }

		}
			
	}
	
?>
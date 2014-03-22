<?php

	class DBi {

		public static $options;

		public static function mysqli(){
			
			if (!Pocket::$config->database){
				throw new Exception('Database connection details must be specified to use the database object');
				die();
			}

			self::$options = Pocket::$config->database;

			return new mysqli(
				self::$options->host,
				self::$options->user,
				self::$options->pass,
				self::$options->db
			);

		}

		public static function getAll($query, $memcached = TRUE){
				
			$FDMemcached = new FDMemcached;

			if(FDMemcached::$memcached['status'] && $memcached){

				$FDget = $FDMemcached->get($query);
				if($FDget){
					$results = $FDget;
				} else {
					$db = self::mysqli();
					$result = $db->query("SET NAMES 'UTF8'");
					$result = $db->query("SET character_set_connection = 'utf8'");
					$result = $db->query($query);
					if($result) {
						while ($row = $result->fetch_object()){
							$results[] = $row;
						}
					}

					if(count($results) > 0) {
					 	$FDMemcached->set($query, $results);
					}
				}

			} else {
				$db = self::mysqli();
				$result = $db->query("SET NAMES 'UTF8'");
				$result = $db->query("SET character_set_connection = 'utf8'");
				$result = $db->query($query);
				if($result) {
					while ($row = $result->fetch_object()){
						$results[] = $row;
					}
				}
			}

			return $results; 

		}

		public static function getRow($query, $memcached = TRUE){

			$FDMemcached = new FDMemcached;

			if(FDMemcached::$memcached['status'] && $memcached){

				$FDget = $FDMemcached->get($query);
				if($FDget){
					$results = $FDget;
				} else {

					$db = self::mysqli();
					$result = $db->query("SET NAMES 'UTF8'");
					$result = $db->query("SET character_set_connection = 'utf8'");
					$result = $db->query($query);
					$result = $result->fetch_object();

					if (!$result){
						$result = false;
					} else {
						$FDMemcached->set($query, $results);
					}
				}

			} else {
				$db = self::mysqli();
				$result = $db->query("SET NAMES 'UTF8'");
				$result = $db->query("SET character_set_connection = 'utf8'");
				$result = $db->query($query);
				$result = $result->fetch_object();

				if (!$result){
					$result = false;
				} 
			}

			return $result; 

		}

		public static function query($query){
			$db = self::mysqli();

			$db->query("SET NAMES 'UTF8'");
			$db->query("SET character_set_connection = 'utf8'");
			if ($db->query($query)) {
				$result['done'] = true;
				$result['id'] = $db->insert_id;

				// Delete all chached queries
				$FDMemcached = new FDMemcached;
				$FDMemcached->delete_all();
			} else {

				if (Pocket::$config->debug == true){
					throw new Exception('There was a problem on insert statement:<br />
										Query: '.$query.'<br />
										Error: '.$db->error);
				}

				$result['done'] = false;
			}

			return $result;
		}

	}

?>
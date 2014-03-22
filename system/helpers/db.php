<?php

	class DB {

		public static $options;
		public static $mysql_connect;

		public static function mysql(){

			if (!Pocket::$config->database){
				throw new Exception('Database connection details must be specified to use the database object');
				die();
			}

			self::$options = Pocket::$config->database;
			self::$mysql_connect = mysql_connect(self::$options->host, self::$options->user, self::$options->pass);
			return mysql_select_db(self::$options->db, self::$mysql_connect);

		}

		public static function getAll($query){

			$db = self::mysql();
			$sql = mysql_query($query);
			while ($row = mysql_fetch_object($sql)){
				$results[] = $row;
			}

			return $results; 

		}

		public static function getRow($query){

			$db = self::mysql();
			$sql = mysql_query($query);
			$result = mysql_fetch_object($sql);
			return $result;

			if (!$result){
				$result = false;
			}

			return $result; 

		}

	}

?>
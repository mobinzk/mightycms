<?php 

	class FDMemcached {

		static public $memcache = NULL;
		static public $memcached = false;
		static public $expireTime = 2592000;  // Expire time in seconds
											  // 1 Week  => (60 seconds * 60 minutes * 24 hours * 7 days)  = 604800 seconds
											  // 1 Month => (60 seconds * 60 minutes * 24 hours * 30 days) = 2592000 seconds
											  // 2 Month => (60 seconds * 60 minutes * 24 hours * 60 days) = 5184000 seconds

		public function __construct(){
			
			if (class_exists('Memcache')) {

				self::$memcache = new Memcache;
			
				if(@self::$memcache->connect('localhost', 11211)){
					self::$memcached['status'] = true;
				} else {
					self::$memcached['status'] = false;
				}

			}
		}


		public function get($key){
			$key = md5($key);
			return self::$memcache->get($key);
		}

		public function set($key, $data){
			$key = md5($key);
			self::$memcache->set($key, $data, 0, self::$expireTime);
		}

		public function delete_all(){
			if(self::$memcached['status']) {
				self::$memcache->flush();
			}
		}

		

	}

?>

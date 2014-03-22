<?php 

	class Pocket {

		public static $config;

		public static function setRoute($route, $options=''){

			if (!isset($options['module'])){
				$options['module'] = 'default';
			}			

			if ($route == '/' && !isset($options['action'])){
				$options['action'] = 'index';
			}

			self::$config->routes[] = (object) array(
				'route'   => $route,
				'options' => $options,
				'action'  => ''
			);

		}

		public static function setConfig($config){

			$existing = (array) self::$config;
			$config = array_merge($existing, $config);
			self::$config = json_decode(json_encode($config));

		}

		public static function setRoutes(){
			
			/**
			 * Load the app routes file 
			 */

			include_once APP_DIR.'/routes.php';

		}

		public static function setEnvironment($server, $path=''){

			try {

				/**
				 * Load the default configuration
				 */

				$file = $path.APP_DIR.'/config/config.php';
				
				if (is_file($file)){
					
					include_once $file;
					self::setConfig($config);

				} else {
					
					throw new Exception('Could not locate default configuration file "'.$file.'"');
					
				}

				/**
				 * Check for a matching environment
				 */

				if (isset(self::$config->environments) && array_key_exists($server, self::$config->environments)) {
					
					$file = $path.APP_DIR.'/config/'.self::$config->environments->$server.'.config.php';
					
					if (is_file($file)){
						
						include_once $file;
						self::setConfig($config);

					} else {
						
						throw new Exception('Could not locate environment ('.$server.') configuration file "'.$file.'"');
						
					}

				}

				/**
				 *	Set app routes 
				 */
				
				self::setRoutes();

			} catch (Exception $e){

				echo $e->getMessage();
			
			}

		}

		public static function executeRequest(){

			/**
			 * Creates a new instance of the router to load the requested route MVC triad
			 * any Exceptions thrown in the application will be caught and handled here.
			 */

			try {
				new Router((isset($_GET['request']) ? $_GET['request'] : ''), self::$config);
			} catch (Exception $e){				
				echo $e->getMessage();
			}

		}

		public static function debug($value, $method='print'){

			/**
			 * A helper to debug arrays/variables
			 */
			
			switch ($method){
				case 'print':
					echo "<pre>";
					print_r($value);
					echo "</pre>";
				break;
				case 'dump':
					echo "<pre>";
					var_dump($value);
					echo "</pre>";
				break;
				default: 
					throw new Exception('Debug method "'.$method.'" not found.');
				break;
			}

		}

	}

?>
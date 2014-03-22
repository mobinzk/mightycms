<?php


	function sys($class){
		$file = SYSTEM_DIR.'/'.strtolower($class).'.php';
		if (is_file($file)){
			include_once $file;
		}
	}

	function module($class){
		$file = APP_DIR.'/modules/'.Router::$route->module.'/controller.php';
		if (is_file($file)){
			include_once $file;
		}
	}

	function helpers($class){
		$file = SYSTEM_DIR.'/helpers/'.strtolower($class).'.php';
		if (is_file($file)){
			include_once $file;
		}
	}

	function models($class){
		$file = APP_DIR.'/modules/'.Router::$route->module.'/models/'.strtolower($class).'.php';
		if (is_file($file)){
			include_once $file;
		} else {

			$ignore = array('.','..');
			$base = APP_DIR.'/modules';
			$dirs = scandir($base);
			foreach ($dirs as $dir){
				if (is_dir($base.'/'.$dir) && !in_array($dir, $ignore)){

					$file = $base.'/'.$dir.'/models/'.strtolower($class).'.php';
					if (is_file($file)){
						include_once $file;
					}

				}
			}

		}
	}

	spl_autoload_register('sys');
	spl_autoload_register('module');
	spl_autoload_register('helpers');
	spl_autoload_register('models');

?>
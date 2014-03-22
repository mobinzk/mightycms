<?php

	/**
	 * Debug
	 */

	$config['debug']           = true;
	
	/**
	 * Error reporting
	 */
	
	$config['display_errors']  = true;
	$config['error_reporting'] = E_ALL ^ E_NOTICE;

	/**
	 * Database connection details
	 */
	

	$environment 	= explode('.', $_SERVER['SERVER_NAME']);
	$config_url 	= MIGHTY_CONFIG_DIR.'/config/'.$environment[0].'.config.php';

	if (is_file($config_url)){
		
		$config_file = $config_url;
		include_once($config_file);
		$config['database'] = $db;
	} else {
		// echo 'Config file not exist!';
		$config_url 	= 'mighty_config/config/config.php';
		$config_file = $config_url;
		include_once($config_file);
		$config['database'] = $db;
	}

	/**
	 * Defaults
	 */
	
	$config['defaults']['view']['header'] = STATIC_DIR.'/inc/header.php';
	$config['defaults']['view']['footer'] = STATIC_DIR.'/inc/footer.php';

?>
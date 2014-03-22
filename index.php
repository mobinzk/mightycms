<?php
 	
	/**
	 * Load Pocket dependencies
	 */
	
	include_once 'system/bootstrap.php';

	/**
	 * Set current environment config file
	 */
	
	Pocket::setEnvironment($_SERVER['SERVER_NAME']);

	/**
	 * Execute request
	 */
	
	Pocket::executeRequest();

?>
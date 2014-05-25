<?php 

	// Login
	Pocket::setRoute('/', array(
		'module' => 'login',
		'action' => 'login'
	));

	Pocket::setRoute('/forgotten-password', array(
		'module' => 'login',
		'action' => 'forgotten'
	));

	Pocket::setRoute('/logout', array(
		'module' => 'login',
		'action' => 'logout'
	));

	// Dashboard
	Pocket::setRoute('/dashboard', array(
		'module' => 'dashboard',
		'action' => 'dashboard'
	));

	// Users
	Pocket::setRoute('/users/<:url>', array(
		'module' => 'users',
		'action' => 'controller'
	));
		
	Pocket::setRoute('/users', array(
		'module' => 'users',
		'action' => 'controller'
	));

	// Pages
	Pocket::setRoute('/pages/<:url>', array(
		'module' => 'pages',
		'action' => 'controller'
	));

	Pocket::setRoute('/pages', array(
		'module' => 'pages',
		'action' => 'controller'
	));

	// Pages
	Pocket::setRoute('/articles/<:url>', array(
		'module' => 'articles',
		'action' => 'controller'
	));

	Pocket::setRoute('/articles', array(
		'module' => 'articles',
		'action' => 'controller'
	));

	// Snippets
	Pocket::setRoute('/snippets/<:url>', array(
		'module' => 'snippets',
		'action' => 'controller'
	));

	Pocket::setRoute('/snippets', array(
		'module' => 'snippets',
		'action' => 'controller'
	));

	Pocket::setRoute('/file-manager', array(
		'module' => 'filemanager',
		'action' => 'filemanager'
	));

	Pocket::setRoute('/credits', array(
		'module' => 'credits',
		'action' => 'credits'
	));

	Pocket::setRoute('/markdownHTML', array(
		'module' => 'markdown',
		'action' => 'markdownHTML'
	));

	// Pocket::setRoute('/');

	// Pocket::setRoute('/<:slug>');
?>
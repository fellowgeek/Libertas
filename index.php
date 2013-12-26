<?php

	// load settings
	require_once 'settings.php';


	if($debugMode == TRUE) {
		// enable debugging
		error_reporting(E_ALL);

		// load dBug class
		require_once __PATH_TO_CORE__ . 'dbug.php';
	}

	// set error reporting to display all errors and types
	ini_set('display_errors', true);

	// starts a session in PHP
	session_set_cookie_params(0);
	session_start();

	$_SESSION["cms"]["css"] = array();
	$_SESSION["cms"]["js"] = array();

	// include ORouter
	require_once __PATH_TO_CORE__ . 'ORouter.php';

	// instatiate ORouter
	$router = new ORouter();

	// set missing path handler object
	$router->setMissingPathHandler('cms', __SELF__ . '/system/cms.php');

	// call ORouter's 'route' function
	$router->route($_SERVER['REQUEST_URI']);

?>
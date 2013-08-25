<?php

	// load settings
	require_once 'settings.php';

	// enable debugging
	error_reporting(E_ALL);

	// set error reporting to display all errors and types
	ini_set('display_errors', true);

	// starts a session in PHP
	session_set_cookie_params(0);
	session_start();

	$_SESSION["cms"]["css"] = array();
	$_SESSION["cms"]["js"] = array();


	$_SESSION["cms"]["css"] = array(
		'<link href="mystyle1.css" rel="stylesheet">',
		'<link href="mystyle2.css" rel="stylesheet">',
		'<link href="mystyle3.css" rel="stylesheet">'
	);


	$_SESSION["cms"]["js"] = array(
		'<script src="script1.js"></script>',
		'<script src="script2.js"></script>',
		'<script src="script3.js"></script>'
	);

	// function to include a PHP view script ( using output buffering )
	function include_view($filename) {
	    if(is_file($filename)) {
	        ob_start();
	        include $filename;
	        return ob_get_clean();
	    }
	    return false;
	}

	// include ORouter
	require_once __PATH_TO_CORE__ . 'ORouter.php';

	// instatiate ORouter
	$router = new ORouter();

	// set missing path handler object
	$router->setMissingPathHandler('cms', __SELF__ . '/system/cms.php');

	// call ORouter's 'route' function
	$router->route($_SERVER['REQUEST_URI']);

?>
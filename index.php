<?php

// libertas CMS


// see if a setting file exists for a given application (looks at the base path where your obray.php file exists)
require_once "settings.php";

// enable debugging
error_reporting(E_ALL);
// set error reporting to display all errors and types
ini_set("display_errors", true);

// starts a session in PHP
session_set_cookie_params(0);
session_start();

// include ORouter
require_once __PATH_TO_CORE__ . "ORouter.php";

// instatiate ORouter
$router = new ORouter();


print("HELLO");

?>
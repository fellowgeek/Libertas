<?php

/******************************************************
    GENERAL SETTINGS
******************************************************/

define('__APP__','libertas');
define('__SELF__', dirname(__FILE__).'/');   				// The should contain the path to your application
define('__PATH_TO_CORE__', dirname(__FILE__).'/core/');		// The path to obray's core files
define('__DebugMode__',TRUE);								// Enable Debug Mode

/******************************************************
    DEFINE AVAILABLE ROUTES
******************************************************/

define('__ROUTES__',
	serialize(
		array(
			"cmd" => __SELF__."lib/"
		)
	)
);

/******************************************************
    DATABASE SETTINGS
******************************************************/

define('__DBHost__','localhost');						// database server host
define('__DBPort__','3306');							// database server port
define('__DBUserName__','root');						// database username
define('__DBPassword__','t0nback23jende');				// database password
define('__DB__','libertas');							// database name
define('__DBEngine__','MyISAM');						// database engine
define('__DBCharSet__','utf8');							// database characterset (default: utf8)

/******************************************************
    User Settings
******************************************************/

define('__MAX_FAILED_LOGIN_ATTEMPTS__',10);				// The maximium allowed failed login attempts before an account is locked
?>
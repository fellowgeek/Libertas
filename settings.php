<?php

/******************************************************
    GENERAL SETTINGS
******************************************************/

// the application name
define('__APP__','libertas');
// the should contain the path to your application
define('__SELF__', dirname(__FILE__).'/');
// the path to obray's core files
define('__PATH_TO_CORE__', dirname(__FILE__).'/core/');
// enable Debug Mode
define('__DebugMode__',TRUE);

/******************************************************
    SITE SETTINGS
******************************************************/

define('__SITE__', 'libertas.erfan.me');
define('__THEME__', 'bootstrap');
define('__LAYOUT__', 'page-layout-1.html');
//define('__LAYOUT__', 'index.html');

/******************************************************
    DEFINE AVAILABLE ROUTES
******************************************************/

define('__ROUTES__',
	serialize(
		array(
			'cmd' => __SELF__ . '/',
			'sys' => __SELF__ . 'system/',
			'com' => __SELF__ . 'components/',
			// do not chnage this route
			'c' => __PATH_TO_CORE__
		)
	)
);


/******************************************************
    DATABASE SETTINGS
******************************************************/

// database server host
define('__DBHost__','localhost');
// database server port
define('__DBPort__','3306');
// database username
define('__DBUserName__','root');
// database password
define('__DBPassword__','t0nback23jende');
// database name
define('__DB__','libertas');

// do not edit these unless you know what you are doing
define('__DBEngine__','MyISAM');
define('__DBCharSet__','utf8');

/******************************************************
    User Settings
******************************************************/

// the maximium allowed failed login attempts before an account is locked
define('__MAX_FAILED_LOGIN_ATTEMPTS__', 10);

?>
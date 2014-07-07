<?php

/*
----------------------------------------------------------------------------------------------------
APPLICATION SETTINGS
----------------------------------------------------------------------------------------------------
*/

define('__SELF__', dirname(__FILE__).'/');
define('__SITE__' , 'example.com');
define('__THEME__', 'bootstrap');
define('__LAYOUT__', 'index.html');

/*
----------------------------------------------------------------------------------------------------
OBRAY CONSTANTS
----------------------------------------------------------------------------------------------------
*/

define('__OBRAY_PATH_TO_CORE__',  dirname(__FILE__) . '/core/');						// the path to obray's core files
define('__OBRAY_DEBUG_MODE__', FALSE);													// enable Debug Mode - will script database and tables if set to TRUE

/*
----------------------------------------------------------------------------------------------------
DEFINE AVAILABLE ROUTES
----------------------------------------------------------------------------------------------------
*/

define('__OBRAY_ROUTES__',serialize(array(
	'cmd' => __SELF__ . '/',
	'sys' => __SELF__ . 'system/',
	'com' => __SELF__ . 'components/',
	// do not chnage this route
	'obray' => __OBRAY_PATH_TO_CORE__
	)));

/*
----------------------------------------------------------------------------------------------------
USER SETTINGS
----------------------------------------------------------------------------------------------------
*/

define('__OBRAY_MAX_FAILED_LOGIN_ATTEMPTS__',10);										// the maximium allowed failed login attempts before an account is locked


/*
----------------------------------------------------------------------------------------------------
DATABASE SETTINGS
----------------------------------------------------------------------------------------------------
*/

define('__OBRAY_DATABASE_HOST__','localhost');											// database server host
define('__OBRAY_DATABASE_PORT__','3306');												// database server port
define('__OBRAY_DATABASE_USERNAME__','USERNAME');										// database username
define('__OBRAY_DATABASE_PASSWORD__','PASSWORD');										// database password
define('__OBRAY_DATABASE_NAME__','DATABASE');											// database name
define('__OBRAY_DATABASE_ENGINE__','MyISAM');											// database engine
define('__OBRAY_DATABASE_CHARACTER_SET__','utf8');										// database characterset (default: utf8)

define ("__OBRAY_DATATYPES__", serialize (array (
    "varchar"   =>  array("sql"=>" VARCHAR(size) COLLATE utf8_general_ci ",	"my_sql_type"=>"varchar(size)",		"validation_regex"=>""),
    "text"      =>  array("sql"=>" TEXT COLLATE utf8_general_ci ",			"my_sql_type"=>"text",				"validation_regex"=>""),
    "integer"   =>  array("sql"=>" int ",									"my_sql_type"=>"int(11)",			"validation_regex"=>"/^([0-9])*$/"),
    "float"     =>  array("sql"=>" float ",									"my_sql_type"=>"float",				"validation_regex"=>"/[0-9\.]*/"),
    "boolean"   =>  array("sql"=>" boolean ",								"my_sql_type"=>"boolean",			"validation_regex"=>""),
    "datetime"  =>  array("sql"=>" datetime ",								"my_sql_type"=>"datetime",			"validation_regex"=>""),
    "password"  =>  array("sql"=>" varchar(255) ",							"my_sql_type"=>"varchar(255)",		"validation_regex"=>"")
)));

?>
<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_NOTICE);

ini_set("arg_separator.output", "&amp;");
ini_set("display_errors", 1);
ini_set("track_errors", 1);
ini_set("error_log", 'temp/error.log');

//database settings
define("DB_HOST"    , 'localhost');
define("DB_LOGIN"   , 'ratedeck');
define("DB_PASSWORD", '');
define("DB_NAME"    , 'extractor');


define("CURRENT_DIR"  , getcwd() . DIRECTORY_SEPARATOR );   //stand-alone classes
define("CLASSES_DIR"  , CURRENT_DIR . 'classes' .  DIRECTORY_SEPARATOR);   //stand-alone classes
define("ACTIONS_DIR"  , CURRENT_DIR . 'actions' .  DIRECTORY_SEPARATOR);   //controllers processing sumbitted data and preparing output
define("TEMP_DIR",  CURRENT_DIR . 'temp' . DIRECTORY_SEPARATOR); //all uploaded files will be copied here so that they won't be deleted between requests
define("ARCHIVE_DIR",  CURRENT_DIR . 'archive' . DIRECTORY_SEPARATOR);

define('DUMP_LINES_LIMIT', 100000); //dump this many records at a time (to fit into memory_limit)


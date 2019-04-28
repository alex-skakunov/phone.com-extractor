<?php

set_time_limit(0);
error_reporting(E_ALL & ~E_NOTICE);

define("CURRENT_DIR"  , getcwd() . DIRECTORY_SEPARATOR );   //stand-alone classes
define("CLASSES_DIR"  , CURRENT_DIR . 'classes' .  DIRECTORY_SEPARATOR);   //stand-alone classes
define("ACTIONS_DIR"  , CURRENT_DIR . 'actions' .  DIRECTORY_SEPARATOR);   //controllers processing sumbitted data and preparing output
define("TEMP_DIR",  CURRENT_DIR . 'temp' . DIRECTORY_SEPARATOR); //all uploaded files will be copied here so that they won't be deleted between requests
define("ARCHIVE_DIR",  CURRENT_DIR . 'archive' . DIRECTORY_SEPARATOR);

define('DUMP_LINES_LIMIT', 100000); //dump this many records at a time (to fit into memory_limit)

define("SESSIONS_DIR", CURRENT_DIR . 'temp' . DIRECTORY_SEPARATOR . 'sessions' . DIRECTORY_SEPARATOR); //sessions are stored here
define('SESSION_TTL', 60 * 60 * 24 * 120); //120 days


include "config.php"; //load database settings, folders paths and such stuff

set_include_path( CLASSES_DIR );
require "Quick_CSV_import.class.php";
require "functions.php";
require "dBug.php";
require "MyCurl.class.php";

is_writable(TEMP_DIR) || exit ("Temporary folder must be writable: <code>".TEMP_DIR."</code>");
is_writable(SESSIONS_DIR) || exit ("Temporary folder must be writable: <code>".SESSIONS_DIR."</code>");

if ( -1 == version_compare( PHP_VERSION, '4.1.0' ) ) {
    exit ('Please, you PHP version greater than 4.1.0 - files uploads will not work properly');
}

//connect to database
$dsn = sprintf('mysql:host=%s;dbname=%s', DB_HOST, DB_NAME);
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    PDO::MYSQL_ATTR_LOCAL_INFILE => true,
); 
$db = new PDO($dsn, DB_LOGIN, DB_PASSWORD, $options);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

!empty($db) || exit("Cannot connect to database");

ini_get("file_uploads") || exit ("PHP directive [file_uploads] must be turned ON");


session_save_path(rtrim(SESSIONS_DIR, '/'));
session_start();
setcookie(session_name(),session_id(),time() + SESSION_TTL, "/");

$uploadErrors = array(
    UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
    UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
    UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
    UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
    UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.',
    -1 => 'File is empty. Try again.'
);

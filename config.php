<?php

ini_set("arg_separator.output", "&amp;");
ini_set("display_errors", 1);
ini_set("track_errors", 1);
ini_set("error_log", 'temp/error.log');
ini_set("session.use_cookies", 1);
ini_set("session.use_trans_sid", 1);
ini_set("session.gc_maxlifetime", 65535);
ini_set('auto_detect_line_endings', 1);


//database settings
define("DB_HOST"    , 'localhost');
define("DB_LOGIN"   , 'root');
define("DB_PASSWORD", '');
define("DB_NAME"    , 'extractor');
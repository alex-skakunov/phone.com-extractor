<?php

function fail($importId, $errorMessage) {
  query('UPDATE `import_stats` SET 
      `finished_at` = NOW(),
      `status` = "fail",
      `error_message` = :error_message
      WHERE id = :import_id',
      array(
          ':error_message' => $errorMessage,
          ':import_id'     =>$importId
      )
  );
  return $errorMessage;
}

function log_import_step($importId, $message) {
  query('UPDATE `import_stats` SET 
      `error_message` = :message
      WHERE id = :import_id',
      array(
          ':message'   => $message,
          ':import_id' =>$importId
      )
  );
  return $message;
}

function startImport($wayString = 'auto', $fileId = 1) {
  global $db;
  query('INSERT INTO `import_stats` (`file_id`, `started_at`, `status`, `way`) VALUES (:file_id, NOW(), "in progress", :way)',
    array(
        ':file_id' => $fileId,
        ':way'     => $wayString
    )
  );
  $importId = $db->lastInsertId();

  $remoteFileUrl = query('SELECT `value` FROM `settings` WHERE `name`="remote file' . $fileId . ' url"')->fetchColumn();

  if (empty($remoteFileUrl)) {
    return fail($importId, 'Remote file URL is not set');
  }

  // $mc = new MyCurl;
  // $info = $mc->head($remoteFileUrl);
  // if (200 != $mc->getResponseCode()) {
  //     fail($importId, 'It seems file is not found: server returned code' . $mc->getResponseCode());
  // }
  // $lastUpdated = date('d.m.Y H:i:s', $info['filetime']);

  log_import_step($importId, 'Downloading the remote file');
  $filename = tempnam(TEMP_DIR, 'zip');
  $result = file_put_contents($filename, fopen($remoteFileUrl, 'r'));
  if (false === $result) {
    return fail($importId, 'Could not copy the remote file');
  }

  $filesize = filesize($filename);
  $zip = new ZipArchive;
  if (!$zip->open($filename) === TRUE) {
    return fail($importId, 'Could not open the zip file');
  }

  $csvFilename = '';
  try {
    $csvFilename = $zip->getNameIndex(0); // 'available_numbers.csv'
    log_import_step($importId, 'Unzipping the file (' . $csvFilename . ')');
    $zip->extractTo(TEMP_DIR, array($csvFilename));
    $zip->close();
    unlink($filename);
  }
  catch(Exception $e) {
    unlink($filename);
    return fail($importId, $e->getMessage());
  }
  $importCSVFile = TEMP_DIR . $csvFilename;

  $tableName = 'import_' . time() . rand(1,100);
  
  query('CREATE TEMPORARY TABLE `' . $tableName . '`(
    `areacode` SMALLINT(3) UNSIGNED NOT NULL,
    `number` INT(7) UNSIGNED NOT NULL,
    `price` decimal(9,2) UNSIGNED DEFAULT NULL
  )');

  log_import_step($importId, 'Loading the file in the database');
  $fQuickCSV = new Quick_CSV_import($db);
  $fQuickCSV->table_name = $tableName;
  $fQuickCSV->file_name = $importCSVFile;
  $fQuickCSV->make_temporary = false;
  $fQuickCSV->use_csv_header = true;
  $fQuickCSV->table_exists = true;
  $fQuickCSV->truncate_table = false;
  $fQuickCSV->field_separate_char = ',';
  $fQuickCSV->encoding = 'utf8';
  $fQuickCSV->field_enclose_char = '"';
  $fQuickCSV->field_escape_char = '\\';
  $fQuickCSV->fields_list = array('@fullnumber', '@price');
  $fQuickCSV->parameters = array(
    'areacode' => 'SUBSTR(@fullnumber, 2, 3)',
    'number'   => 'REPLACE(REPLACE(SUBSTR(@fullnumber, 6), " ", ""), "-", "")',
    'price'    => 'IF(@price <> "None", @price, NULL)'
  );

  try {
    $fQuickCSV->import();
  }
  catch(Exception $e) {
    unlink($importCSVFile);
    return fail($importId, $e->getMessage());
  }

  query('ALTER TABLE ' . $tableName . ' ADD PRIMARY KEY (`areacode`,`number`)');

  unlink($importCSVFile);
  if (!empty($fQuickCSV->error) )
  {
    return fail($importId, $fQuickCSV->error);
  }

  $rowsCount = $fQuickCSV->rows_count;

  try {
    log_import_step($importId, 'Deleting the obsolete numbers');
    query('DELETE FROM p
           USING `phones` p
           LEFT JOIN `' . $tableName . '` n USING (`areacode`, `number`)
           WHERE n.`number` IS NULL
             AND p.file_id = ' . $fileId);

    log_import_step($importId, 'Adding the new phones');
    query('INSERT INTO `phones`
          SELECT `areacode`, `number`, ' . $fileId . ', `price`
          FROM `'.$fQuickCSV->table_name.'`
          ON DUPLICATE KEY UPDATE `price` = VALUES(`price`)');

    query('UPDATE `import_stats` SET 
        `finished_at` = NOW(),
        `status` = "success",
        `filesize` = :filesize,
        `records_number` = :records_number,
        `error_message` = NULL
        WHERE id = ' . $importId,
        array(
          ':records_number' => $rowsCount,
          ':filesize' => $filesize
        )
    );

  }
  catch(Exception $e) {
    return fail($importId, $e->getMessage());
  }

  return query('SELECT *,
    UNIX_TIMESTAMP(`started_at`) AS "started_at",
    UNIX_TIMESTAMP(`finished_at`) AS "finished_at"
    FROM `import_stats` WHERE `id` = ' . $importId)->fetch();
}


//Returns the first non-empty value in the list, or an empty line if there are no non-empty values.
function coalesce()
{ 
  for($i=0; $i < func_num_args(); $i++)
  {
    $arg = func_get_arg($i);
    if(!empty($arg))
      return $arg;
  }
  return "";
}

//go to new location (got from Fusebox4 source)
function Location($URL, $addToken = 1)
{
  $questionORamp = (strstr($URL, "?"))?"&":"?";
  $location = ( $addToken && substr($URL, 0, 7) != "http://" && defined('SID') ) ? $URL.$questionORamp.SID : $URL; //append the sessionID ($SID) by default
  //ob_end_clean(); //clear buffer, end collection of content
  if(headers_sent()) {
    print('<script type="text/javascript" type="text/javascript">( document.location.replace ) ? document.location.replace("'.$location.'") : document.location.href = "'.$location.'";</script>'."\n".'<noscript><meta http-equiv="Refresh" content="0;URL='.$location.'" /></noscript>');
  } else {
    header('Location: '.$location); //forward to another page
    exit; //end the PHP processing
  }
}

//checks that we have all modules we need or exit() will be called
function check_necessary_functions()
{ 
  for($i=0; $i < func_num_args(); $i++)
  {
    $func_name = func_get_arg($i);
    if( !function_exists($func_name) )
    {
      exit ( "Function [" . $func_name . "] is not accessable. Please check that correspondent PHP module is installed at your web-server." );
    }
  }
  return true;
}

//writes data in a file
function write_file($filename, $data)
{
  $fp = fopen($filename, 'w');
  if($fp)
  {
    fwrite($fp, $data);
    fclose($fp);
    return true;
  }
  return false;
}

//writes data in the end of a file
function append_file($filename, $data)
{
  $fp = fopen($filename, 'a');
  if($fp)
  {
    fwrite($fp, $data);
    fclose($fp);
    return true;
  }
  return false;
}

//OS independent deletion of a file
function delete_file($filename)
{
  if(file_exists($filename))
  {
    $os = php_uname();
    if(stristr($os, "indows")!==false)
      return exec("del ".$filename);
    else
      return unlink($filename);
  }
  return true;
}


//returns all fields of [tableName]
function get_table_fields($db, $tableName )
{
  $arrFields = array();
  if( empty($tableName) )
  {
    return false;
  }
  
  $db->query("SHOW TABLES LIKE '".$tableName."'");
  
  if( 0 == $db->getRowsCount())
  {
    return false;
  }
  
  $db->query("SHOW COLUMNS FROM ".$tableName);
  
  
  while( $row = mysql_fetch_array($db->fResult) )
  {
    $arrFields[] = trim( $row[0] );
  }
  
  return $arrFields;
}

function detect_line_ending($file)
{
    $s = file_get_contents($file);
    if( empty($s) ) return null;
    
    if( substr_count( $s,  "\r\n" ) ) return '\r\n'; //Win
    if( substr_count( $s,  "\r" ) )   return '\r';   //Mac
    return '\n'; //Unix
}

function startsWith( $str, $token ) {
    $_token = trim( $token );
    $_str = trim( $str );
    if( empty( $_token ) || empty( $str ) ) return false;
    
    $tokenLen = strlen( $_token );
    // $tokenFromStr = substr( $_str, 0, $tokenLen );
    // return strtolower( $_token ) == strtolower( $tokenFromStr );
    
    return !strncasecmp($_str, $token, $tokenLen );
}

function time_ago($startTimestamp, $endTimestamp=null) {
  if (empty($endTimestamp)) {
    $endTimestamp = time();
  }
  return $endTimestamp - $startTimestamp;
}

function format_time($timestamp, $precision=1) {
  if ($timestamp <= 0) {
    return '—';
  }
  if ($timestamp < 60) {
    return $timestamp . ' seconds';
  }
  return number_format($timestamp / 60, $precision) . ' minutes';
}

function format_datetime($datetime) {
  $today = date('d.m.Y');
  $yesterday = date('d.m.Y', strtotime('yesterday'));
  $givenDay = date('d.m.Y', $datetime);
  if ($givenDay == $today) {
    return 'today at ' . date('H:m', $datetime);
  }
  if ($givenDay == $yesterday) {
    return 'yesterday at ' . date('H:m', $datetime);
  }
  return date('jS \of F Y (h:i:s A)', $datetime);
}

function query($sql, $replacements=null) {
    global $db;
    $stmt = $db->prepare($sql);
    if (false === $stmt->execute($replacements)) {
      new dBug($sql);
      error_log(print_r($stmt->errorInfo(), 1));
      throw new Exception($stmt->errorInfo()[2], $stmt->errorInfo()[1]);
    }
    return $stmt;
}
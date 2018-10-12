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
    exit($errorMessage);
}

query('INSERT INTO `import_stats` (`started_at`, `status`) VALUES (NOW(), "in progress")');
$importId = $db->lastInsertId();

$remoteFileUrl = query('SELECT `value` FROM `settings` WHERE `name`="remote file url"')->fetchColumn();

if (empty($remoteFileUrl)) {
    fail($importId, 'Remote file URL is not set');
}

$filename = tempnam(TEMP_DIR, 'arc') . '.zip';
$result = file_put_contents($filename, fopen($remoteFileUrl, 'r'));
if (false === $result) {
    fail($importId, 'Could not copy the remote file');
}


$zip = new ZipArchive;
if (!$zip->open($filename) === TRUE) {
    fail($importId, 'Could not open the zip file');
}

$zip->extractTo(TEMP_DIR, array('available_numbers.csv'));
$zip->close();
unlink($filename); //remove zip
$importCSVFile = TEMP_DIR . 'available_numbers.csv';


$fQuickCSV = new Quick_CSV_import($db);
$fQuickCSV->make_temporary = true;
$fQuickCSV->file_name = $importCSVFile;
$fQuickCSV->use_csv_header = true;
$fQuickCSV->table_exists = false;
$fQuickCSV->truncate_table = false;
$fQuickCSV->field_separate_char = ',';
$fQuickCSV->encoding = 'utf8';
$fQuickCSV->field_enclose_char = '"';
$fQuickCSV->field_escape_char = '\\';

$fQuickCSV->import();
unlink($importCSVFile);
if(!empty($fQuickCSV->error) )
{
  fail($importId, $fQuickCSV->error);
}

$rowsCount = $fQuickCSV->rows_count;

try {
    query('TRUNCATE TABLE `phones`');
    query('INSERT IGNORE INTO `phones`
           SELECT `Available Phone Numbers`, SUBSTR(`Available Phone Numbers`, 2, LOCATE(")", `Available Phone Numbers`, 2)-2) AS "area_code",  `Price`
           FROM `'.$fQuickCSV->table_name.'`');

    query('UPDATE `import_stats` SET 
        `finished_at` = NOW(),
        `status` = "success",
        `error_message` = NULL
        WHERE id = ' . $importId
    );

}
catch(Exeption $e) {
    fail($importId, $e->getMessage());
}
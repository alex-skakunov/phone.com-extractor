<?php
set_time_limit(0);

$list = trim($_GET['list']);
$list = explode(',', $list);
if (empty($list)) {
    return;
}

$timestamp = date('Y-m-d-h-i-s');

$dumpFolderPath = TEMP_DIR . 'dump-' . $timestamp . DIRECTORY_SEPARATOR;
mkdir($dumpFolderPath);
$skipNumber = 0;
$count = 1;
$filesList = array();
while (true) {
    $sql = 'SELECT `areacode`, `number`, IFNULL(`price`, "X") as price
            FROM `phones`
            WHERE `areacode` IN ('.implode(',', $list).')
            LIMIT ' . $skipNumber . ',' . DUMP_LINES_LIMIT;
//    new dBug($sql);
    $stmt = query($sql);
    $recordset = $stmt->fetchAll();
    if (empty($recordset)) {
        break;
    }
    $lines = array();
    foreach ($recordset as $row) {
        $lines[] = sprintf('(%s) %s-%s,%s',
                $row['areacode'],
                substr($row['number'], 0, 3),
                substr($row['number'], 3),
                $row['price']
            );
    }
    unset($recordset);
    $filename = 'dump' . $count++ . '.csv';
    file_put_contents($dumpFolderPath . $filename, implode("\n", $lines));
    $filesList[] = $filename;
    $skipNumber += DUMP_LINES_LIMIT;
}

$zipFilename = TEMP_DIR . $timestamp . '.zip';
$zip = new ZipArchive;
if ($zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    foreach ($filesList as $filename) {
        unlink($dumpFolderPath . $filename);
    }
    rmdir($dumpFolderPath);
    exit('Could not create a zip file: ' . $zipFilename);
}

foreach ($filesList as $filename) {
    $zip->addFile($dumpFolderPath . $filename, $filename);
}
$zip->close();

foreach ($filesList as $filename) {
    unlink($dumpFolderPath . $filename);
}
rmdir($dumpFolderPath);

header('Content-Type: application/zip');
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename="' . $timestamp . '.zip"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($zipFilename));

ob_clean();
flush();
readfile($zipFilename);
unlink($zipFilename);
exit();
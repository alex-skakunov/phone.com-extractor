<?php

if (empty($_GET['file_id'])) {
  return;
}

$fileId = (int)$_GET['file_id'];

$way = strtolower(trim($_GET['way'])) == 'manual' ? 'manual' : 'auto';

// kill hanged
query("UPDATE `import_stats` SET `status`='fail', `error_message`='Hanged' WHERE `status`='in progress' AND DATE_ADD(`started_at`, INTERVAL 1 HOUR) < NOW() AND `file_id` = " . $fileId);

$isInProgress = query('SELECT `id` FROM `import_stats` WHERE status="in progress"  AND `file_id` = ' . $fileId)->fetch();
if( !empty($isInProgress)) {
    exit('Import in progress');
}

startImport($way, $fileId);
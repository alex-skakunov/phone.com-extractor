<?php

$way = strtolower(trim($_GET['way'])) == 'manual' ? 'manual' : 'auto';

$isInProgress = query('SELECT `id` FROM `import_stats` WHERE status="in progress" LIMIT 1')->fetchAll();
if( !empty($isInProgress)) {
    exit('Import in progress');
}

startImport($way, 1);
startImport($way, 2);
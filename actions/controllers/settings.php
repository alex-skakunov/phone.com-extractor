<?php


$latestImport = query('SELECT *,
    UNIX_TIMESTAMP(`started_at`) AS "started_at",
    UNIX_TIMESTAMP(`finished_at`) AS "finished_at"
    FROM `import_stats` ORDER BY `id` DESC LIMIT 1')->fetch();

$isImportInProgress = !empty($latestImport) && ('in progress' == $latestImport['status']);

$numbersCount = query('SELECT COUNT(*) FROM `phones`')->fetchColumn();
$remoteFileUrl = query('SELECT `value` FROM `settings` WHERE `name`="remote file url"')->fetchColumn();

if (empty($_POST)) {
    return;
}
    
if (!empty($_POST['erase_database'])) {
    query('TRUNCATE TABLE phones');
    $numbersCount = 0;
    $message = 'Database has been successfully erased';
}

if (!empty($_POST['remote_file_url'])) {
    $newUrl = trim($_POST['remote_file_url']);
    if (empty($newUrl)) {
        $errorMessage = 'The remote file URL should not be empty';
        return;
    }
    query('UPDATE `settings` SET `value`="' . $newUrl . '" WHERE `name`="remote file url"');
    $remoteFileUrl = $newUrl;
    $latestImport = startImport('manual'); // this can take 1-2 minutes   
}

if (!empty($_POST['user_submit'])) {
    $newPassword = trim($_POST['user_password']);
    if (empty($newPassword)) {
        $errorMessage = 'The password should not be empty';
        return;
    }
    query('UPDATE `settings` SET `value`=:password WHERE `name`="password"', array(':password' => $newPassword));
    $message = 'The password has been successfully updated';
}

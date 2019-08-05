<?php


$latestImports = array();

$numbersCount = query('SELECT COUNT(*) FROM `phones`')->fetchColumn();
$remoteFileUrls = array();
for ($i = 1; $i <= 2; $i++) {
    $remoteFileUrls[$i] = query('SELECT `value` FROM `settings` WHERE `name`="remote file'.$i.' url"')->fetchColumn();

    $latestImports[$i] = query('SELECT *,
            UNIX_TIMESTAMP(`started_at`) AS "started_at",
            UNIX_TIMESTAMP(`finished_at`) AS "finished_at"
            FROM `import_stats`
            WHERE `file_id` = ' . $i . '
            ORDER BY `id` DESC
            LIMIT 1')->fetch();
}

if (empty($_POST)) {
    return;
}

    
if (!empty($_POST['erase_database'])) {
    query('TRUNCATE TABLE phones');
    $numbersCount = 0;
    $message = 'Database has been successfully erased';
}

if (!empty($_POST['remote_file_url'])) {
    $fileId = 0;
    if (!empty($_POST['remote_file_url'][1])) {
        $fileId = 1;
    }
    elseif (!empty($_POST['remote_file_url'][2])) {
        $fileId = 2;
    }
    $newUrl = trim($_POST['remote_file_url'][$fileId]);
    if (empty($newUrl)) {
        $errorMessage = 'The remote file URL #'.$fileId.' should not be empty';
        return;
    }
    query('UPDATE `settings` SET `value`="' . $newUrl . '" WHERE `name`="remote file'.$fileId.' url"');
    $remoteFileUrls[$fileId] = $newUrl;
    $latestImports[$fileId] = startImport('manual', $fileId); // this can take 1-2 minutes   
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

<?php

if (empty($_POST)) {
  return;
}

header('Content-Type: application/json');

$url = trim($_POST['url']);
if (empty($url)) {
  header("HTTP/1.0 204 No Content");
  exit;
}

$fileId = (int)$_POST['file_id'];
if (empty($fileId)) {
  header("HTTP/1.0 204 No Content");
  exit;
}

query('UPDATE `settings` SET `value`=:url WHERE `name`=:name', [
  ':url'  => $url,
  ':name' => "remote file$fileId url",
]);
query('DELETE FROM `import_stats` WHERE `file_id`=' . $fileId);

sleep(2);

echo json_encode(['basename' => basename($url)]);
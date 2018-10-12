<?php

header('Content-Type: application/json');

$remoteFileUrl = query('SELECT `value` FROM `settings` WHERE `name`="remote file url"')->fetchColumn();
if (empty($remoteFileUrl)) {
    exit(json_encode(null));
}

$mc = new MyCurl;

$info = $mc->head($remoteFileUrl);
if (200 != $mc->getResponseCode()) {
    exit(json_encode(array('status' => 'fail', 'message' => 'It seems file is not found: server returned code' . $mc->getResponseCode())));
}

exit(
    json_encode(
        array(
            'status' => 'success', 
            'content_length'    => $info['download_content_length'],
            'last_updated'      => date('d.m.Y H:i:s', $info['filetime']),
        )
    )
);
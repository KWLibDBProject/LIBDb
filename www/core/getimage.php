<?php
require_once('core.db.php');
require_once('core.filestorage.php');

$id = isset($_GET['id']) ? $_GET['id'] : -1;

$is_downloading = isset($_SERVER['HTTP_REFERER']);

if ($id != -1) {

    $link = ConnectDB() or Die("Не удается соединиться с базой данных!");

    $file_info = FileStorage::getFileInfo($id);

    if ($file_info) {
        header ("HTTP/1.1 200 OK");
        header ("X-Powered-By: PHP/" . phpversion());
        header ("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
        header ("Cache-Control: None");
        header ("Pragma: no-cache");
        header ("Accept-Ranges: bytes");
        header ("Content-Disposition: inline; filename=\"" . $file_info['username'] . "\"");

        $ct = ($is_downloading) ? "Content-Type: application/octet-stream" : "Content-Type: {$file_info['filetype']}";
        header($ct);

        header ("Content-Length: " . $file_info['filesize']);
        header ("Age: 0");
        header ("Proxy-Connection: close");

        print(FileStorage::getFileContent($id));

        flush();
        CloseDB($link);
    } else {
        header("Content-type: image/gif");

        print(FileStorage::getEmptyIMG());

        flush();
    }
} else
{
    header("Content-type: image/gif");

    print(FileStorage::getEmptyIMG());

    flush();
};
?>
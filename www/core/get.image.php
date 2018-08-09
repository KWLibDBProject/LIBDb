<?php
require_once '__required.php'; // $mysqli_link

$id = isset($_GET['id']) ? intval($_GET['id']) : -1;

$is_downloading = isset($_SERVER['HTTP_REFERER']);

if ($id != -1) {

    // FileStorage::init($mysqli_link);
    $file_info = FileStorage::getFileInfo($id); //@todo: а если файла нет? см. ниже getFileContent()

    /* update stat_download_counter
    but only for really downloaded files, not fetched via control panel */
    if (strpos($_SERVER['HTTP_REFERER'], '/core/') == false ) {
        FileStorage::statUpdateDownloadCounter($id);
        // FileStorage::statLogDownloadEvent($id, 'Image: referer = '.$_SERVER['HTTP_REFERER']);
    }

    // FileStorage::statUpdateDownloadCounter($id);

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
    } else {
        header("Content-type: image/gif");

        print(FileStorage::getEmptyFile('image'));

        flush();
    }
} else
{
    header("Content-type: image/gif");

    print(FileStorage::getEmptyFile('image'));

    flush();
};
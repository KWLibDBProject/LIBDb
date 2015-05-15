<?php
require_once('core.db.php');
require_once('core.filestorage.php');

// сделать возможность просмотра файла как по АЙДИ, так и по ЮЗЕРНЕЙМ
$id = IsSet($_GET['id']) ? intval($_GET['id']) : Die();

$link = ConnectDB();

$file_info = FileStorage::getFileInfo($id);

if (!$file_info) {
    $current_state = "Error: unknown file id ({$id}), not found in DB!";

    $file_info['username'] = 'file not found.pdf';
    $file_info['filesize'] = 632;
    $file_info['content'] = FileStorage::getEmptyFile('pdf');
} else {
    $file_info['content'] = FileStorage::getFileContent($id);

    if ($file_info['content'] == null) {
        $current_state = "Error: `{$file_info['username']}` file content not found (id = {$id})";

        $file_info['content'] = FileStorage::getEmptyFile('pdf');
        $file_info['username'] = 'file not found.pdf';
        $file_info['filesize'] = 632;
    } else {
        $current_state = "File: `{$file_info['username']}` retrieved, filesize = {$file_info['filesize']}";
    }

    /* update stat_download_counter
    but only for really downloaded files, not fetched via control panel */
    if (strpos($_SERVER['HTTP_REFERER'], '/core/') == false ){
        FileStorage::statUpdateDownloadCounter($id);
        FileStorage::statLogDownloadEvent($id, $current_state);
    }

}

CloseDB($link);

header ("HTTP/1.1 200 OK");
header ("X-Powered-By: PHP/" . phpversion());
header ("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
header ("Cache-Control: None");
header ("Pragma: no-cache");
header ("Accept-Ranges: bytes");
header ("Content-Disposition: inline; filename=\"" . $file_info['username'] . "\"");

if (isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
    Header('Content-Type: application/force-download');
else
    Header('Content-Type: application/octet-stream');

header ("Content-Length: " . $file_info['filesize']);
header ("Age: 0");
header ("Proxy-Connection: close");
header('Accept-Ranges: bytes');
print($file_info['content']);
flush();
exit();
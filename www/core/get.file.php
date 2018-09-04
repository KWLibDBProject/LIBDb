<?php
define('__ACCESS_MODE__', 'frontend');
require_once '__required.php'; // $mysqli_link

//@todo: сделать возможность просмотра файла как по АЙДИ, так и по ЮЗЕРНЕЙМ

$fid = IsSet($_GET['id']) ? intval($_GET['id']) : Die();

$file_info = FileStorage::getFileInfo($fid);

if (!$file_info) {
    $current_state = "Error: unknown file id ({$fid}), not found in DB!";

    $file_info['username'] = 'file not found.pdf';
    $file_info['filesize'] = 632;
    $file_info['content'] = FileStorage::getEmptyFile('pdf');

} else {

    $file_info['content'] = FileStorage::getFileContent($fid);

    if ($file_info['content'] == null) {
        $current_state = "Error: `{$file_info['username']}` file content not found (id = {$fid})";

        $file_info['content'] = FileStorage::getEmptyFile('pdf');
        $file_info['username'] = 'file not found.pdf';
        $file_info['filesize'] = 632;
    } else {
        $current_state = $_SERVER['HTTP_REFERER'] ?? '';
    }

    /* update stat_download_counter
    but only for really downloaded files, not fetched via control panel */
    if (strpos( ($_SERVER['HTTP_REFERER'] ?? ''), '/core/') == false ) {
        FileStorage::statUpdateDownloadCounter($fid);
        FileStorage::statLogDownloadEvent($fid, $current_state);
    }
}

header ('HTTP/1.1 200 OK');
header ('X-Powered-By: PHP/' . phpversion());
header ('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
header ('Cache-Control: None');
header ('Pragma: no-cache');
header ('Accept-Ranges: bytes');
header ('Content-Disposition: inline; filename="' . $file_info['username'] . '"');

if (isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
    Header('Content-Type: application/force-download');
else
    Header('Content-Type: application/octet-stream');

header ('Content-Length: ' . $file_info['filesize']);
header ('Age: 0');
header ('Proxy-Connection: close');
header('Accept-Ranges: bytes');

print($file_info['content']);

flush();

exit();
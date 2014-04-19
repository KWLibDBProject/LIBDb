<?php
require_once('core.db.php');
require_once('core.filestorage.php');

// сделать возможность просмотра файла как по АЙДИ, так и по ЮЗЕРНЕЙМ
$id = IsSet($_GET['id']) ? $_GET['id'] : Die();

$link = ConnectDB();

$file_info = FileStorage::getFileInfo($id);

if (!$file_info) {
    $file_info['username'] = 'file not found.pdf';
    $file_info['filesize'] = 632;
    $file_info['content'] = FileStorage::getEmptyPDF();
} else {
    $file_info['content'] = FileStorage::getFileContent($id);
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
?>
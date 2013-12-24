<?php
require_once('core.php');
require_once('core.db.php');
// сделать возможность просмотра файла как по АЙДИ, так и по ЮЗЕРНЕЙМ
$id = IsSet($_GET['id']) ? $_GET['id'] : Die();

$link = ConnectDB();

$q = "SELECT content,filesize,username FROM pdfdata WHERE id=$id";
$result = mysql_query($q, $link) or Die("On $q");
$file = mysql_fetch_assoc($result);

CloseDB($link);

if (isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
    Header('Content-Type: application/force-download');
else
    Header('Content-Type: application/octet-stream');

Header('Accept-Ranges: bytes');
Header('Content-Length: ' . $file['filesize']);
Header('Content-disposition: attachment; filename="' . $file['username'] . '"');
echo $file['content'];
exit();
?>
<?php

require_once('core.db.php');

$simple_pdf = "JVBERi0xLjQNCjEgMCBvYmoNCjw8IC9UeXBlIC9DYXRhbG9nIC9PdXRsaW5lcyAyIDAgUiAvUGFnZXMgMyAwIFIgPj4NCmVuZG9iag0KMiAwIG9iag0KPDwgL1R5cGUgT3V0bGluZXMgL0NvdW50IDAgPj4NCmVuZG9iag0KMyAwIG9iag0KPDwgL1R5cGUgL1BhZ2VzIC9LaWRzIFs0IDAgUl0gL0NvdW50IDEgPj4NCmVuZG9iag0KNCAwIG9iag0KPDwgL1R5cGUgL1BhZ2UgL1BhcmVudCAzIDAgUiAvTWVkaWFCb3ggWzAgMCA2MTIgNzkyXSAvQ29udGVudHMgNSAwIFIgL1Jlc291cmNlcyA8PCAvUHJvY1NldCA2IDAgUiA+PiA+Pg0KZW5kb2JqDQo1IDAgb2JqDQo8PCAvTGVuZ3RoIDM1ID4+DQpzdHJlYW0NCoUgUGFnZS1tYXJraW5nIG9wZXJhdG9ycyCFDQplbmRzdHJlYW0gDQplbmRvYmoNCjYgMCBvYmoNClsvUERGXQ0KZW5kb2JqDQp4cmVmDQowIDcNCjAwMDAwMDAwMDAgNjU1MzUgZiANCjAwMDAwMDAwMDkgMDAwMDAgbiANCjAwMDAwMDAwNzQgMDAwMDAgbiANCjAwMDAwMDAxMTkgMDAwMDAgbiANCjAwMDAwMDAxNzYgMDAwMDAgbiANCjAwMDAwMDAyOTUgMDAwMDAgbiANCjAwMDAwMDAzNzYgMDAwMDAgbiANCnRyYWlsZXIgDQo8PCAvU2l6ZSA3IC9Sb290IDEgMCBSID4+DQpzdGFydHhyZWYNCjM5NA0KJSVFT0Y=";
$ref_filestorage = 'filestorage';

// сделать возможность просмотра файла как по АЙДИ, так и по ЮЗЕРНЕЙМ
$id = IsSet($_GET['id']) ? $_GET['id'] : Die();

$link = ConnectDB();

$q = "SELECT `content`, `filesize`, `username` FROM $ref_filestorage WHERE `id`='$id'";
$result = mysql_query($q, $link) or Die("On $q");

if (mysql_num_rows($result) == 1) {
    $record = mysql_fetch_assoc($result);
    $filename = $record['username'];
    $filesize = $record['filesize'];
    $filecontent = $record['content'];
} else {
    $filename = 'file not found.pdf';
    $filesize = 632;
    $filecontent = base64_decode($simple_pdf);
}

CloseDB($link);

header ("HTTP/1.1 200 OK");
header ("X-Powered-By: PHP/" . phpversion());
header ("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
header ("Cache-Control: None");
header ("Pragma: no-cache");
header ("Accept-Ranges: bytes");
header ("Content-Disposition: inline; filename=\"" . $filename . "\"");

if (isset($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
    Header('Content-Type: application/force-download');
else
    Header('Content-Type: application/octet-stream');
header ("Content-Length: " . $filesize);
header ("Age: 0");
header ("Proxy-Connection: close");
header('Accept-Ranges: bytes');
header('Content-Length: ' . $filesize);
echo $filecontent;
exit();
?>
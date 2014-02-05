<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$id = IsSet($_GET['id']) ? $_GET['id'] : Die();

$ref_filestorage = 'filestorage';

// удалить из таблицы filestorage

$link = ConnectDB();

$pdf_record = mysql_fetch_assoc(mysql_query("SELECT relation FROM $ref_filestorage WHERE id=$id"))or Die("Die on: SELECT articleid FROM filestorage WHERE id=$id");

$a_result = mysql_query("UPDATE articles SET pdfid=-1 WHERE id=".$pdf_record['relation']) or Die("Die on: UPDATE articles SET pdfid=0 WHERE id=".$pdf_record['relation']);

$del = mysql_query("DELETE FROM $ref_filestorage WHERE id=$id") or Die("Die on: DELETE FROM $ref_filestorage WHERE id=$id");

CloseDB($link);
$result['error'] = 0;
print(json_encode($result));
?>
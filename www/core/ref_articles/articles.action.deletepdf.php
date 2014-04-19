<?php
//@todo: ПЕРЕЙТИ НА filestorage.action.remove!!!

require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.filestorage.php');

$id = IsSet($_GET['id']) ? $_GET['id'] : Die();

$ref_filestorage = 'filestorage';

// удалить из таблицы filestorage

$link = ConnectDB();

$pdf_relation = FileStorage::getRelById($id);

$a_result = mysql_query("UPDATE articles SET pdfid=-1 WHERE id={$pdf_relation}") or Die("Die on: UPDATE articles RELATION field");
//@todo: FileStorage::??? -- как назвать функцию, которая будет делать то же, что делает строчка выше?

FileStorage::removeFileById($id);

CloseDB($link);
$result['error'] = 0;
print(json_encode($result));
?>
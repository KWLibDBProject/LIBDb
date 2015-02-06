<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.filestorage.php');

$id = IsSet($_GET['id']) ? intval($_GET['id']) : Die();

// удалить из таблицы filestorage

$link = ConnectDB();

// вообще-то это избыточно, достаточно "update articles set pdfid = -1 where id = $id" :)
$pdf_relation = FileStorage::getRelById($id);
$a_result = mysql_query("UPDATE articles SET pdfid=-1 WHERE id={$pdf_relation}") or Die("Die on: UPDATE articles RELATION field");

//FileStorage::??? -- как назвать функцию, которая будет делать то же, что делает строчка выше?
// это удаление реально внести в функцию removeFileById, но нюанс в том, что в разных таблицах
// поле, где хранится идентификатор файла называется по-разному!
// А в некоторых таблицах так их еще и несколько :)

FileStorage::removeFileById($id);

CloseDB($link);
$result['error'] = 0;
print(json_encode($result));
?>
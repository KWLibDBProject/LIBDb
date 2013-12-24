<?php
require_once('../core.php');
require_once('../core.db.php');
$id = IsSet($_GET['id']) ? $_GET['id'] : Die();

// удалить из таблицы pdfdata

$link = ConnectDB();

$pdf_record = mysql_fetch_assoc(mysql_query("SELECT articleid FROM pdfdata WHERE id=$id"))or Die("Die on: SELECT articleid FROM pdfdata WHERE id=$id");
$a_result = mysql_query("UPDATE articles SET pdfid=-1 WHERE id=".$pdf_record['articleid']) or Die("Die on: UPDATE articles SET pdfid=0 WHERE id=".$pdf_record['articleid']);

$del = mysql_query("DELETE FROM pdfdata WHERE id=$id") or Die("Die on: DELETE FROM pdfdata WHERE id=$id");
CloseDB($link);
$result['error'] = 0;
print(json_encode($result));
?>
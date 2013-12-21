<?php
require_once('../core.php');
require_once('../db.php');
$result['message'] = '';
$result['error'] = 0;
$id = IsSet($_GET['id']) ? $_GET['id'] : Die("No id!");

$link = ConnectDB();

// удалить статью из артиклез
$q = "DELETE FROM articles WHERE id=$id";
mysql_query($q,$link) or Die("Death at $q");

// удалить пдфку из пдфдата
$q = "DELETE FROM pdfdata WHERE articleid=$id";
mysql_query($q,$link) or Die("Death at $q");

// удалить соответствия из cross_aa
$q = "DELETE FROM cross_aa WHERE article=$id";
mysql_query($q,$link) or Die("Death at $q");

CloseDB($link);

if ($result['error']==0) {
    $result['message'] = <<<FINAL_MESSAGE
<meta http-equiv="refresh" content="15;URL=../articles.show.php">
<button onclick="window.location.href='../articles.show.php'">Вернуться к списку статей</button>
FINAL_MESSAGE;
}

echo $result['message'];
?>
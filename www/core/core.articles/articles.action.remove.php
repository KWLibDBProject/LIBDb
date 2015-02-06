<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.filestorage.php');

$result['message'] = '';
$result['error'] = 0;
$article_id = IsSet($_GET['id']) ? intval($_GET['id']) : Die("No id!");

$link = ConnectDB();

// получить информацию о ПДФке, относящейся к статье
$q = "SELECT id, pdfid FROM articles WHERE id = {$article_id}";
$qr = mysql_query($q);
$qf = mysql_fetch_assoc($qr);
$pdfid = $qf['pdfid'];

// удалить пдфку из filestorage
FileStorage::removeFileById($pdfid);

// удалить связи СТАТЬЯ - АВТОРЫ из cross_aa
$q = "DELETE FROM cross_aa WHERE article = {$article_id}";
mysql_query($q, $link) or Die("Death at $q");

// только теперь удалить саму статью
$q = "DELETE FROM articles WHERE id = {$article_id}";
mysql_query($q, $link) or Die("Death at {$q}");

kwLogger::logEvent('Delete', 'articles', $article_id, "Article removed, id was: {$article_id}" );

CloseDB($link);

if ($result['error'] == 0) {
    $override = array(
        'time' => 10,
        'target' => '/core/ref.articles.show.php',
        'buttonmessage' => 'Вернуться к списку статей',
        'message' => 'Статья удалена из базы данных'
    );
} else {
    $override = array(
        'time' => 10,
        'target' => '/core/ref.articles.show.php',
        'buttonmessage' => 'Вернуться к списку статей',
        'message' => $result['message']
    );
}
$tpl = new kwt('../ref.all.timed.callback.tpl');
$tpl->override($override);
$tpl->out();
?>
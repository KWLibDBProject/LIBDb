<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.filestorage.php');

$result['message'] = '';
$result['error'] = 0;
$id = IsSet($_GET['id']) ? $_GET['id'] : Die("No id!");

$link = ConnectDB();

// удалить статью из артиклез

$q = "SELECT id, pdfid FROM articles WHERE id = {$id}";
$qr = mysql_query($q);
$qf = mysql_fetch_assoc($qr);
$pdfid = $qf['pdfid'];

$q = "DELETE FROM articles WHERE id= $id";
mysql_query($q, $link) or Die("Death at $q");

// удалить пдфку из пдфдата
// FileStorage::removeFileByRel($id, 'articles'); // обязательно указать коллекцию, иначе удалим не то
FileStorage::removeFileById($pdfid);

// удалить соответствия из cross_aa
$q = "DELETE FROM cross_aa WHERE article=$id";
mysql_query($q,$link) or Die("Death at $q");

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
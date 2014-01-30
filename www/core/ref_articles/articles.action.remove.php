<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');


$result['message'] = '';
$result['error'] = 0;
$id = IsSet($_GET['id']) ? $_GET['id'] : Die("No id!");

$link = ConnectDB();

// @todo: TEST: когда нельзя удалить статью?

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

if ($result['error'] == 0) {
    $override = array(
        'time' => 10,
        'target' => '../ref.articles.show.php',
        'buttonmessage' => 'Вернуться к списку статей',
        'message' => 'Статья в базе данных поставлена на удаление'
    );
} else {
    $override = array(
        'time' => 10,
        'target' => '../ref.articles.show.php',
        'buttonmessage' => 'Вернуться к списку статей',
        'message' => $result['message']
    );
}
$tpl = new kwt('../ref.all.timed.callback.tpl');
$tpl->override($override);
$tpl->out();
?>
<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');


$tpl = new kwt('news.form.tpl.html');

$tpl -> config('/**','**/');

$over = array(
    'page_id' => isset($_GET['id']) ? $_GET['id'] : -1,
    'form_call_script' => isset($_GET['id']) ? 'news.action.update.php' : 'news.action.insert.php',
    'submit_button_text' => isset($_GET['id']) ? 'ОТРЕДАКТИРОВАТЬ НОВОСТЬ' : 'ДОБАВИТЬ НОВОСТЬ',
    'page_title' => isset($_GET['id']) ? 'Новости -- редактирование' : 'Новости -- добавление',
);
$tpl->override($over);
$tpl->out();
?>
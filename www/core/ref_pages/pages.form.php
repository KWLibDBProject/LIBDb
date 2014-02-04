<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');


$tpl = new kwt('pages.form.tpl.html');

$tpl -> config('/**','**/');

$over = array(
    'page_id' => isset($_GET['id']) ? $_GET['id'] : -1,
    'form_call_script' => isset($_GET['id']) ? 'pages.action.update.php' : 'pages.action.insert.php',
    'submit_button_text' => isset($_GET['id']) ? 'СОХРАНИТЬ ИЗМЕНЕНИЯ' : 'ДОБАВИТЬ СТРАНИЦУ',
    'page_title' => isset($_GET['id']) ? 'Страницы -- редактирование' : 'Страницы -- добавление',
);
$tpl->override($over);
$tpl->out();
?>
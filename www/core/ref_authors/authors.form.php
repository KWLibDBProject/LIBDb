<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');


$tpl = new kwt('authors.form.tpl_');

$over = array(
    'author_id' => isset($_GET['id']) ? $_GET['id'] : -1,
    'form_call_script' => isset($_GET['id']) ? 'authors.action.update.php' : 'authors.action.insert.php',
    'submit_button_text' => isset($_GET['id']) ? 'СОХРАНИТЬ ИЗМЕНЕНИЯ' : 'ДОБАВИТЬ АВТОРА',
    'page_title' => isset($_GET['id']) ? 'Авторы -- редактирование' : 'Авторы -- добавление',
);
$tpl->override($over);
$tpl->out();
?>

<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$data = json_decode(file_get_contents("http://".$_SERVER['HTTP_HOST'].'/core/core.pages/pages.action.getitem.php?id='.$_GET['id']), true);
$page_id = isset($_GET['id']) ? $_GET['id'] : -1;

$tpl = new kwt('pages.form.tpl.html');

$tpl -> config('/**','**/');

$over = array(
    'page_id'               => $page_id,
    'form_call_script'      => isset($_GET['id']) ? 'pages.action.update.php' : 'pages.action.insert.php',
    'submit_button_text'    => isset($_GET['id']) ? 'СОХРАНИТЬ ИЗМЕНЕНИЯ' : 'СОХРАНИТЬ СТРАНИЦУ',
    'page_title'        => isset($_GET['id']) ? 'Страницы -- редактирование' : 'Страницы -- добавление',
    'title_en'          => $data['data']['title_en'],
    'title_ru'          => $data['data']['title_ru'],
    'title_uk'          => $data['data']['title_uk'],
    'content_en'        => $data['data']['content_en'],
    'content_ru'        => $data['data']['content_ru'],
    'content_uk'        => $data['data']['content_uk'],
    'comment'           => $data['data']['comment'],
    'alias'             => $data['data']['alias'],
);
$tpl->override($over);
$tpl->out();
?>
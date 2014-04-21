<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$data = json_decode(file_get_contents("http://".$_SERVER['HTTP_HOST'].'/core/core.news/news.action.getitem.php?id='.$_GET['id']), true);
$news_id = isset($_GET['id']) ? $_GET['id'] : -1;

$tpl = new kwt('news.form.tpl.html');

$tpl -> config('/**','**/');

$over = array(
    'news_id'   => $news_id,
    'form_call_script' => isset($_GET['id']) ? 'news.action.update.php' : 'news.action.insert.php',
    'submit_button_text' => isset($_GET['id']) ? 'СОХРАНИТЬ ИЗМЕНЕНИЯ' : 'ДОБАВИТЬ',
    'page_title' => isset($_GET['id']) ? 'Новости -- редактирование' : 'Новости -- добавление',
    'title_en' => $data['data']['title_en'],
    'title_ru' => $data['data']['title_ru'],
    'title_uk' => $data['data']['title_uk'],
    'text_en' => $data['data']['text_en'],
    'text_ru' => $data['data']['text_ru'],
    'text_uk' => $data['data']['text_uk'],
    'date_add' => $data['data']['date_add'],
    'comment'   => $data['data']['comment'],
);
$tpl->override($over);
$tpl->out();
?>
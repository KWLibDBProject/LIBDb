<?php
require_once '../__required.php'; // $mysqli_link

$SID = session_id();
if(empty($SID)) session_start();
ifNotLoggedRedirect('/core/');

$page_id = isset($_GET['id']) ? intval($_GET['id']) : -1;

if ($page_id != -1) {
    $query = "SELECT * FROM staticpages WHERE id=$page_id";
    $res = mysqli_query($mysqli_link, $query) or die("Unable to execute mysqli request: ".$query);

    if (mysqli_num_rows($res) > 0) {
        $data = mysqli_fetch_assoc($res);
    }
}


$template_dir = '$/core/core.pages/';
$template_file = "_template.pages.form.html";

$template_data = array(
    'page_id'           => $page_id,
    'form_call_script'  => ($page_id != -1) ? 'pages.action.update.php' : 'pages.action.insert.php',
    'submit_button_text'=> ($page_id != -1) ? 'СОХРАНИТЬ ИЗМЕНЕНИЯ' : 'СОХРАНИТЬ СТРАНИЦУ',
    'page_title'        => ($page_id != -1) ? 'Страницы -- редактирование' : 'Страницы -- добавление',
    'title_en'          => $data['title_en'] ?? '',
    'title_ru'          => $data['title_ru'] ?? '',
    'title_ua'          => $data['title_ua'] ?? '',
    'content_en'        => $data['content_en'] ?? '',
    'content_ru'        => $data['content_ru'] ?? '',
    'content_ua'        => $data['content_ua'] ?? '',
    'comment'           => $data['comment'] ?? '',
    'alias'             => $data['alias'] ?? '',
);

echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);
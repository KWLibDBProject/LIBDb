<?php
require_once '../__required.php'; // $mysqli_link

$news_id = isset($_GET['id']) ? intval($_GET['id']) : -1;

if ($news_id != -1) {
    $query = "SELECT *, DATE_FORMAT(publish_date, '%d.%m.%Y') as publish_date FROM news WHERE id=$news_id";
    $res = mysqli_query($mysqli_link, $query) or die("Unable to execute mysqli request: ".$query);

    if (mysqli_num_rows($res) > 0) {
        $data = mysqli_fetch_assoc($res);
    }
}

$template_dir = '$/core/core.news/';
$template_file = "_template.news.form.html";

$template_data = [
    'news_id'               => $news_id,
    'form_call_script'      => isset($_GET['id']) ? 'news.action.update.php' : 'news.action.insert.php',
    'submit_button_text'    => isset($_GET['id']) ? 'СОХРАНИТЬ ИЗМЕНЕНИЯ' : 'ДОБАВИТЬ',
    'page_title'            => isset($_GET['id']) ? 'Новости -- редактирование' : 'Новости -- добавление',
    'title_en'          => $data['title_en'] ?? '',
    'title_ru'          => $data['title_ru'] ?? '',
    'title_ua'          => $data['title_ua'] ?? '',
    'text_en'           => $data['text_en'] ?? '',
    'text_ru'           => $data['text_ru'] ?? '',
    'text_ua'           => $data['text_ua'] ?? '',
    'publish_date'      => $data['publish_date'] ?? '',
    'comment'       => $data['comment'] ?? '',
];

echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);
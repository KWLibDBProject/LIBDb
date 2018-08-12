<?php
require_once '../__required.php'; // $mysqli_link

$ref_name = 'news';

$q = array(
    //@todo: переименовать в publish_date в шаблонах и базе - дата добавления != дата публикации

    'date_add'      => DateTime::createFromFormat('d.m.Y', $_POST['date_add'])->format('Y-m-d'),

    'comment'       => mysqli_real_escape_string($mysqli_link, $_POST['comment']),
    'title_en'      => mysqli_real_escape_string($mysqli_link, $_POST['title_en']),
    'title_ru'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ru']),
    'title_ua'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ua']),
    'text_en'       => mysqli_real_escape_string($mysqli_link, $_POST['text_en']),
    'text_ru'       => mysqli_real_escape_string($mysqli_link, $_POST['text_ru']),
    'text_ua'       => mysqli_real_escape_string($mysqli_link, $_POST['text_ua']),

    'timestamp'     => DateTime::createFromFormat('d.m.Y', $_POST['date_add'])->format('U'),
);
/*
with PDO:
'date_add'      => "STR_TO_DATE('{$_POST['date_add']}', '%d.%m.%Y')" //@PDO
 */


$qstr = MakeInsert($q, $ref_name);

if ($res = mysqli_query($mysqli_link, $qstr)) {
    $result['message'] = 'Новость добавлена';
    $result['error'] = 0;
    $record_id = mysqli_insert_id($mysqli_link);
    kwLogger::logEvent('Add', 'news', $record_id, "News record added, id = {$record_id}");
}
else {
    Die("Unable to insert data to DB!  ".$qstr);
}

if (isAjaxCall()) {
    print(json_encode($result));
} else {
    if ($result['error'] == 0) {

        $template_dir = '$/core/_templates';
        $template_file = "ref.all_timed_callback.html";

        $template_data = array(
            'time'          => Config::get('callback_timeout') ?? 15,
            'target'        => '../list.news.show.php',
            'button_text'   => 'Вернуться к списку новостей',
            'message'       => 'Новость добавлена'
        );
        echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);
    }
}
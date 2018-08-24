<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

$ref_name = 'news';
$id = isset($_POST['id']) ? $_POST['id'] : Die('Unknown ID. ');

$dataset = array(
    'publish_date'      => DateTime::createFromFormat('d.m.Y', $_POST['publish_date'])->format('Y-m-d'),

    'comment'       => mysqli_real_escape_string($mysqli_link, $_POST['comment']),

    'title_en'      => mysqli_real_escape_string($mysqli_link, $_POST['title_en']),
    'title_ru'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ru']),
    'title_ua'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ua']),

    'text_en'       => mysqli_real_escape_string($mysqli_link, $_POST['text_en']),
    'text_ru'       => mysqli_real_escape_string($mysqli_link, $_POST['text_ru']),
    'text_ua'       => mysqli_real_escape_string($mysqli_link, $_POST['text_ua']),
);

$query = MakeUpdate($dataset, $ref_name, " WHERE id=$id ");

if ($res = mysqli_query($mysqli_link, $query)) {
    $result['message'] = 'Новость обновлена';
    $result['error'] = 0;
    kwLogger::logEvent('Update', 'news', $id, "News record updated, id = {$id}");
}
else {
    Die("Unable to insert data to DB!  ".$query);
}

if (isAjaxCall()) {
    print(json_encode($result));
} else {
    if ($result['error'] == 0) {

        $template_dir = '$/core/_templates';
        $template_file = "ref.all_timed_callback.html";

        $template_data = array(
            'time'          => Config::get('callback_timeout') ?? 15,
            'target'        => '/core/list.news.show.php',
            'button_text'   => 'Вернуться к списку новостей',
            'message'       => 'Новость обновлена'
        );
        echo websun_parse_template_path($template_data, $template_file, $template_dir);
    }
}
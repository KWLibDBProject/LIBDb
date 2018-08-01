<?php
require_once '../__required.php'; // $mysqli_link

$ref_name = 'news';

$q = array(
    'date_add'      => mysqli_real_escape_string($mysqli_link, $_POST['date_add']),
    'comment'       => mysqli_real_escape_string($mysqli_link, $_POST['comment']),
    'title_en'      => mysqli_real_escape_string($mysqli_link, $_POST['title_en']),
    'title_ru'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ru']),
    'title_uk'      => mysqli_real_escape_string($mysqli_link, $_POST['title_uk']),
    'text_en'       => mysqli_real_escape_string($mysqli_link, $_POST['text_en']),
    'text_ru'       => mysqli_real_escape_string($mysqli_link, $_POST['text_ru']),
    'text_uk'       => mysqli_real_escape_string($mysqli_link, $_POST['text_uk']),
    'date_year'     => substr(mysqli_real_escape_string($mysqli_link, $_POST['date_add']),6,4),
);
$q['timestamp'] = ConvertDateToTimestamp($q['date_add']);

$qstr = MakeInsert($q, $ref_name);

if ($res = mysqli_query($mysqli_link, $qstr)) {
    $result['message'] = $qstr;
    $result['error'] = 0;
    $record_id = mysqli_insert_id();
    kwLogger::logEvent('Add', 'news', $record_id, "News record added, id = {$record_id}");
}
else {
    Die("Unable to insert data to DB!  ".$qstr);
}

if (isAjaxCall()) {
    print(json_encode($result));
} else {
    if ($result['error'] == 0) {
        // use template
        $override = array(
            'time' => 10,
            'target' => '../ref.news.show.php',
            'buttonmessage' => 'Вернуться к списку страниц',
            'message' => 'Новость добавлена в базу данных'
        );
        $tpl = new kwt('../ref.all.timed.callback.tpl');
        $tpl->override($override);
        $tpl->out();
    }
}
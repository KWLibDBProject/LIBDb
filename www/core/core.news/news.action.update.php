<?php
require_once '../__required.php'; // $mysqli_link

$ref_name = 'news';
$id = isset($_POST['id']) ? $_POST['id'] : Die('Unknown ID. ');

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
$q ['timestamp']    =  ConvertDateToTimestamp($q['date_add']);

$qstr = MakeUpdate($q, $ref_name, " WHERE id=$id ");

if ($res = mysqli_query($mysqli_link, $qstr, $link)) {
    $result['message'] = $qstr;
    $result['error'] = 0;
    kwLogger::logEvent('Update', 'news', $id, "News record updated, id = {$id}");
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
            'target' => '/core/ref.news.show.php',
            'buttonmessage' => 'Вернуться к списку новостей',
            'message' => 'Данные обновлены'
        );
        $tpl = new kwt('../ref.all.timed.callback.tpl');
        $tpl->override($override);
        $tpl->out();
    }
}
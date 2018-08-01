<?php
require_once '../__required.php'; // $mysqli_link

$ref_name = 'staticpages';


$now = ConvertTimestampToDate();
$q = array(
    'alias'         => mysqli_real_escape_string($mysqli_link, $_POST['alias']),
    'comment'       => mysqli_real_escape_string($mysqli_link, $_POST['comment']),
    'title_en'      => mysqli_real_escape_string($mysqli_link, $_POST['title_en']),
    'title_ru'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ru']),
    'title_uk'      => mysqli_real_escape_string($mysqli_link, $_POST['title_uk']),
    'content_en'    => mysqli_real_escape_string($mysqli_link, $_POST['content_en']),
    'content_ru'    => mysqli_real_escape_string($mysqli_link, $_POST['content_ru']),
    'content_uk'    => mysqli_real_escape_string($mysqli_link, $_POST['content_uk']),
    'stat_date_insert'  =>  $now,
    'stat_date_update'  =>  $now
);
$qstr = MakeInsert($q, $ref_name);

if ($res = mysqli_query($mysqli_link, $qstr)) {
    $result['message'] = $qstr;
    $result['error'] = 0;
    $record_id = mysqli_insert_id($mysqli_link);
    kwLogger::logEvent('Add', 'pages', $record_id, "Static page added, id = {$record_id}");
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
            'target' => '../ref.pages.show.php',
            'buttonmessage' => 'Вернуться к списку страниц',
            'message' => 'Страница добавлена в базу данных'
        );
        $tpl = new kwt('../ref.all.timed.callback.tpl');
        $tpl->override($override);
        $tpl->out();
    }
}
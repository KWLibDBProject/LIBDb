<?php
// print_r($_POST);

require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.kwlogger.php');

$ref_name = 'staticpages';


$link = ConnectDB();

$now = ConvertTimestampToDate();
$q = array(
    'alias'         => mysql_real_escape_string($_POST['alias']),
    'comment'       => mysql_real_escape_string($_POST['comment']),
    'title_en'      => mysql_real_escape_string($_POST['title_en']),
    'title_ru'      => mysql_real_escape_string($_POST['title_ru']),
    'title_uk'      => mysql_real_escape_string($_POST['title_uk']),
    'content_en'    => mysql_real_escape_string($_POST['content_en']),
    'content_ru'    => mysql_real_escape_string($_POST['content_ru']),
    'content_uk'    => mysql_real_escape_string($_POST['content_uk']),
    'stat_date_insert'  =>  $now,
    'stat_date_update'  =>  $now
);
$qstr = MakeInsert($q, $ref_name);

if ($res = mysql_query($qstr, $link)) {
    $result['message'] = $qstr;
    $result['error'] = 0;
    $record_id = mysql_insert_id();
    kwLogger::logEvent('Add', 'pages', $record_id, "Static page added, id = {$record_id}");
}
else {
    Die("Unable to insert data to DB!  ".$qstr);
}

CloseDB($link);

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
?>
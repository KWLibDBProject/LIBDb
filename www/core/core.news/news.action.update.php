<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.kwlogger.php');

$ref_name = 'news';
$id = isset($_POST['id']) ? $_POST['id'] : Die('Unknown ID. ');

$link = ConnectDB();
$q = array(
    'date_add'      => mysql_real_escape_string($_POST['date_add']),
    'comment'       => mysql_real_escape_string($_POST['comment']),
    'title_en'      => mysql_real_escape_string($_POST['title_en']),
    'title_ru'      => mysql_real_escape_string($_POST['title_ru']),
    'title_uk'      => mysql_real_escape_string($_POST['title_uk']),
    'text_en'       => mysql_real_escape_string($_POST['text_en']),
    'text_ru'       => mysql_real_escape_string($_POST['text_ru']),
    'text_uk'       => mysql_real_escape_string($_POST['text_uk']),
    'date_year'     => substr(mysql_real_escape_string($_POST['date_add']),6,4),
);
$q ['timestamp']    =  ConvertDateToTimestamp($q['date_add']);

$qstr = MakeUpdate($q, $ref_name, " WHERE id=$id ");

if ($res = mysql_query($qstr, $link)) {
    $result['message'] = $qstr;
    $result['error'] = 0;
    kwLogger::logEvent('Update', 'news', $id, "News record updated, id = {$id}");
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
            'target' => '/core/ref.news.show.php',
            'buttonmessage' => 'Вернуться к списку новостей',
            'message' => 'Данные обновлены'
        );
        $tpl = new kwt('../ref.all.timed.callback.tpl');
        $tpl->override($override);
        $tpl->out();
    }
}
?>
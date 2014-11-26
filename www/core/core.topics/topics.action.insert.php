<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.kwlogger.php');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}

$link = ConnectDB();

$q = array(
    'title_en' => mysql_real_escape_string($_POST['title_en']),
    'title_ru' => mysql_real_escape_string($_POST['title_ru']),
    'title_uk' => mysql_real_escape_string($_POST['title_uk']),
    'rel_group' => mysql_real_escape_string($_POST['rel_group']),
);
$qstr = MakeInsert($q,$_POST['ref_name']);
$res = mysql_query($qstr, $link) or Die("Unable to insert data to DB!".$qstr);
$new_id = mysql_insert_id() or Die("Unable to get last insert id!");

kwLogger::logEvent('Add', 'topics', $new_id, "Topic added, id = {$new_id}");

$result['message'] = $qstr;
$result['error'] = 0;

print(json_encode($result));
CloseDB($link);
?>
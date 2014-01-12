<?php
require_once('../core.php');
require_once('../core.db.php');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}

$link = ConnectDB();

$q = array(
    'title' => mysql_escape_string($_POST['title']),
    'date' => mysql_escape_string($_POST['date']),
    'contentpages' => mysql_escape_string($_POST['contentpages']),
    'published' => mysql_escape_string($_POST['published'])
);
$qstr = MakeInsert($q,$_POST['ref_name']);
$res = mysql_query($qstr, $link) or Die("Unable to insert data to DB!".$qstr);
$new_id = mysql_insert_id() or Die("Unable to get last insert id!");

$result['message'] = $qstr;
$result['error'] = 0;

print(json_encode($result));
CloseDB($link);
?>
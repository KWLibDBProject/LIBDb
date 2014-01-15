<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}

$id = $_POST['id'];

$link = ConnectDB();

$q = array(
    'title' => mysql_escape_string($_POST['title']),
    'date' => mysql_escape_string($_POST['date']),
    'contentpages' => mysql_escape_string($_POST['contentpages']),
    'published' => mysql_escape_string($_POST['published'])
);
$q['year'] = substr($q['date'],6,4);
$qstr = MakeUpdate($q, $_POST['ref_name'], "WHERE id=$id");
$res = mysql_query($qstr, $link) or Die("Unable update data : ".$qstr);

$result['message'] = $qstr;
$result['error'] = 0;

print(json_encode($result));
CloseDB($link);
?>
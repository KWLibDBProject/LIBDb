<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) header('Location: /core/');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}

$reference = 'topicgroups';
$id = $_POST['id'];

$link = ConnectDB();

$q = array(
    'title_en' => mysql_escape_string($_POST['title_en']),
    'title_ru' => mysql_escape_string($_POST['title_ru']),
    'title_uk' => mysql_escape_string($_POST['title_uk']),
    'display_order' => mysql_escape_string($_POST['display_order']),
);

$qstr = MakeUpdate($q, $reference, "WHERE id=$id");
$res = mysql_query($qstr, $link) or Die("Unable update data : ".$qstr);

$result['message'] = $qstr;
$result['error'] = 0;

print(json_encode($result));
CloseDB($link);
?>
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
    'title_en' => mysql_escape_string($_POST['title_en']),
    'title_ru' => mysql_escape_string($_POST['title_ru']),
    'title_uk' => mysql_escape_string($_POST['title_uk']),
);

$qstr = MakeUpdate($q, $_POST['ref_name'], "WHERE id=$id");
$res = mysql_query($qstr, $link) or Die("Unable update data : ".$qstr);

$result['message'] = $qstr;
$result['error'] = 0;

print(json_encode($result));
CloseDB($link);
?>
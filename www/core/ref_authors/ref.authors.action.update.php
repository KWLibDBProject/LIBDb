<?php
require_once('../core.php');
require_once('../db.php');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller! '; print(json_encode($result)); exit();
}

$id = $_POST['id'];

$link = ConnectDB();

//@todo переписать под mysqli http://www.php.net/manual/ru/mysqli.real-escape-string.php
//@todo: вставить проверку, что мы не вставляем дубликат. КАК???
$q = array(
    'name_rus' => mysql_escape_string($_POST['name_rus']),
    'name_eng' => mysql_escape_string($_POST['name_eng']),
    'name_ukr' => mysql_escape_string($_POST['name_ukr']),
    'title_rus' => mysql_escape_string($_POST['title_rus']),
    'title_eng' => mysql_escape_string($_POST['title_eng']),
    'title_ukr' => mysql_escape_string($_POST['title_ukr']),
    'email' => mysql_escape_string($_POST['email']),
    'workplace' => mysql_escape_string($_POST['workplace'])
);

$qstr = MakeUpdate($q, $_POST['ref_name'], "WHERE id=$id");
$res = mysql_query($qstr, $link) or Die("Unable update data : ".$qstr);

$result['message'] = $qstr;
$result['error'] = 0;

print(json_encode($result));
CloseDB($link);
?>
<?php
require_once('../core.php');
require_once('../core.db.php');
if (!isAjaxCall()) Die('Некорректный вызов скрипта!');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}

$id = $_POST['id'];

$link = ConnectDB();

$q = array(
    'name' => mysql_escape_string($_POST['name']),
    'email' => mysql_escape_string($_POST['email']),
    'permissions' => mysql_escape_string($_POST['permissions']),
    'login' => mysql_escape_string($_POST['login']),
    'password' => mysql_escape_string($_POST['password'])
);

$qstr = MakeUpdate($q, $_POST['ref_name'], "WHERE id=$id");
$res = mysql_query($qstr, $link) or Die("Unable update data : ".$qstr);

$result['message'] = $qstr;
$result['error'] = 0;

print(json_encode($result));
CloseDB($link);

//@todo: проверка, не пытаемся ли мы изменить логин на уже существующий?
?>
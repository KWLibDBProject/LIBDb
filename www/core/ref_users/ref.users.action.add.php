﻿<?php
require_once('../core.php');
require_once('../db.php');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}

$link = ConnectDB();

//@todo переписать под mysqli http://www.php.net/manual/ru/mysqli.real-escape-string.php
//@todo: вставить проверку, что мы не вставляем дубликат. КАК???
$q = array(
    'name' => mysql_escape_string($_POST['name']),
    'email' => mysql_escape_string($_POST['email']),
    'permissions' => mysql_escape_string($_POST['permissions']),
    'login' => mysql_escape_string($_POST['login']),
    'password' => mysql_escape_string($_POST['password'])
);
$qstr = MakeInsert($q,$_POST['ref_name']);
$res = mysql_query($qstr, $link) or Die("Unable to insert data to DB!".$qstr);
$new_id = mysql_insert_id() or Die("Unable to get last insert id!");

$result['message'] = $qstr;
$result['error'] = 0;

print(json_encode($result));
CloseDB($link);
?>
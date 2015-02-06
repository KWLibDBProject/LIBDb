<?php
// выводит в виде таблицы содержимое справочника 'users'
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

if (!isAjaxCall()) Die('Некорректный вызов скрипта!');

$link = ConnectDB();

// $ref_name = IsSet($_GET['ref']) ? $_GET['ref'] : 'users';
$ref_name = 'users';

$item_id = isset($_GET['id']) ? intval($_GET['id']) : die('no id requested');

$query = "SELECT * FROM $ref_name WHERE id=$item_id";

$res = mysql_query($query) or die("Невозможно получить содержимое справочника! ".$query);
$ref_numrows = mysql_num_rows($res);

if ($ref_numrows != 0) {
    $row = mysql_fetch_assoc($res);
    $data['data'] = $row;
    $data['error'] = 0;
    $data['message'] = '';
} else {
    $data['error'] = 1;
    $data['message'] = 'User not found, query is: '.$query;
}

CloseDB($link);

print(json_encode($data));
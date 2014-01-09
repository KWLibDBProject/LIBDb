<?php
// выводит в виде таблицы содержимое справочника (в данном случае неуниверсально, работаем со справочником авторов)
require_once('../core.php');
require_once('../core.db.php');

if (!isAjaxCall()) Die('Некорректный вызов скрипта!');

$link = ConnectDB();

$ref_name = IsSet($_GET['ref']) ? $_GET['ref'] : 'users';

$item_id = isset($_GET['id']) ? $_GET['id'] : die('no id requested');

$query = "SELECT * FROM $ref_name WHERE id=$item_id";

$res = mysql_query($query) or die("Невозможно получить содержимое справочника! ".$query);
$ref_numrows = mysql_num_rows($res);

if ($ref_numrows != 0) {
    $data['data'] = mysql_fetch_assoc($res);
    $data['error'] = 0;
    $data['message'] = '';
} else {
    $data['error'] = 1;
    $data['message'] = 'User not found, query is: '.$query;
}

CloseDB($link);

print(json_encode($data));
?>
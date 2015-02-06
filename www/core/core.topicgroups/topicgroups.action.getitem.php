<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$link = ConnectDB();

$ref_name = 'topicgroups';
$item_id = IsSet($_GET['id']) ? intval($_GET['id']) : 1;

$query = "SELECT * FROM $ref_name WHERE id=$item_id";
$res = mysql_query($query) or die("Невозможно получить содержимое справочника! ".$q);
$ref_numrows = mysql_num_rows($res);

if ($ref_numrows != 0) {
    $data['data'] = mysql_fetch_assoc($res);
    $data['error'] = 0;
    $data['message'] = '';
} else {
    $data['error'] = 1;
    $data['message'] = 'Топик не найден, скорее всего ошибка базы данных!';
}

CloseDB($link);

print(json_encode($data));
?>
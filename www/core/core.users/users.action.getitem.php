<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

if (!isAjaxCall()) Die('Некорректный вызов скрипта!');

$item_id = isset($_GET['id']) ? intval($_GET['id']) : die('no id requested');

$query = "SELECT * FROM users WHERE id=$item_id";

$res = mysqli_query($mysqli_link, $query) or die("Невозможно получить содержимое справочника! ".$query);
$ref_numrows = mysqli_num_rows($res);

if ($ref_numrows != 0) {
    $row = mysqli_fetch_assoc($res);
    $data['data'] = $row;
    $data['error'] = 0;
    $data['message'] = '';
} else {
    $data['error'] = 1;
    $data['message'] = 'User not found, query is: '.$query;
}

print(json_encode($data));
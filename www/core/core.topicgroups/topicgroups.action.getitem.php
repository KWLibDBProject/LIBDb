<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

$item_id = $_GET['id'] ?? 1;

$query = "SELECT * FROM topicgroups WHERE id={$item_id}";
$res = mysqli_query($mysqli_link, $query) or die("Невозможно получить содержимое справочника! ".$query);
$ref_numrows = mysqli_num_rows($res);

if ($ref_numrows != 0) {
    $data['data'] = mysqli_fetch_assoc($res);
    $data['error'] = 0;
    $data['message'] = '';
} else {
    $data['error'] = 1;
    $data['message'] = 'Топик не найден, скорее всего ошибка базы данных!';
}

print(json_encode($data));
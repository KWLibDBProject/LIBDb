<?php
require_once '../__required.php'; // $mysqli_link

$ref_name = 'news';
$item_id = isset($_GET['id']) ? intval($_GET['id']) : -1;

if ($item_id != -1) {
    $query = "SELECT *, DATE_FORMAT(date_add, '%d.%m.%Y') as date_add FROM news WHERE `id`={$item_id}"; // was $ref_name
    $res = mysqli_query($mysqli_link, $query) or die("Невозможно получить содержимое таблицы ".$ref_name);
    $ref_numrows = mysqli_num_rows($res);

    if ($ref_numrows != 0) {
        $data['data'] = mysqli_fetch_assoc($res);
        $data['error'] = 0;
        $data['message'] = '';
    } else {
        $data['error'] = 1;
        $data['message'] = 'Новости в базе данных не найдены, добавьте хотя бы одну!';
    }
    print(json_encode($data));
} else {
    $data['error'] = 2;
    $data['message'] = 'Неправильный вызов скрипта';
    print($data);
}

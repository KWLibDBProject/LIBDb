<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

// с помощью этого скрипта мы загружаем данные и проставляем их в поля формы редактирования, используя JS
$link = ConnectDB();

$ref_name = IsSet($_GET['ref']) ? $_GET['ref'] : 'staticpages';
$item_id = IsSet($_GET['id']) ? intval($_GET['id']) : -1;

if ($item_id != -1) {
    $query = "SELECT * FROM $ref_name WHERE id=$item_id";
    $res = mysql_query($query) or die("Невозможно получить содержимое справочника! ".$ref_name);
    $ref_numrows = mysql_num_rows($res);

    if ($ref_numrows != 0) {
        $data['data'] = mysql_fetch_assoc($res);
        $data['error'] = 0;
        $data['message'] = '';
    } else {
        $data['error'] = 1;
        $data['message'] = 'Страницы в базе данных не найдены, добавьте хотя бы одного!';
    }
    print(json_encode($data));
} else {
    $data['error'] = 2;
    $data['message'] = 'Неправильный вызов скрипта';
    print($data);
}

CloseDB($link);
?>
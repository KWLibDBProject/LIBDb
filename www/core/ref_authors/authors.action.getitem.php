<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

// с помощью этого скрипта мы загружаем данные и проставляем их в поля формы редактирования, используя JS
$link = ConnectDB();

$ref_name = IsSet($_GET['ref']) ? $_GET['ref'] : 'authors';
$item_id = IsSet($_GET['id']) ? $_GET['id'] : -1;

if ($item_id != -1) {
    $query = "SELECT * FROM $ref_name WHERE id=$item_id";
    $res = mysql_query($query) or die("Невозможно получить содержимое $ref_name");
    $ref_numrows = mysql_num_rows($res);

    if ($ref_numrows != 0) {
        $data['data'] = mysql_fetch_assoc($res);

        /* заглушка фото */
        $data['data']['photo_username'] = 'TEST'; // пользовательское имя файла

        $query = "SELECT * FROM filestorage WHERE id={$data['data']['photo_id']}";
        $res = mysql_query($query);
        if (@mysql_num_rows($res)>0)
        {
            $file = mysql_fetch_assoc($res);
            // в базе есть файл с фото автора
            $data['data']['photo_id'] = $file['id'];
            $data['data']['photo_username'] = $file['username'];
            $data['error'] = 0;
            $data['message'] = '';
        } else {
            // все в порядке, просто файла нет
            $data['error'] = 0;
            $data['message'] = 'Нажмите *удалить* и добавьте фотографию автора.';
            $data['data']['photo_id'] = -1;
            $data['data']['photo_username'] = "Нажмите *удалить* и добавьте фотографию автора.";
        }

    } else {
        $data['error'] = 2;
        $data['message'] = 'Авторы в базе данных не найдены, добавьте хотя бы одного!';
    }
    print(json_encode($data));
} else {
    $data['error'] = 5;
    $data['message'] = 'Неправильный вызов скрипта';
    print($data);
}

CloseDB($link);
?>
<?php
require_once '../__required.php'; // $mysqli_link

// с помощью этого скрипта мы загружаем данные и проставляем их в поля формы редактирования, используя JS

$ref_name = 'authors';

$item_id = IsSet($_GET['id']) ? intval($_GET['id']) : -1;

if ($item_id != -1) {
    $query = "SELECT * FROM {$ref_name} WHERE id={$item_id}";
    $res = mysqli_query($mysqli_link, $query) or die("Невозможно получить содержимое $ref_name");
    $ref_numrows = mysqli_num_rows($res);

    if ($ref_numrows == 1) {
        $data['data'] = mysqli_fetch_assoc($res);

        $file = FileStorage::getFileInfo($data['data']['photo_id']);
        // returns null if NO file (ADD mode)
        if ($file) {
            $data['data']['photo_id'] = $file['id'];
            $data['data']['photo_username'] = $file['username'];
            $data['error'] = 0;
            $data['message'] = '';
        } else {
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


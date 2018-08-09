<?php
require_once '../__required.php'; // $mysqli_link

// в $id может быть -1 - это значит, что пытаться удалять ничего не надо, а просто вернуть "ОК"

$id = isset($_GET['id']) ? intval($_GET['id']) : -1; // айди удаляемого объекта
if (empty($id)) {
    $id = -1;
}

// таблица, в которой ОБНОВЛЯЕМ релейшен объекта - собственно кто владелец файла?
$owner = isset($_GET['caller']) ? $_GET['caller'] : Die();

// проверка допустимости таблицы
$owner = getAllowedValue( $owner , array(
    'articles', 'authors', 'books'
));

// поле, в которое записываем -1 в таблице-владельце
$field = isset($_GET['subcaller']) ? $_GET['subcaller'] : Die();

// проверка допустимости поля
$field = getAllowedValue( $field, array(
    'pdfid', 'photo_id', 'file_cover', 'file_title_ru', 'file_title_en', 'file_toc_ru',
    'file_toc_en'
));

if ($id != -1)
{
    $result['message'] = '';

    $file_related_to = FileStorage::getRelById($id);

    /* update related table with -1 in field*/
    $q = "UPDATE {$owner} SET {$field}=-1 WHERE id={$file_related_to}";
    $a_result = mysqli_query($mysqli_link, $q) or Die($q);

    FileStorage::removeFileById($id);

    $result['error'] = 0;
} else {
    $result['error'] = 0;
}

print(json_encode($result));

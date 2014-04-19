<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.filestorage.php');

// в $id может быть -1 - это значит, что пытаться удалять ничего не надо, а просто вернуть "ОК"

$id = IsSet($_GET['id']) ? $_GET['id'] : -1; // айди удаляемого объекта
if (empty($id)) {
    $id = -1;
}

$caller = IsSet($_GET['caller']) ? $_GET['caller'] : Die(); // таблица, в которой ОБНОВЛЯЕМ релейшен объекта
$field = IsSet($_GET['subcaller']) ? $_GET['subcaller'] : Die(); // поле, в которое записываем -1 для таблицы

if ($id != -1)
{
    $result['message'] = '';
    $link = ConnectDB();

    $file_related_to = FileStorage::getRelById($id);

    /* update related table with -1 in field*/
    $q = "UPDATE {$caller} SET {$field}=-1 WHERE id={$file_related_to}";
    $a_result = mysql_query($q) or Die($q);

    FileStorage::removeFileById($id);

    CloseDB($link);
    $result['error'] = 0;
} else {
    $result['error'] = 0;
}

print(json_encode($result));
?>
<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

// в $id может быть -1 - это значит, что пытаться удалять ничего не надо

$id = IsSet($_GET['id']) ? $_GET['id'] : Die(); // айди удаляемого объекта
$caller = IsSet($_GET['caller']) ? $_GET['caller'] : Die(); // таблица, в которой ОБНОВЛЯЕМ релейшен объекта
$field = IsSet($_GET['subcaller']) ? $_GET['subcaller'] : Die(); // поле, в которое записываем -1 для таблицы

$ref_filestorage = 'filestorage';

// удалить из таблицы filestorage

if ($id != -1)
{
    $result['message'] = '';
    $link = ConnectDB();
    $q = "SELECT relation FROM $ref_filestorage WHERE id = $id";

    $file_record = mysql_fetch_assoc(mysql_query($q));

    $result['message'] .= "[ $q ]\r\n";

    $q = "UPDATE $caller SET $field=-1 WHERE id={$file_record['relation']}";

    $result['message'] .= "[ $q ]\r\n";

    $a_result = mysql_query($q) or Die($q);
    $q = "DELETE FROM $ref_filestorage WHERE id=$id";

    $result['message'] .= "[ $q ]\r\n";

    mysql_query($q) or Die($q);

    CloseDB($link);
    $result['error'] = 0;
} else {
    $result['error'] = 0;
}

print(json_encode($result));
?>
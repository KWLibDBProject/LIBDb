<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.kwlogger.php');


if (!isAjaxCall()) Die('Некорректный вызов скрипта!');

if (!IsSet($_GET['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
} else {
    $table = 'users';

    $link = ConnectDB();
    $id = mysql_real_escape_string($_GET["id"]);

    $q = "SELECT `permissions` FROM $table WHERE (`id`=$id)";
    $r = mysql_query($q) or Die($q);

    if (mysql_num_rows($r)!=0){
        // пользователь есть
        $row = mysql_fetch_assoc($r);
        $permission = $row['permissions'];
        if ($permission != 255) {
            // можно удалять
            $q = "DELETE FROM {$table} WHERE id = {$id} ";
            $r = mysql_query($q) or Die($q);
            $result["error"] = 0;
            $result['message'] = 'User deleted from DB';
            kwLogger::logEvent('Delete', 'users', $id, "User deleted, id was {$id}.");
        } else {
            // нельзя удалять админа
            $result["error"] = 255;
            $result['message'] = 'Unable remove root from database';
            kwLogger::logEvent('Error', 'users', $id, "Attempt remove root from DB.");
        }
    } else {
        $result["error"] = 1;
        $result['message'] = 'User not found';
        kwLogger::logEvent('Error', 'users', $id, "User with id {$id} not found.");
        // пользователь не найден
    }
    CloseDB($link);
    print(json_encode($result));
}

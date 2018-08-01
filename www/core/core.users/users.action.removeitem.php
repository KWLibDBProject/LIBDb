<?php
require_once '../__required.php'; // $mysqli_link

const PERMISSIONS_ADMIN = 255;

if (!isAjaxCall()) Die('Некорректный вызов скрипта!');

if (!IsSet($_GET['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
} else {
    $table = 'users';

    $id = mysqli_real_escape_string($mysqli_link, $_GET["id"]);

    $q = "SELECT `permissions` FROM $table WHERE (`id`=$id)";
    $r = mysqli_query($mysqli_link, $q) or Die($q);

    if (mysqli_num_rows($r)!=0){
        // пользователь есть
        $row = mysqli_fetch_assoc($r);
        $permission = $row['permissions'];
        if ($permission != PERMISSIONS_ADMIN) {
            // можно удалять
            $q = "DELETE FROM {$table} WHERE id = {$id} ";
            $r = mysqli_query($mysqli_link, $q) or Die($q);
            $result["error"] = 0;
            $result['message'] = 'User deleted from DB';
            kwLogger::logEvent('Delete', 'users', $id, "User deleted, id was {$id}.");
        } else {
            // нельзя удалять админа
            $result["error"] = PERMISSIONS_ADMIN;
            $result['message'] = 'Unable remove root administrator from database';
            kwLogger::logEvent('Error', 'users', $id, "Attempt remove root from DB.");
        }
    } else {
        $result["error"] = 1;
        $result['message'] = 'User not found';
        kwLogger::logEvent('Error', 'users', $id, "User with id {$id} not found.");
        // пользователь не найден
    }
    print(json_encode($result));
}

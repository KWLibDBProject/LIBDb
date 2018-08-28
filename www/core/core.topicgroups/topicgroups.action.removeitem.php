<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

if (!IsSet($_GET['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
} else {
    $id = intval($_GET["id"]);

    $query = "DELETE FROM topicgroups WHERE id = {$id} ";

    if ($r = mysqli_query($mysqli_link, $query)) {

        //@todo: вставить проверку, привязаны ли к группе разделы. Но оно надо? Перфекционизм говорит что да.

            $result["error"] = 0;
            $result['message'] = 'Группа тематических разделов удалена из базы данных!';
            kwLogger::logEvent('Delete', 'topicgroups', $id, "Group of topics deleted, id was {$id}");
        } else {
            $result["error"] = 1;
            $result['message'] = 'Ошибка удаления группы тематических разделов из базы данных!';
        }
    };

print(json_encode($result));
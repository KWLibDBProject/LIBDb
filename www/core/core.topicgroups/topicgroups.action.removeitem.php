<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.kwlogger.php');

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) header('Location: /core/');

if (!IsSet($_GET['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
} else {
    $table = 'topicgroups';
    $id = intval($_GET["id"]);
    $link = ConnectDB();

    $q = "DELETE FROM {$table} WHERE id = {$id} ";
    if ($r = mysql_query($q)) {
        //@todo: вставить проверку, привязаны ли к группе разделы. Но оно надо? Перфекционизм говорит что да.
            $result["error"] = 0;
            $result['message'] = 'Группа тематических разделов удалена из базы данных!';
            kwLogger::logEvent('Delete', $table, $id, "Group of topics deleted, id was {$id}");
        } else {
            $result["error"] = 1;
            $result['message'] = 'Ошибка удаления группы тематических разделов из базы данных!';
        }
    CloseDB($link);
    };

print(json_encode($result));
?>
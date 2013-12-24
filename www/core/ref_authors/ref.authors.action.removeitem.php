<?php
require_once('../core.php');
require_once('../core.db.php');

if (!IsSet($_GET['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller! '; print(json_encode($result)); exit();
} else {
    $table = $_GET['ref_name'];

    $link = ConnectDB();
    $id = $_GET["id"];

    $q = "UPDATE $table SET deleted=1 WHERE (id=$id)";
    $r = mysql_query($q) or Die(print_r($q));
    $result["error"] = 0;
    $result['message'] = 'Record marked as "deleted"';
    print(json_encode($result));
    CloseDB($link);
}
?>
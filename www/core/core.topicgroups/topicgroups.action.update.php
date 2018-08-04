<?php
require_once '../__required.php'; // $mysqli_link

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) header('Location: /core/');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}

$table = 'topicgroups';
$id = $_POST['id'];

$q = array(
    'title_en'      => mysqli_real_escape_string($mysqli_link, $_POST['title_en']),
    'title_ru'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ru']),
    'title_ua'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ua']),
    'display_order' => mysqli_real_escape_string($mysqli_link, $_POST['display_order']),
);

$qstr = MakeUpdate($q, $table, "WHERE id=$id");
$res = mysqli_query($mysqli_link, $qstr) or Die("Unable update data : ".$qstr);

kwLogger::logEvent('Update', $table, $id, "Group of topics updated, id = {$id}");

$result['message'] = $qstr;
$result['error'] = 0;

print(json_encode($result));
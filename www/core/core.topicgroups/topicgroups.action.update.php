<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}

$table = 'topicgroups';
$id = $_POST['id'];

$dataset = array(
    'title_en'      => mysqli_real_escape_string($mysqli_link, $_POST['title_en']),
    'title_ru'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ru']),
    'title_ua'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ua']),
    'display_order' => mysqli_real_escape_string($mysqli_link, $_POST['display_order']),
);

$query = MakeUpdate($dataset, $table, "WHERE id={$id}");
$res = mysqli_query($mysqli_link, $query) or Die("Unable update data : ".$query);

kwLogger::logEvent('Update', $table, $id, "Group of topics updated, id = {$id}");

$result['message'] = $query;
$result['error'] = 0;

print(json_encode($result));
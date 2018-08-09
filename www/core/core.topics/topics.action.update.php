<?php
require_once '../__required.php'; // $mysqli_link

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}

$id = intval($_POST['id']);

$q = array(
    'title_en' => mysqli_real_escape_string($mysqli_link, $_POST['title_en']),
    'title_ru' => mysqli_real_escape_string($mysqli_link, $_POST['title_ru']),
    'title_ua' => mysqli_real_escape_string($mysqli_link, $_POST['title_ua']),
    'rel_group' => mysqli_real_escape_string($mysqli_link, $_POST['rel_group']),
);

$qstr = MakeUpdate($q, $_POST['ref_name'], "WHERE id=$id");
$res = mysqli_query($mysqli_link, $qstr) or Die("Unable update data : ".$qstr);

kwLogger::logEvent('Update', 'topics', $id, "Topic updated, id = {$id}");

$result['message'] = $qstr;
$result['error'] = 0;

print(json_encode($result));
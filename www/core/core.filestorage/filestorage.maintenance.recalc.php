<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

$result = FileStorage::recalcFilesSize();

kwLogger::logEvent(
    'Maintenance',
    'filestorage',
    '*',
    "{$result['total_files_found']} records in FILESTORAGE scanned. 
    Fixed: {$result['total_files_fixed']} files. 
    Errors: {$result['total_files_error']}. ");

$exit_message = $_GET['target'] ?? '';
$exit_message =
    $exit_message == 'iframe'
        ? '<a href="#" onclick="javascript:window.parent.closeIframe()">Закрыть</a>'
        : '<a href="/core/"><<< Назад в административный раздел</a>';

$template_dir = '$/core/core.filestorage';
$template_file = "_template.filestorage.recalc_sizes.html";

$template_data = array(
    'total_files_found'     =>  $result['total_files_found'],
    'total_files_fixed'     =>  $result['total_files_fixed'],
    'total_files_error'     =>  $result['total_files_error'],
    'is_log_present'        =>  (($result['total_files_fixed'] + $result['total_files_error'])>0) ? true : false,
    'log_records'           =>  $result['log'],
    'exit_message'          =>  $exit_message
);

echo websun_parse_template_path($template_data, $template_file, $template_dir);
die;


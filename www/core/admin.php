<?php
/**
 * User: Arris
 * Date: 03.08.2018, time: 4:08
 */
require_once '__required.php'; // $mysqli_link

$SID = session_id();
if(empty($SID)) session_start();

if (!isLogged()) {
    Redirect('admin.actions.php');
}

// prepare data
$template_dir = '$/core/_templates';
$template_file = "admin.html";

$template_data = [
    'stats'     =>  [
        'articles'  =>  DBGetCount('id', 'articles'),
        'authors'   =>  DBGetCount('id', 'authors'),
        'books'     =>  DBGetCount('id', 'books'),
        'files'     =>  DBGetCount('id', 'filestorage'),
        'events'    =>  DBGetCount('id', 'eventlog'),
        'news'      =>  DBGetCount('id', 'news')
    ],
    'resources' =>  [
        'disk_space_available'  =>  ConvertToHumanBytes( disk_free_space( FileStorage::getStorageDir() ) , 1)
    ],
    'limits'    =>  [
        'post_max_size'         => ini_get('post_max_size'),
        'upload_max_filesize'   => ini_get('upload_max_filesize'),
        'max_file_uploads'      =>  ini_get('max_file_uploads'),
    ],
];


echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);

 

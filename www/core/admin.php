<?php
/**
 * User: Karel Wintersky
 * Date: 03.08.2018, time: 4:08
 */
define('__ROOT__', '/');
define('__ACCESS_MODE__', 'admin:main');

/*$SID = session_id();
if(empty($SID)) session_start();*/

require_once '__required.php';

// prepare data
$template_dir = '$/core/_templates';
$template_file = "admin.html";

$template_data = [
    'main'      =>  [
        'title'     =>  Config::get('frontend/theme/root_page_title')
    ],
    'stats'     =>  [
        'articles'  =>  DB::getRowCount('articles'),
        'authors'   =>  DB::getRowCount('authors'),
        'books'     =>  DB::getRowCount('books'),
        'news'      =>  DB::getRowCount('news'),
        'pages'     =>  DB::getRowCount('staticpages'),

        'files'     =>  DB::getRowCount( Config::get('storage/sql_table')),
        'events'    =>  DB::getRowCount( Config::get('kwlogger/log_table_event')),
        'downloads' =>  DB::getRowCount( Config::get('kwlogger/log_table_download')),

    ],
    'resources' =>  [
        'disk_space_available'  =>  ConvertToHumanBytes( disk_free_space( FileStorage::getStorageDir() ) , 1),
        'max_upload_filesize_system'    =>  FileStorage::getRealMaxUploadFileSize(),
        'max_upload_filesize_config'    =>  Config::get('storage/max_upload_size')
    ],
    'limits'    =>  [
        'post_max_size'         =>  ini_get('post_max_size'),
        'upload_max_filesize'   =>  ini_get('upload_max_filesize'),
        'max_file_uploads'      =>  ini_get('max_file_uploads'),
    ],
];

echo websun_parse_template_path($template_data, $template_file, $template_dir);

 

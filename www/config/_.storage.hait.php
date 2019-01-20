<?php
/**
 * User: Karel Wintersky
 * Date: 06.01.2019
 */

return [
    'sql_table'         =>  'filestorage',

    // storage root
    'path_root'         =>  'files.hait',

    // path to files folder for FileManager WITHOUT FINAL '/'
    'path_fm_upload'    =>  'files.hait/upload',
    'path_fm_thumb'     =>  'files.hait/thumbs',

    // path to storage folder for all PDF/JPG files WITHOUT FINAL '/'
    'path_storage'      =>  'files.hait/storage',

    'max_upload_size'   =>  1024 * 1024 * 1024,

    // store driver
    // 'db', 'disk', 'cloud'
    'store_driver'      =>  'disk',

    'cloud' => [

    ],
];

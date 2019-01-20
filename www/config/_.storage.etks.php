<?php
/**
 * User: Karel Wintersky
 * Date: 25.08.2018, time: 4:14
 */

return [
    'sql_table'         =>  'filestorage',

    // storage root
    'path_root'         =>  'files.etks',

    // path to files folder for FileManager WITHOUT FINAL '/'
    'path_fm_upload'    =>  'files.etks/upload',
    'path_fm_thumb'     =>  'files.etks/thumbs',

    // path to storage folder for all PDF/JPG files WITHOUT FINAL '/'
    'path_storage'      =>  'files.etks/storage',

    'max_upload_size'   =>  3 * 1024 * 1024,

    // store driver
    // 'db', 'disk', 'cloud'
    'store_driver'      =>  'disk',

    'cloud' => [

    ],
];

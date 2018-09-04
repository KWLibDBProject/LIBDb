<?php
/**
 * User: Karel Wintersky
 * Date: 25.08.2018, time: 4:14
 */

return [
    'sql_table'         =>  'filestorage',

    // storage root
    'path_root'         =>  'files.aait',

    // path to files folder for FileManager WITHOUT FINAL '/'
    'path_fm_upload'    =>  'files.aait/upload',
    'path_fm_thumb'     =>  'files.aait/thumbs',

    // path to storage folder for all PDF/JPG files WITHOUT FINAL '/'
    'path_storage'      =>  'files.aait/storage',

    // store driver
    // 'db', 'disk', 'cloud'
    'store_driver'      =>  'disk',

    'cloud' => [

    ],
];

<?php

/**
 * Задает конфигурацию модуля FileStorage
 */
return [
    'table'             =>  'filestorage',

    // path to files folder for FileManager
    'path_files'        =>  'files.etks',

    // path to storage folder for all PDF/JPG files
    'path_storage'      =>  'storage.etks',

    'save_to_db'        =>  false,
    'save_to_disk'      =>  true,
    'return_data_from'  =>  'disk'
];

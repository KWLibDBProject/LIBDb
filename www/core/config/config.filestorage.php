<?php

/**
 * Задает конфигурацию модуля FileStorage
 */
return [
    'table'             => 'filestorage',
    'path'              => 'files/storage/',
    'save_to_db'        => false,
    'save_to_disk'      => true,
    'return_data_from'  => 'disk'
];

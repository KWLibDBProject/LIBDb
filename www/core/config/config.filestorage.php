<?php

/**
 * Задает конфигурацию модуля FileStorage
 */
class FileStorageConfig
{
    public static $config = array(
        'table'             => 'filestorage',
        'path'              => '/files/storage/',
        'save_to_db'        => false,
        'save_to_disk'      => true,
        'return_data_from'  => 'disk'
    );

}

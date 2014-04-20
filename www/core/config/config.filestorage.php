<?php

class FileStorageConfig
{
    public static $config = array(
        'table'             => 'filestorage',
        'path'              => '/files/storage/',
        'save_to_db'        => true,
        'save_to_disk'      => true,
        'return_data_from'  => 'disk'
    );

}


?>
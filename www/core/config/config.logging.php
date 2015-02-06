<?php

/**
 * Задает конфигурацию модуля kwLogger
 */
class kwLoggerConfig
{
    // mysql-table for logging event data
    public static $log_table   =   'eventlog';

    // path to local errors.log file
    public static $log_file    =   '/core/config/errors.log';

    // key in $_SESSION for logging informaton about logged user
    public static $log_userid_key_in_session    = 'u_username';

    // formatting rule for converting timestamp to datetime
    public static $log_datetime_format = '%Y-%m-%d %H:%M:%S';
}


?>
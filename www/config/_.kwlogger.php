<?php

/**
 * Задает конфигурацию модуля kwLogger
 */

return [
    /**
     * mysql-table for logging event data
     */
    'log_table_event'       =>  'eventlog',

    /**
     * mysql-table for logging download events
     */
    'log_table_download'    =>  'eventlog_download',

    /* path to local errors.log file
     * владелец файла должен быть пользователь апача
     * @todo: '$/core/config/errors.log'
     */
    'log_file'              =>  'core/config/errors.log',

    /**
     * formatting rule for converting timestamp to datetime
     */
    'log_datetime_format'   =>  'Y-m-d H:m:s'
];

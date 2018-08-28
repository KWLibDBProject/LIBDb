<?php
/**
 * User: Arris
 * Date: 28.08.2018, time: 4:38
 */

interface kwLoggerInterface {
    public static function init(\PDO $dbc, $config);
    public static function logEventDownload($affected_element = 0, $referrer = '');
    public static function logEvent($action='?', $affected_table='*', $affected_element='-', $comment='-');
    public static function logEventToFile($dataset);
}

class kwLogger implements kwLoggerInterface {
    private static $log_datetime_format;
    private static $config;

    /**
     * @var \PDO $dbc
     */
    private static $dbc;

    public static function init(\PDO $dbc, $config)
    {
        self::$log_datetime_format = $config['log_datetime_format'];
        self::$config = $config;
        self::$dbc = $dbc;
    }

    public static function logEventDownload($affected_element = 0, $referrer = '')
    {
        $dataset = [
            'element'       =>  $affected_element,
            'referrer'      =>  $referrer,
            'ip'            =>  self::getIP(),
        ];

        $query = DB::makeInsertQuery(self::$config['log_table_download'], $dataset);

        if (!self::$dbc->prepare($query)->execute($dataset)) {
            self::logEventToFile($dataset);
        };
    }

    public static function logEvent($action='?', $affected_table='*', $affected_element='-', $comment='-')
    {
        $dataset = [
            'action'        =>  $action,
            'table'         =>  $affected_table,
            'element'       =>  $affected_element,
            'comment'       =>  $comment,
            'ip'            =>  self::getIP(),
            'user'          =>  $_COOKIE[ Config::get('auth:cookies/user_id') ] ?? -1
        ];

        $query = DB::makeInsertQuery(self::$config['log_table_event'], $dataset);

        if (!self::$dbc->prepare($query)->execute($dataset)) {
            self::logEventToFile($dataset);
        };
    }

    public static function logEventToFile($dataset)
    {
        if (is_array($dataset)) {
            $message = json_encode($dataset);
        } elseif (is_string($dataset)) {
            $message = $dataset;
        } else {
            $message = (string)$dataset;
        }

        $filename = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . self::$config['log_file'];
        $f = fopen( $filename , 'a+' );
        fwrite($f, $message );
        fclose($f);
    }


    private static function timestampToDate()
    {
        return (new DateTime())->format( self::$log_datetime_format );
    }

    private static function getIP()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ipAddress = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipAddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ipAddress = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ipAddress = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ipAddress = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ipAddress = getenv('REMOTE_ADDR');
        } else {
            $ipAddress = '127.0.0.1';
        }

        return $ipAddress;
    }
}

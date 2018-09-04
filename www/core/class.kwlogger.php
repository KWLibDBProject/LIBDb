<?php
/**
 * User: Karel Wintersky
 * Date: 28.08.2018, time: 4:38
 */

interface kwLoggerInterface {
    /**
     * @param PDO $dbc
     * @param $config
     * @return mixed
     */
    public static function init(\PDO $dbc, $config);

    /**
     * @param int $affected_element
     * @param string $referrer
     * @return mixed
     */
    public static function logEventDownload($affected_element = 0, $referrer = '');

    /**
     * @param string $action
     * @param string $affected_table
     * @param string $affected_element
     * @param string $comment
     * @return mixed
     */
    public static function logEvent($action='?', $affected_table='*', $affected_element='-', $comment='-');

    /**
     * @param $dataset
     * @return mixed
     */
    public static function logEventToFile($dataset);
}

/**
 * Class kwLogger
 */
class kwLogger implements kwLoggerInterface {
    private static $log_datetime_format;
    private static $config;

    /**
     * @var \PDO $dbc
     */
    private static $dbc;

    /**
     * Инициализирует статический класс
     *
     * @param PDO $dbc
     * @param $config
     */
    public static function init(\PDO $dbc, $config)
    {
        self::$log_datetime_format = $config['log_datetime_format'];
        self::$config = $config;
        self::$dbc = $dbc;
    }

    /**
     * Логгирует событие скачивания файла
     *
     * @param int $affected_element
     * @param string $referrer
     */
    public static function logEventDownload($affected_element = 0, $referrer = '')
    {
        $dataset = [
            'element'       =>  $affected_element,
            'referrer'      =>  $referrer,
            'ip'            =>  self::getIP(),
        ];

        $query = self::makeInsertQuery(self::$config['log_table_download'], $dataset);

        if (!self::$dbc->prepare($query)->execute($dataset)) {
            self::logEventToFile($dataset);
        };
    }

    /**
     * Логгирует произвольное событие
     *
     * @param string $action
     * @param string $affected_table
     * @param string $affected_element
     * @param string $comment
     */
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

        $query = self::makeInsertQuery(self::$config['log_table_event'], $dataset);

        if (!self::$dbc->prepare($query)->execute($dataset)) {
            self::logEventToFile($dataset);
        };
    }

    /**
     * Записывает логгируемое событие в файл лога. Это происходит, если база недоступна.
     *
     * @param $dataset
     */
    public static function logEventToFile($dataset)
    {
        $current_time = (new DateTime())->format( self::$log_datetime_format );

        if (is_array($dataset)) {
            $dataset['time'] = $current_time;
            $message = json_encode($dataset);
        } elseif (is_string($dataset)) {
            $message = "'{$current_time}' : {$dataset}";
        } else {
            $message = "'{$current_time}'" . (string)$dataset;
        }

        $filename = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . self::$config['log_file'];
        $f = fopen( $filename , 'a+' );
        fwrite($f, $message );
        fclose($f);
    }

    /**
     * возвращает текущий IPv4
     * @return array|false|string
     */
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

    /**
     * Копия DB::makeInsertQuery(), добавлена для независимости от класса DB
     *
     * @param $tablename
     * @param $dataset
     * @return string
     */
    private static function makeInsertQuery($tablename, $dataset)
    {
        $query = '';
        $r = [];

        if (empty($dataset)) {
            $query = "INSERT INTO {$tablename} () VALUES (); ";
        } else {
            $query = "INSERT INTO `{$tablename}` SET ";

            foreach ($dataset as $index=>$value) {
                $r[] = "\r\n `{$index}` = :{$index}";
            }

            $query .= implode(', ', $r) . ' ;';
        }

        return $query;
    }
}

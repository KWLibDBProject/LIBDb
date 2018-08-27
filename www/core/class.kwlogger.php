<?php
/* version 1.2 */

class kwLogger
{
    private static $mysqli_link;

    private static $config = [];

    public static function init($mysqli_link, $config)
    {
        self::$mysqli_link = $mysqli_link;
        self::$config = $config;
    }

    /**
     * convert query record to SQL statement
     *
     * @param $record
     * @param string $where
     * @return string
     */
    private static function makeInsertStatement($table, $record, $where="")
    {
        $query = "INSERT INTO {$table} ";

        $keys = " ( ";
        $vals = " ( ";
        foreach ($record as $key => $val) {
            $keys .= "`" . $key . "`" . ", ";
            $vals .= "'".$val."', ";
        }
        $query .= trim($keys,", ") . ") VALUES " . trim($vals,", ") . ") ".$where;
        return $query;
    }

    /**
     * prepare event record for export to specific format: db|json|csv
     *
     * @return string
     */
    private static function ConvertTimestampToDate()
    {
        return (new DateTime())->format( self::$config['log_datetime_format'] );
    }

    private static function prepare( $array, $target = 'db' )
    {
        $return = null;

        if ($target == 'db' || $target == 'mysql') {

            foreach( $array as $key => $value )
                $return [ $key ] = mysqli_real_escape_string(self::$mysqli_link,  $value );

        } elseif ($target == 'json' || $target == 'file') {

            $return = json_encode($array) . "\r\n";

        } elseif ($target == 'csv') {

            // see http://stackoverflow.com/a/16353448
            $fp = fopen('php://temp', 'r+');
            fputcsv($fp, $array, ',', '"');
            rewind($fp);
            $data = fread($fp, 1048576);
            fclose($fp);
            $return = rtrim($data, "\n");

        }
        return $return;
    }

    /**
     *
     * @param int $affected_element
     * @param string $referrer
     * @return int
     */
    public static function logEventDownload($affected_element = 0, $referrer = '')
    {
        $entry = [
            'element'       =>  $affected_element,
            'referrer'      =>  $referrer,
            'ip'            =>  self::getIP(),
        ];

        $query = self::makeInsertStatement( self::$config['log_table_download'], $entry);

        mysqli_query(self::$mysqli_link, $query )
            or self::_die('Error addind data to eventlog_download table, query data saved to error.log : ' . $query);

        return mysqli_errno(self::$mysqli_link);
    }

    /**
     * Log custom event
     *
     * @param string $action
     * @param string $affected_table
     * @param string $affected_element
     * @param string $comment
     * @return int
     */
    public static function logEvent($action='?', $affected_table='*', $affected_element='-', $comment='-')
    {
        $entry = [
            'action'        =>  $action,
            'table'         =>  $affected_table,
            'element'       =>  $affected_element,
            'comment'       =>  $comment,
            'ip'            =>  self::getIP(),
            'user'          =>  $_COOKIE[ Config::get('auth:cookies/user_id') ] ?? -1
        ];
        $query = self::makeInsertStatement(self::$config['log_table_event'], $entry);
        mysqli_query(self::$mysqli_link, $query )
            or self::_die('Error addind data to eventlog table, query data saved to error.log : ' . $query);
        return mysqli_errno(self::$mysqli_link);
    }



    /* override die function */
    public static function _die($message = '-', $dataset = array())
    {
        $entry = array(
            'action'        =>  'critical',
            'table'         =>  '-',
            'element'       =>  self::prepare( $dataset , 'csv' ),
            'comment'       =>  $message,
            'ip'            =>  self::getIP(),
            'datetime'      =>  self::ConvertTimestampToDate(),
            'user'          =>  $_SESSION[ Config::get('auth:session/user_id') ] ?? 0
        );
        $die_message = '';

        if (!empty($dataset)) {
            $entry [ 'element' ] = self::prepare( $dataset , 'csv' );
            $die_message = print_r($dataset, true);
        }

        $filename = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . self::$config['log_file'];

        $f = fopen( $filename , 'a+' );
        fwrite($f, self::prepare( $entry, 'file' ));
        fclose($f);
        die( $message . $die_message);
    }

    /**
     * Returns IP address
     * @return string $ip
     */
    protected static function getIP()
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


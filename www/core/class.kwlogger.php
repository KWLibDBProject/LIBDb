<?php
/* version 1.1 */
require_once('config/config.logging.php');

class kwLogger extends kwLoggerConfig
{
    private static $mysqli_link;

    public static function init($mysqli_link)
    {
        self::$mysqli_link = $mysqli_link;
    }

    /**
     * convert query record to SQL statement
     *
     * @param $record
     * @param string $where
     * @return string
     */
    private static function makeInsertStatement($record, $where="")
    {
        $table = self::$log_table;
        $query = "INSERT INTO $table ";

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
        return strftime(self::$log_datetime_format);
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

    /* log event */
    public static function logEvent($action='?', $affected_table='*', $affected_element='--', $comment = 'none')
    {
        $entry = array(
            'action'        =>  $action,
            'table'         =>  $affected_table,
            'element'       =>  $affected_element,
            'comment'       =>  $comment,
            'ip'            =>  $_SERVER['REMOTE_ADDR'],
            'datetime'      =>  self::ConvertTimestampToDate(),
            // нельзя получить доступ к сессии без session_start() , поэтому сохраняем данные из кукисов
            // 'user'          =>  (isset($_SESSION[ self::$log_userid_key_in_session ])) ? $_SESSION[ self::$log_userid_key_in_session ] : -1
            'user'          => $_COOKIE[ self::$log_userid_key_in_cookies ] ?? -1
        );
        $query = self::makeInsertStatement($entry);
        mysqli_query(self::$mysqli_link, $query ) or self::_die('Error addind data to eventlog table, query data saved to error.log : ' . $query);
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
            'ip'            =>  $_SERVER['REMOTE_ADDR'],
            'datetime'      =>  self::ConvertTimestampToDate(),
            'user'          =>  $_SESSION[ self::$log_userid_key_in_session ] ?? 0
        );
        $die_message = '';
        if (!empty($dataset)) {
            $entry [ 'element' ] = self::prepare( $dataset , 'csv' );
            $die_message = print_r($dataset, true);
        }

        $filename = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . self::$log_file;

        $f = fopen( $filename , 'a+' );
        fwrite($f, self::prepare( $entry, 'file' ));
        fclose($f);
        die( $message . $die_message);
    }



    /* ================================= TEST FUNCTIONS  ================================= */
    public static function log_to_file($action='?', $affected_table='*', $affected_element='--', $comment = 'none')
    {
        $entry = array(
            'action'        =>  $action,
            'table'         =>  $affected_table,
            'element'       =>  $affected_element,
            'comment'       =>  $comment,
            'ip'            =>  $_SERVER['REMOTE_ADDR'],
            'datetime'      =>  self::ConvertTimestampToDate(),
            'user'          =>  $_SESSION[ self::$log_userid_key_in_session ] ?? 0
        );
        $f = fopen( $_SERVER['DOCUMENT_ROOT'].self::$log_file , 'a+' );
        fwrite($f, self::prepare( $entry, 'file' ));
        fclose($f);
    }

    public static function log_to_csv($action='?', $affected_table='*', $affected_element='--', $comment = 'none')
    {
        $entry = array(
            'action'        =>  $action,
            'table'         =>  $affected_table,
            'element'       =>  $affected_element,
            'comment'       =>  $comment,
            'ip'            =>  $_SERVER['REMOTE_ADDR'],
            'datetime'      =>  self::ConvertTimestampToDate(),
            'user'          =>  $_SESSION[ self::$log_userid_key_in_session ] ?? 0
        );
        $f = fopen( $_SERVER['DOCUMENT_ROOT'].self::$log_file , 'a+' );
        fputcsv( $f, $entry );
        fclose($f);
    }

    public static function _print_error_string($action='?', $affected_table='*', $affected_element='--', $comment = 'none')
    {
        $entry = array(
            'action'        =>  $action,
            'table'         =>  $affected_table,
            'element'       =>  $affected_element,
            'comment'       =>  $comment,
            'ip'            =>  $_SERVER['REMOTE_ADDR'],
            'datetime'      =>  self::ConvertTimestampToDate(),
            'user'          =>  $_SESSION[ self::$log_userid_key_in_session ] ?? 0
        );
        return self::prepare( $entry, 'csv' );
    }

}


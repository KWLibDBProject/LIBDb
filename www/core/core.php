<?php
// функции ядра

/**
 * @param $filename
 * @return string
 */
function floadpdf($filename)
{
    $fh = fopen($filename,"rb");
    $real_filesize = filesize($filename);
    $blobdata = fread($fh, $real_filesize);
    fclose($fh);
    return $blobdata;
}

/**
 * @param $filename
 * @return string
 */
function floadfile($filename)
{
    $fh = fopen($filename,"rb");
    $real_filesize = filesize($filename);
    $blobdata = fread($fh, $real_filesize);
    fclose($fh);
    return $blobdata;
}



/**
 * @param bool $debugmode
 * @return bool
 */
function isAjaxCall($debugmode=false)
{
    $debug = (isset($debugmode)) ? $debugmode : false;
    return ((!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) || ($debug);
}

/**
 * @param $url
 */
function Redirect($url)
{
    if (headers_sent() === false) {
        header('Location: '.$url );
    } else {
        die('Headers sent');
    }
    exit;
}

/**
 * Возвращает 1 если мы залогинены в системе.
 * @return int
 */
function isLogged()
{
    // вот тут мы проверямем куки и сессию на предмет "залогинились ли мы"
    $we_are_logged = !empty($_SESSION);
    $we_are_logged = $we_are_logged && isset($_SESSION['u_id']);
    $we_are_logged = $we_are_logged && $_SESSION['u_id'] !== -1;
    $we_are_logged = $we_are_logged && isset($_COOKIE['u_libdb_logged']);
    return (int) $we_are_logged ;

    //@todo : перенести в CONFIG.INI названия проверяемых в сессии и куках переменных
}

/**
 * Если мы не залогинены (проверяем функцией isLogged() ) - переход по указанному url.
 * @param $path
 * @return void
 * */
function ifNotLoggedRedirect($path = "/")
{
    if (!isLogged()) { header('Location: '.$path); die(); }
}


/**
 * @param $str
 */
function printr($str)
{
    echo '<pre>'.print_r($str,true).'</pre>';
}

/* Три функции возврата данных в option соотв. селекта */
/* объявление переехало в frontend_.php */

/**
 * @param $row
 * @param $lang
 * @param $withoutid
 * @return string
 */
function returnBooksOptionString($row, $lang, $withoutid)
{
    // @todo: ВАЖНО: ТУТ ЗАДАЕТСЯ ФОРМАТ ВЫВОДА ДАННЫХ В СЕЛЕКТ (оформить функцией на основе шаблона? )
    // по идее можно и с шаблоном, но ну нафиг
    /*     switch ($lang) {
            case 'en': {
                $name = $row['name_en'];
                $title = $row['title_en'];
                break;
            }
            case 'ru': {
                $name = $row['name_ru'];
                $title = $row['title_ru'];
                break;
            }
            case 'uk': {
                $name = $row['name_uk'];
                $title = $row['title_uk'];
                break;
            }
        } */
    $id = ($withoutid==1) ? '' : "[{$row['id']}] " ;

    $title = ($row['title'] != '') ? $row['title'] : 'Unnamed';

    return $id."\"$title\"";
}

/**
 * @param $row
 * @param $lang
 * @param $withoutid
 * @return string
 */
function returnAuthorsOptionString($row, $lang, $withoutid)
{
    // @todo: ВАЖНО: ТУТ ЗАДАЕТСЯ ФОРМАТ ВЫВОДА ДАННЫХ В СЕЛЕКТ (оформить функцией на основе шаблона? )
    // по идее можно и с шаблоном, но ну нафиг
    $name = ''; $title = '';
    $id = ($withoutid==1) ? '' : "[{$row['id']}] " ;
    switch ($lang) {
        case 'en': {
            $name = $row['name_en'];
            $title = $row['title_en'];
            break;
        }
        case 'ru': {
            $name = $row['name_ru'];
            $title = $row['title_ru'];
            break;
        }
        case 'uk': {
            $name = $row['name_uk'];
            $title = $row['title_uk'];
            break;
        }
    }
    return $id.$name." , ".$title;
}

/**
 * @param $row
 * @param $lang
 * @param $withoutid
 * @return string
 */
function returnTopicsOptionString($row, $lang, $withoutid)
{
    //ТУТ ЗАДАЕТСЯ ФОРМАТ ВЫВОДА ДАННЫХ В СЕЛЕКТ
    $title = '';
    switch ($lang) {
        case 'en': {
            $title = $row['title_en'];
            break;
        }
        case 'ru': {
            $title = $row['title_ru'];
            break;
        }
        case 'uk': {
            $title = $row['title_uk'];
            break;
        }
    }
    $id = ($withoutid==1) ? '' : "[{$row['id']}] " ;
    $title = ($title != '') ? $title : '< NONAME >';

    return $id.$title;
}

/**
 * @param $row
 * @param $lang
 * @param $withoutid
 * @return string
 */
function returnNewsOptionString($row, $lang, $withoutid) // © Thomas Moroh
{
    $id = ($withoutid==1) ? '' : "[{$row['id']}] " ;
    switch ($lang) {
        case 'en': {
            $name = $row['text_en'];
            $title = $row['title_en'];
            break;
        }
        case 'ru': {
            $name = $row['text_ru'];
            $title = $row['title_ru'];
            break;
        }
        case 'uk': {
            $name = $row['text_uk'];
            $title = $row['title_uk'];
            break;
        }
    }
    return $id."$name $title";
}

/*
sell also:
http://www.tools4noobs.com/online_php_functions/date_parse/
http://php.fnlist.com/date_time/mktime
http://www.php.net/manual/ru/function.mktime.php
*/

/**
 * @param $str_date
 * @return array
 */
function ConvertDateToArray($str_date)
{
    $date_as_array = date_parse_from_format('d.m.Y', $str_date);
    return $date_as_array;
}

/**
 * @param $str_date
 * @param string $format
 * @return int
 */
function ConvertDateToTimestamp($str_date, $format="d.m.Y")
{
    $date_array = date_parse_from_format($format, $str_date);
    return mktime(12, 0, 0, $date_array['month'], $date_array['day'], $date_array['year']);
}

/**
 * @param string $format
 * @return string
 */
function ConvertTimestampToDate($format = '%Y-%m-%d %H:%M:%S')
{
    return strftime($format, time());
}

/*
Converts value (filesize) to human-friendly view like '5.251 M', 339.645 K or 4.216 K
$value : converted value
$closeness : number of digits after dot, default 0
*/
/**
 * @param $value
 * @param int $closeness
 * @return string
 */
function ConvertToHumanBytes($value, $closeness = 0) {
    $filesizename = array(" Bytes", " K", " M", " G", " T", " P", " E", " Z", " Y");
    return $value ? round($value / pow(1024, ($i = (int)floor(log($value, 1024)))), $closeness) . $filesizename[$i] : '0 Bytes';
}


/**
 * @param $string
 */
function println( $string )
{
    print($string . '<br/>' . "\r\n");
}

/**
 * @todo comment
 * @param $data
 * @param $allowed_values_array
 * @return $data if it is in allowed values array, NULL otherwise
 */
function getAllowedRef( $data, $allowed_values_array )
{
    $key = array_search($data, $allowed_values_array);
    return ($key !== FALSE )
        ? $allowed_values_array[ $key ]
        : FALSE;
}

/**
 * Проверяет заданную переменную на допустимость (на основе массива допустымых значений)
 * и если находит - возвращает её. В противном случае возвращает NULL.
 * @param $data
 * @param $allowed_values_array
 */
function getAllowedValue( $data, $allowed_values_array )
{
    if (empty($data)) {
        return NULL;
    } else {
        $key = array_search($data, $allowed_values_array);
        return ($key !== FALSE )
            ? $allowed_values_array[ $key ]
            : NULL;
    }
}

/**
 * Эквивалент isset( array[ key ] ) ? array[ key ] : default ;
 * at PHP 7 useless, z = a ?? b;*
 * @param $array
 * @param $key
 * @param $default
 * @return mixed
 */
function at($array, $key, $default)
{
    return isset($array[$key]) ? $array[$key] : $default;
}


function ossl_encrypt($data)
{
    global $CONFIG;
    $CONFIG['OPENSSL_ENCRYPTION_KEY'];

    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($data, $cipher, $CONFIG['OPENSSL_ENCRYPTION_KEY'], $options = OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $CONFIG['OPENSSL_ENCRYPTION_KEY'], $as_binary = true);
    $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);

    return $ciphertext;
}

function ossl_decrypt($data)
{
    global $CONFIG;

    $c = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len = 32);
    $ciphertext_raw = substr($c, $ivlen + $sha2len);
    $decrypted_data = openssl_decrypt($ciphertext_raw, $cipher, $CONFIG['OPENSSL_ENCRYPTION_KEY'], $options = OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $CONFIG['OPENSSL_ENCRYPTION_KEY'], $as_binary = true);


    return (hash_equals($hmac, $calcmac)) ? $decrypted_data : null;
}
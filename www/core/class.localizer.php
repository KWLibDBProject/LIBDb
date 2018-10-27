<?php

/**
 * User: Arris
 *
 * Class Localizer
 *
 * Date: 27.10.2018, time: 9:59
 */
class Localizer
{
    private static $config;
    private static $config_path;
    private static $dictionary;


    /**
     * Загружает конфиг словарей
     *
     * @param $config_path
     */
    public static function init($config_path)
    {
        $path = preg_replace('/^\$/', $_SERVER['DOCUMENT_ROOT'], $config_path, 1);
        $file =  '_index.json';

        if (is_readable($path . $file)) {
            self::$config = json_decode( file_get_contents($path . $file) , true);
            self::$config_path = $path;
        }
    }

    /**
     * Загружает словарь
     *
     * @param $locale
     */
    public static function setLocale($locale)
    {
        if (array_key_exists($locale, self::$config)) {
            $dictionary_file_name = self::$config_path . $locale . '.json';

            if (is_readable($dictionary_file_name)) {
                self::$dictionary = json_decode( file_get_contents($dictionary_file_name), true);
            } else {
                self::$dictionary = [];
            }
        }
    }

    /**
     *
     *
     * @param $message_id
     * @param mixed ...$args
     * @return string
     */
    public static function get($message_id, ...$args)
    {
        $string = array_key_exists($message_id, self::$dictionary) ? self::$dictionary[$message_id] : $message_id;

        return (func_num_args() > 1) ? vsprintf($string, $args) : $string;
    }

    /**
     * @param $message_id
     * @param mixed ...$args
     * @return string
     */
    public static function __($message_id, ...$args)
    {
        return self::get($message_id, ...$args);
    }

    public function __invoke($message_id)
    {
        return $message_id;
    }


}
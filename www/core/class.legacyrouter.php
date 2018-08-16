<?php
/**
 * User: Karel Wintersky
 * Date: 16.08.2018, time: 6:50
 *
 * MOVE it to ArrisFramework
 */

/**
 * Class LegacyRouter
 *
 * Класс эмулирует работу роутера, но не для строки /foo/bar/id ,
 * а для ?foo=arg1&bar=arg2&...
 *
 * Метод bindRoute() определяет параметры foo, bar итд, по которым будет анализироваться роутинг
 *
 * Метод action() проверяет правило на соответствие параметрам строки запроса $_GET
 *
 * Метод get() отдает значение ключа key
 *
 * Метод default() вызывает функцию в любом случае, не попавшем в набор правил (во всех остальных случаях)
 */
class LegacyRouter {
    const GLUE = '/';

    /**
     * source, it's keys and values
     */
    private static $source = [];
    private static $keys = [];
    private static $values = [];

    private static $is_request_routed = false;

    public static function bindRoute($source, $route_keys, $default_settings = null) {
        self::$source = $source;

        foreach ($route_keys as $rk_value) {
            self::$keys[] = $rk_value;
            self::$values[] = $source[ $rk_value ] ?? $default_settings;
        }
    }

    public static function action($route_values, callable $callback, ...$args) {
        $is_callable = is_callable($callback);
        $can_call = true;

        if (!$is_callable) return false;

        // explode routing by glue
        if (gettype($route_values) == 'string') {
            $route_values = explode(self::GLUE, $route_values);
        }

        // check routing rule
        foreach ($route_values as $index => $value) {
            $source_value = self::$source[ self::$keys[ $index ] ] ?? null;
            $route_value = $route_values[ $index ];

            $can_call = $can_call && ($source_value == $route_value);
        }

        if ($is_callable && $can_call) {
            self::$is_request_routed = true;
            return call_user_func_array($callback, self::$source);
        }

        return false;
    }

    public static function get($index, $default_value){
        return (array_key_exists($index, self::$source) && self::$source[ $index ]) ? self::$source[ $index ] :  $default_value;
    }

    public static function default(callable $callback, ...$args){
        if (!self::$is_request_routed && is_callable($callback)) {
            return call_user_func_array($callback, self::$source);
        }

        return false;
    }
}

if (!function_exists('input')) {
    function input($index, $default_value) {
        return LegacyRouter::get($index, $default_value);
    }
}

/*

Инвоук

<?php
class x {
    function __invoke($b){
       return strtoupper($b);
    }
}
$x = new x();
print_r(array_map($x, array('a')));
?>





*/
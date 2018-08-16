<?php
/**
 * User: Karel Wintersky
 * Date: 16.08.2018, time: 6:50
 *
 * MOVE it to ArrisFramework
 */
/*

Правильное решение - сначала биндить правила (во внутреннюю структуру соответствия правило=>коллбэк

Потом вызывать start(), который проанализирует get, пройдется по правилам и вызовет нужное.

TODO: сделать распознание коллбэка как 'Class@Method' или 'Class@' (= __invoke), а не только как замыкания

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

    private static $namespace = '';

    public static function bindNamespace($namespace) {
        self::$namespace = $namespace;
    }

    public static function bindRoute($source, $route_keys, $default_settings = null) {
        self::$source = $source;

        foreach ($route_keys as $rk_value) {
            self::$keys[] = $rk_value;
            self::$values[] = $source[ $rk_value ] ?? $default_settings;
        }
    }

    public static function route($route_rule, $callback, ...$args){
        $namespace = self::$namespace;
        $parameters = $args; // $this->getParameters();

        $can_call = true;

        /*
        роутинг может быть:
        - строка
        - массив
        */
        if (empty($route_rule)) return false;

        // explode routing by glue
        if (gettype($route_rule) == 'string') {
            $route_rule = explode(self::GLUE, $route_rule);
        }

        // check routing rule
        foreach ($route_rule as $index => $value) {
            $source_value = self::$source[ self::$keys[ $index ] ] ?? null;
            $route_value = $route_rule[ $index ];

            $can_call = $can_call && ($source_value == $route_value);
        }

        if (!$can_call) return false; // на самом деле записываем в массив коллбеков сопоставление "правило" => "коллбэк"

        /* $callback может быть:
        - коллбэк как анонимная функция
        - строка без собачки (то есть имя функции) - функция или класс
        - строка с собачкой и методом
        - строка с :: и методом
        */

        if (is_callable($callback)) {
            self::$is_request_routed = true;
            return call_user_func_array($callback, self::$source);
        }

        // это 'Class@method'
        if (strpos($callback, '@') > 1) {
            $controller = explode('@', $callback);

            $className = ($namespace !== null && $controller[0][0] !== '\\') ? $namespace . '\\' . $controller[0] : $controller[0];

            $class = self::loadClass($className);
            $method = $controller[1] ?? null;

            // Это
            if (is_null($method)) {
                self::$is_request_routed = true;
                call_user_func_array($class, $parameters);
            }

            if (method_exists($class, $method) === false) {
                // throw new \Exception(sprintf('Method "%s" does not exist in class "%s"', $method, $className), 404);
                return false;
            }

            self::$is_request_routed = true;
            return call_user_func_array([$class, $method], $parameters);
        }

        // это коллбэк 'Class::method'
        if (strpos($callback, '::') > 1) {
            self::$is_request_routed = true;
            call_user_func_array($callback, $parameters);
        }

        if (function_exists($callback)) {
            self::$is_request_routed = true;
            return call_user_func_array($callback, self::$source);
        }

        return false;

    }


    public static function action($route_values, $callback, ...$args) {
        if (empty($route_values)) return false;

        $is_callable_function = is_callable($callback);
        $can_call = true;

        // if (!$is_callable_function) return false;

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

        /* Render callback function */
        if ($is_callable_function && $can_call) {
            self::$is_request_routed = true;
            return call_user_func_array($callback, self::$source);
        }

        return false;
    }

    public static function get_value($index, $default_value){
        return (array_key_exists($index, self::$source) && self::$source[ $index ]) ? self::$source[ $index ] :  $default_value;
    }

    public static function default(callable $callback, ...$args){
        if (!self::$is_request_routed && is_callable($callback)) {
            return call_user_func_array($callback, self::$source);
        }

        return false;
    }

    public static function start(){

    }

    /**
     * Из PeCee/SimpleRouter
     *
     * @param string $className
     * @return mixed
     * @throws Exception
     */
    private static function loadClass(string $className)
    {
        if (class_exists($className) === false) {
            // throw new Exception(sprintf('Class "%s" does not exist', $className), 404);
            return false;
        }
        return new $className();
    }
}

if (!function_exists('input')) {
    function input($index, $default_value) {
        return LegacyRouter::get_value($index, $default_value);
    }
}
/*
Примеры:

LegacyRouter::action('authors/info', 'Authors@Info');

LegacyRouter::action(['foo', 'bar'], function (){
    $id = input('id', 0);
    $j  = input('j', 0);
    echo "/foo/bar => id = {$id} , j = {$j}";
});

LegacyRouter::action('authors/info', function (){
    echo "/authors/info";
});


LegacyRouter::default(function (){
    echo "No other routers fired, called default router";
});




 */


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
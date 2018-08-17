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
 * Класс эмулирует работу роутера, но не для строки /foo/bar/id,
 *
 * а для ?foo=arg1&bar=arg2&...
 *
 * Метод bindNamespace() определяет неймспейс
 *
 * Метод bindRouteKeys() определяет параметры foo, bar итд, по которым будет анализироваться роутинг
 *
 * Метод bindRule() связывает правило роутинга и коллбэк
 *
 * Метод bindDefaultRule
 *
 * Метод get_value() отдает значение ключа переменной key в $_GET
 *
 * Метод default() вызывает функцию в любом случае, не попавшем в набор правил (во всех остальных случаях)
 */
class LegacyRouter {
    const VERSION = '1.4';
    const GLUE = '/';

    private static $source = [];
    private static $keys = [];

    private static $is_request_routed = false;

    private static $namespace = '';

    private static $CALLBACKS = [];

    private static $CALLBACK_DEFAULT = [];

    /* === PUBLIC METHODS === */

    /**
     * Устанавливает namespace для вызываемых методов
     *
     * @param $namespace
     */
    public static function bindNamespace($namespace) {
        self::$namespace = $namespace;
    }

    /**
     * определяет параметры, по которым будет анализироваться роутинг
     * @param $route_keys
     */
    public static function bindRouteKeys($route_keys) {
        foreach ($route_keys as $rk_value) {
            self::$keys[] = $rk_value;
        }
    }

    /**
     * Биндит коллбэк к правилу
     *
     * @param $route_rule
     * @param $callback
     * @param mixed ...$args
     */
    public static function bindRule($route_rule, $callback, ...$args) {
        if (is_array($route_rule)) {
            $route_rule = implode('/', $route_rule);
        }

        self::$CALLBACKS[] = [
            'rule'      =>  $route_rule,
            'callback'  =>  $callback,
            'params'    =>  $args
        ];
    }

    /**
     * Биндит действие по умолчанию
     * @param $callback
     * @param mixed ...$args
     */
    public static function bindDefaultRule($callback, ...$args) {
        self::$CALLBACK_DEFAULT = [
            'rule'      =>  '',
            'callback'  =>  $callback,
            'params'    =>  $args
        ];
    }

    /**
     * Запускает процесс парсинга правил
     *
     * @param $source
     * @return bool|mixed|null
     * @throws Exception
     */
    public static function start($source){
        self::$source = $source;

        // форыч по всем вариантам роутов
        foreach (self::$CALLBACKS as $ruleset) {
            $can_call = true;
            $rule_values = explode('/', $ruleset['rule']);

            // check routing rule
            foreach ($rule_values as $index => $rule_key) {
                $can_call = $can_call && ($source[ self::$keys[$index] ] == $rule_key);
            }

            if (!$can_call) continue;

            return self::call($ruleset['callback'], $ruleset['params']);
        }

        if (!self::$is_request_routed && !empty(self::$CALLBACK_DEFAULT)) {
            return self::call(self::$CALLBACK_DEFAULT['callback'], self::$CALLBACK_DEFAULT['params']);
        }

        return null;
    }

    /**
     * @param $index
     * @param $default_value
     * @return mixed
     */
    public static function input($index, $default_value){
        return (array_key_exists($index, self::$source) && self::$source[ $index ]) ? self::$source[ $index ] :  $default_value;
    }

    /* === PRIVATE METHODS === */

    /**
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

    /**
     * Вызывает коллбэк
     *
     * @param $callback
     * @param mixed ...$parameters
     * @return bool|mixed|null
     * @throws Exception
     */
    private static function call($callback, ...$parameters) {
        if (is_callable($callback)) {
            self::$is_request_routed = true;
            return call_user_func_array($callback, self::$source);
        }

        // вызов динамического класса
        if (strpos($callback, '@') > 1) {
            $controller = explode('@', $callback);

            $className = (self::$namespace !== null && $controller[0][0] !== '\\') ? self::$namespace . '\\' . $controller[0] : $controller[0];

            $class = self::loadClass($className);
            $method = $controller[1] ?? null;

            // Это инвоук класса - метода нет, только собачка
            if (is_null($method)) {
                self::$is_request_routed = true;
                return call_user_func_array($class, $parameters);
            }

            // это динамическая проверка метода в ЭКЗЕМПЛЯРЕ
            // а еще метод может быть статическим (по идее он проверяется a::b)

            if (method_exists($class, $method) === false) {
                // throw new \Exception(sprintf('Method "%s" does not exist in class "%s"', $method, $className), 404);
                die( sprintf('Method "%s" does not exist in class "%s"', $method, $className) );
                return false;
            }

            self::$is_request_routed = true;
            return call_user_func_array([$class, $method], $parameters);
        }

        // это коллбэк 'Class::method'
        if (strpos($callback, '::') > 1) {
            self::$is_request_routed = true;
            return call_user_func_array($callback, $parameters);
        }

        // это просто существует такая функция
        if (function_exists($callback)) {
            self::$is_request_routed = true;
            return call_user_func_array($callback, self::$source);
        }

        // а это инвоук класса без метода
        if (class_exists($callback)) {
            self::$is_request_routed = true;
            $className = (self::$namespace !== null && $callback !== '\\') ? self::$namespace . '\\' . $callback : $callback;
            $class = self::loadClass($className);

            return call_user_func_array($class, $parameters);
        }

        return null;
    }

}

// HELPERS

if (!function_exists('input')) {
    function input($index, $default_value) {
        return LegacyRouter::input($index, $default_value);
    }
}

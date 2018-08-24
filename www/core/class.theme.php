<?php

/**
 * User: Karel Wintersky
 *
 * Class Theme
 *
 * Date: 10.08.2018, time: 14:02
 *
 * It is a better version for ArrisFramework\App ,INI_Confg and other classes
 *
 * Added: set, caching
 *
 * @todo: move to ArrisFrameWork
 */
class Theme
{
    const VERSION = '1.2';
    private static $GLUE = '.';

    /**
     * Stores the configuration data
     *
     * @var array|null
     */
    protected static $data = [];

    /**
     * Caches the configuration data
     *
     * @var array
     */
    protected static $cache = [];


    /**
     * Constructor method and sets default options, if any
     *
     * @param string $filename
     * @param array $data
     * @param string $glue
     */
    public static function init(string $filename, array $data = [], $glue = '.')
    {
        self::$GLUE = $glue;

        $content = '';

        if (is_file($filename) && is_readable($filename)) {
            $content = file_get_contents($filename);
        }

        self::$data = array_merge( self::json_decode_commented($content, true), $data);
    }


    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public static function get($key, $default = null)
    {
        if (self::has($key)) {
            return self::$cache[$key];
        }
        return $default;
    }

    /**
     * @param $key
     * @return bool
     */
    public static function has($key)
    {
        // Check if already cached
        if (isset(self::$cache[$key])) {
            return true;
        }

        $segments = explode(self::$GLUE, $key);
        $root = self::$data;

        // nested case
        foreach ($segments as $segment) {
            if (array_key_exists($segment, $root)) {
                $root = $root[$segment];
                continue;
            } else {
                return false;
            }
        }

        // Set cache for the given key
        self::$cache[$key] = $root;
        return true;
    }

    /**
     * @param $key
     * @param $value
     */
    public static function set($key, $value)
    {
        $segs = explode(self::$GLUE, $key);
        $root = &self::$data;
        $cacheKey = '';
        // Look for the key, creating nested keys if needed
        while ($part = array_shift($segs)) {
            if ($cacheKey != '') {
                $cacheKey .= self::$GLUE;
            }
            $cacheKey .= $part;
            if (!isset($root[$part]) && count($segs)) {
                $root[$part] = array();
            }
            $root = &$root[$part];
            //Unset all old nested cache
            if (isset(self::$cache[$cacheKey])) {
                unset(self::$cache[$cacheKey]);
            }
            //Unset all old nested cache in case of array
            if (count($segs) == 0) {
                foreach (self::$cache as $cacheLocalKey => $cacheValue) {
                    if (substr($cacheLocalKey, 0, strlen($cacheKey)) === $cacheKey) {
                        unset(self::$cache[$cacheLocalKey]);
                    }
                }
            }
        }
        // Assign value at target node
        self::$cache[$key] = $root = $value;
    }

    /**
     * {@inheritDoc}
     */
    public static function all()
    {
        return self::$data;
    }


    public static function json_decode_commented ($data, $objectsAsArrays = false, $maxDepth = 512, $opts = 0) {
        $data = preg_replace('~
    (" (?:[^"\\\\] | \\\\\\\\ | \\\\")*+ ") | \# [^\v]*+ | // [^\v]*+ | /\* .*? \*/
  ~xs', '$1', $data);

        return json_decode($data, $objectsAsArrays, $maxDepth, $opts);
    }
}
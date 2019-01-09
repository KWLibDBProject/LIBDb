<?php
/**
 * User: Karel Wintersky
 */

/**
 * На сервере этот файл надо скопировать в config.php и задать настройки (темы, хранилища, кук) согласно выбранному сайту.
 * Для настройки БД смотри файл _.db.php (его нет в гите) 
 */
$VERSION = [
    'copyright' =>  'KW LIBDb Engine',
    'version'   =>  '1.132 (2018-09-22)',   
];

/**  Ключ выбора окружения (подключения к БД), см _.db.php  */
$DB_CONNECTION = '????';

/* Подключение частей конфигов */
$INCLUDE_DB         = include '_.db.php';
$INCLUDE_KWLOGGER   = include '_.kwlogger.php';

$INCLUDE_THEME      = include '_.theme.????.php';
$INCLUDE_STORAGE    = include '_.storage.????.php';
$INCLUDE_AUTH       = include '_.auth.????.php';

/* Определение главного блока конфигурации */
$CONFIG = [
    'database_connection'   =>  $DB_CONNECTION,
    'database'              =>  $INCLUDE_DB[ $DB_CONNECTION ],

    // Storage configuration
    'storage'               =>  $INCLUDE_STORAGE,

    // Задает конфигурацию модуля kwLogger
    'kwlogger'              =>  $INCLUDE_KWLOGGER,

    // Theme (путь к шаблонам и так далее)
    'frontend'  =>  [
        'theme'         =>  $INCLUDE_THEME,
        'assets_mode'   =>  'development',  // development | production
        'debug_mode'    =>  true            // выводить или нет информацию об использовании памяти?
    ],

    // Кука для определения языка сайта
    'cookie_site_language'  =>  $INCLUDE_AUTH['cookie:site_language'],

    // Авторизация: Кука для проверки логина
    'auth:cookies'          =>  $INCLUDE_AUTH['auth:cookies'],

    // Авторизация: Переменные в сессии
    'auth:session'          =>  $INCLUDE_AUTH['auth:session'],
    
    // Разрешенные справочники для редактора абстрактного справочника
    'allowed_abstract_refs' =>  [
        'ref_estaff_roles'
    ],

    // Таймаут для коллбэка в админке в секундах
    'callback_timeout'      =>  3600,

    'openssl'   =>  [
        'OPENSSL_ENCRYPTION_KEY'    =>  '????'
    ]
];

return $CONFIG;
 

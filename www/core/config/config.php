<?php
/**
 * User: Karel Wintersky
 */
$VERSION = [
    'copyright' =>  'KW LIBDb Engine',
    'version'   =>  '1.131 (2018-09-22)',
];

/**  Ключ выбора окружения (подключения к БД)  */
$DB_CONNECTION = 'blacktower_etks';

/* Подключение частей конфигов */
$INCLUDE_DB         = include '_.db.php';
$INCLUDE_KWLOGGER   = include '_.kwlogger.php';

$INCLUDE_THEME      = include '_.theme.etks.php';
$INCLUDE_STORAGE    = include '_.storage.etks.php';
$INCLUDE_AUTH       = include '_.auth.etks.php';

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
        'theme'     =>  $INCLUDE_THEME,
    ],

    // Кука для определения языка сайта
    'cookie_site_language'  =>  $INCLUDE_AUTH['cookie:site_language'],

    // Авторизация: Кука для проверки логина
    'auth:cookies'          =>  $INCLUDE_AUTH['auth:cookies'],

    // Авторизация: Переменные в сессии
    'auth:session'          =>  $INCLUDE_AUTH['auth:session'],

    // Мета-данные в шаблонах
    'frontend_meta' =>  [
        'copyright'     =>  $VERSION['copyright'],
        'version'       =>  $VERSION['version']
    ],

    'frontend_assets'   =>  [
        'assets_mode'   =>  'development' ,                 // тип ассетов : development | production
        'assets_version'=>  crc32( $VERSION['version'] ),   // git rev-parse --short HEAD
    ],

    // Разрешенные справочники для редактора абстрактного справочника
    'allowed_abstract_refs' =>  [
        'ref_estaff_roles'
    ],

    // Таймаут для коллбэка в админке
    'callback_timeout'      =>  3600,

    'openssl'   =>  [
        'OPENSSL_ENCRYPTION_KEY'    =>  'ab86d144e3f080b61c7c2e43'
    ]
];

return $CONFIG;
 

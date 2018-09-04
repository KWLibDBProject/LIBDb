<?php
/**
 * User: Karel Wintersky
 * Date: 25.08.2018, time: 4:06
 */

$INCLUDE_DB         = include 'config.db.php';
$INCLUDE_THEME      = include 'config.theme.etks.php';
$INCLUDE_STORAGE    = include 'config.storage.etks.php';
$INCLUDE_KWLOGGER   = include 'config.kwlogger.php';

/**
 * Ключ выбора окружения (подключения к БД)
 */
$DB_CONNECTION = 'blacktower_etks';

$VERSION = [
    'copyright' =>  'KW LIBDb Engine',
    'version'   =>  '1.126 (2018-09-24)'
];

// "database:{$x}" => 'x', // $x = 'x';

$CONFIG = [
    'database_connection'   =>  $DB_CONNECTION,

    'database'              =>  $INCLUDE_DB[ $DB_CONNECTION ],

    // 'database:docker57'  =>  $INCLUDE_DB['docker57'],

    // Storage configuration
    'storage'               =>  $INCLUDE_STORAGE,

    // Theme (путь к шаблонам и так далее)
    'frontend'  =>  [
        'theme'         =>  $INCLUDE_THEME,
    ],

    'frontend_meta' =>  [
        'copyright'     =>  $VERSION['copyright'],
        'version'       =>  $VERSION['version']
    ],

    'frontend_assets'   =>  [
        // тип ассетов
        'assets_mode'   =>  'development' , // development | production

        // git rev-parse --short HEAD
        'assets_version'=>  crc32( $VERSION['version'] )
    ],

    // Задает конфигурацию модуля kwLogger
    'kwlogger'              =>  $INCLUDE_KWLOGGER,

    // Кука для определения языка сайта
    'cookie_site_language'  => 'libdb_sitelanguage',

    // Авторизация: Кука для проверки логина
    'auth:cookies' => [
        'user_is_logged'    =>  'u_libdb_is_logged',
        'user_permissions'  =>  'u_libdb_permissions',
        'user_id'           =>  'u_libdb_userid'
    ],

    // Авторизация: Переменные в сессии
    'auth:session'   =>  [
        'user_is_logged'    =>  'u_libdb_is_logged',
        'user_permissions'  =>  'u_libdb_permissions',
        'user_id'           =>  'u_libdb_userid'
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
 

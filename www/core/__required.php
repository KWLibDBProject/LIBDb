<?php
/**
 * User: Karel Wintersky
 * Date: 31.07.2018, time: 16:57
 */
ini_set('pcre.backtrack_limit', 2*1024*1024); // 2 Mб
ini_set('pcre.recursion_limit', 2*1024*1024);

require_once 'class.config.php';
require_once 'class.filestorage.php';

require_once 'class.kwlogger.php';

require_once 'class.localizer.php'; // локализация сообщений

require_once 'websun.php';

require_once 'core.php';
require_once 'core.db.php';

require_once 'class.dbconnection.php';

Config::init(['../config/config.php']);
Config::set('version', get_engine_version());   // установка мета-данных и версий ассетов

$SID = session_id();
if(empty($SID)) session_start();

if (__ACCESS_MODE__ == 'admin' && !isLogged()) {
    Redirect('/core/admin.actions.php');
} elseif (__ACCESS_MODE__ == 'admin:main' && !isLogged()) {
    Redirect('/core/admin.actions.php');
} elseif (__ACCESS_MODE__ == 'admin:actions') {} elseif (__ACCESS_MODE__ == 'admin') {} else {}

$mysqli_link = ConnectDB();

/**
 * Init singletones and static modules
 */
DB::init(NULL, Config::get('database'));
FileStorage::init($mysqli_link, Config::get('storage'));
kwLogger::init(DB::getConnection(), Config::get('kwlogger'));

/**
 * Lazy load localized
 */
Localizer::init('$/template/_locale/');

/**
 * Check access rights for STORAGE directory
 */
FileStorage::checkStorageDirectoryACL();


<?php
/**
 * User: Karel Wintersky
 * Date: 31.07.2018, time: 16:57
 */

require_once 'config/config.php';
require_once 'core.php';
require_once 'core.db.php';
require_once 'core.kwt.php';
require_once 'core.filestorage.php';
require_once 'core.kwlogger.php';
require_once 'websun.php';

// require_once '../frontend.php';
/*  вызов нужного шаблона и его движка.
    путь до каталога шаблонов определяется во включаемом файле движка шаблона */
// require_once  '../template.bootstrap24.php';

$mysqli_link = ConnectDB();

kwLogger::init($mysqli_link);
FileStorage::init($mysqli_link);

// check errors
$storage_folder = FileStorage::getStorageDir();
$user_php_executor = exec('whoami');
$user_storage_owner = posix_getpwuid(fileowner( $storage_folder ))['name'];

if ($user_php_executor != $user_storage_owner) {
    echo <<<OWNERS_ERROR_MESSAGE
Owner of directory `<strong>{$storage_folder}</strong>` is `{$user_storage_owner}`. <br />
For create/write access to this directory you MUST set it's owner to {$user_php_executor} (Apache User).
OWNERS_ERROR_MESSAGE;
    die;
}

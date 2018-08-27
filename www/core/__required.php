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

require_once 'websun.php';

require_once 'core.php';
require_once 'core.db.php';

/*Config::init([
    'config/config.php',
    'config/config.db.php',

    'filestorage'   =>  'config/config.filestorage.php',
    'kwlogger'      =>  'config/config.logging.php'
]);*/
Config::init(['config/config.php']);

$SID = session_id();
if(empty($SID)) session_start();

if (__ACCESS_MODE__ == 'admin' && !isLogged()) {
    Redirect('/core/admin.actions.php');
} elseif (__ACCESS_MODE__ == 'admin:main' && !isLogged()) {
    Redirect('/core/admin.actions.php');
} elseif (__ACCESS_MODE__ == 'admin:actions') {} elseif (__ACCESS_MODE__ == 'admin') {} else {}

$mysqli_link = ConnectDB();


kwLogger::init($mysqli_link, Config::get('kwlogger'));
FileStorage::init($mysqli_link, Config::get('storage'));

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

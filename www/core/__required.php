<?php
/**
 * User: Karel Wintersky
 * Date: 31.07.2018, time: 16:57
 */

require_once 'core.php';
require_once 'core.db.php';
require_once 'core.kwt.php';
require_once 'core.filestorage.php';
require_once 'core.kwlogger.php';

// require_once '../frontend.php';
/*  вызов нужного шаблона и его движка.
    путь до каталога шаблонов определяется во включаемом файле движка шаблона */
// require_once  '../template.bootstrap24.php';

$mysqli_link = ConnectDB();

kwLogger::init($mysqli_link);
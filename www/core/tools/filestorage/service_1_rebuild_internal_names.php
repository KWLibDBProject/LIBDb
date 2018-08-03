<?php
require_once '../__required.php'; // $mysqli_link
require_once('core.filestorage.migration.php');

FileStorageMigration::init($mysqli_link);
FileStorageMigration::migrateRebuildInternalNames();

<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.filestorage.php');

$link = ConnectDB();

$result = FileStorage::recalcFilesSize();

kwLogger::logEvent('Maintenance', 'filestorage', '*',
    "{$result['total_files_found']} records in FILESTORAGE scanned.
    Fixed: {$result['total_files_fixed']} files.
    Errors: {$result['total_files_error']}.");

?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Recalc file sizes</title>
    <style>
        .note {
            color: blue;
        }
        .warning {
            color: red;
        }
        .stats {
            font-size: 120%;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="stats">
    Просканировано файлов: <span class="warning"><?=$result['total_files_found']?></span> <br/>
    Исправлено записей в БД: <span class="warning"><?=$result['total_files_fixed']?></span> <br/>
    Ошибочных записей в БД: <span class="warning"><?=$result['total_files_error']?></span> <br/>
</div>

<?php if (($result['total_files_fixed'] + $result['total_files_error'])>0) { ?>

<table class="" border="1" width="100%" id="exportable">
    <caption></caption>
    <tr>
        <th>Icon</th>
        <th>User filename</th>
        <th>Internal filename</th>
        <th>Old <br> filesize</th>
        <th>Status</th>
    </tr>

    <?php foreach($result['log'] as $i => $file) {?>
    <tr>
        <td class="center"><img src="<?=$file['icon']?>"></td>
        <td><?=$file['username']?></td>
        <td><?=$file['internal_name']?></td>
        <td class="center"><?=$file['filesize_old']?></td>
        <td class="center">
            <span class="note"><?=$file['status']?>
        </td>
    </tr>
    <?php } ?>
</table>

<?php } ?>

<hr>
<a href="/core/"><<< Назад в административный раздел</a>
</body>
</html>
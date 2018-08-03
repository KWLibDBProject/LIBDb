<?php
require_once '../__required.php'; // $mysqli_link

class FileStorageMigration extends FileStorage
{
    private static $mysqli_link;

    private static $config = array(
        'table' => 'filestorage',
        'path' => '/files/storage/',
        'save_to_db' => true,
        'save_to_disk' => true,
        'return_data_from' => 'disk'
    );

    public static function init($mysqli_link)
    {
        self::$mysqli_link = $mysqli_link;
    }

    /* возвращает имя таблицы хранилища */
    private static function getSQLTable()
    {
        return self::$config['table'];
    }

    private static function getRealFileName($filename)
    {
        return $_SERVER['DOCUMENT_ROOT'] . self::$config['path'] . $filename;
    }

    /* выгрузка файла из базы */
    private static function __getFileContent_db($id)
    {
        $table = self::getSQLTable();
        if (($id != null) && ($id != -1)) {
            $q = "SELECT content FROM {$table} WHERE id = {$id}";
            $r = @mysqli_query(self::$mysqli_link, $q);
            if ($r) {
                $d = mysqli_fetch_assoc($r);
                $ret = $d['content'];
            } else {
                $ret = null;
            }
        } else {
            $ret = null;
        }
        return $ret;
    }


    /* пишет на диск в файл контент из базы для файла с указанным id */
    private static function migrateExportSingleFile($name, $id)
    {
        $fh = fopen($name, "wb");
        $size = fwrite($fh, self::__getFileContent_db($id));
        fflush($fh);
        usleep(100000);
        print("{$size} bytes written. <br>\r\n ");
        flush();
        fclose($fh);
        return $size;
    }

    /* построение внутреннего имени на основе информации о файле */
    private static function getInternalFileName($fileinfo)
    {
        $a = explode('/', $fileinfo['filetype']);
        $t = microtime();
        $h = md5($fileinfo['username'] . '_' . $t);
        // mask: collection_relation_hash.extension
        $ret = "{$fileinfo['collection']}_{$fileinfo['relation']}_{$h}.{$a[1]}";
        return $ret;
    }

    /* public */
    public static function migrateExportFilesFromDB()
    {
        $s = 0;
        $n = 0;
        $table = self::getSQLTable();
        $path = $_SERVER['DOCUMENT_ROOT'] . self::$config['path'];
        $q = "SELECT id, internal_name FROM {$table}";
        $qr = @mysqli_query(self::$mysqli_link, $q);

        if ($qr) {
            while ($r = mysqli_fetch_assoc($qr)) {
                $id = $r['id'];
                $name = $r['internal_name'];
                print("Preparing to export file `{$name}` to `{$path}`... ");
                flush();

                $s += self::migrateExportSingleFile(self::getRealFileName($name), $id);

                $n++;
            }
        }
        print("Totally exported {$n} files. Total size: {$s} bytes");
    }

    /* сервисные функции МИГРАЦИИ */
    public static function migrateRebuildInternalNames()
    {
        $table = self::getSQLTable();
        $infos = array();
        $insert_qu_s = array();
        $q = "SELECT id FROM {$table}";
        $qr = @mysqli_query(self::$mysqli_link, $q);
        if ($qr) {
            while ($r = mysqli_fetch_assoc($qr)) {
                $info = self::getFileInfo($r['id']);

                $insert_qu_s [$r['id']] = MakeUpdate(array(
                    'internal_name' => self::getInternalFileName($info)
                ), $table, " WHERE id = {$r['id']}");
            }
        }
        foreach ($insert_qu_s as $id => $q) {
            mysqli_query(self::$mysqli_link, $q);
            print("Executed query: `{$q}` <br>\r\n");
            flush();
        }
    }


}


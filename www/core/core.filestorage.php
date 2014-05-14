<?php
require_once('core.php');
require_once('config/config.filestorage.php');

class FileStorage extends FileStorageConfig {

    /* возвращает blob-строку пустого PDF-файла */
    private  static function __getEmptyPDF()
    {
        $data = "JVBERi0xLjQNCjEgMCBvYmoNCjw8IC9UeXBlIC9DYXRhbG9nIC9PdXRsaW5lcyAyIDAgUiAvUGFnZXMgMyAwIFIgPj4NCmVuZG9iag0KMiAwIG9iag0KPDwgL1R5cGUgT3V0bGluZXMgL0NvdW50IDAgPj4NCmVuZG9iag0KMyAwIG9iag0KPDwgL1R5cGUgL1BhZ2VzIC9LaWRzIFs0IDAgUl0gL0NvdW50IDEgPj4NCmVuZG9iag0KNCAwIG9iag0KPDwgL1R5cGUgL1BhZ2UgL1BhcmVudCAzIDAgUiAvTWVkaWFCb3ggWzAgMCA2MTIgNzkyXSAvQ29udGVudHMgNSAwIFIgL1Jlc291cmNlcyA8PCAvUHJvY1NldCA2IDAgUiA+PiA+Pg0KZW5kb2JqDQo1IDAgb2JqDQo8PCAvTGVuZ3RoIDM1ID4+DQpzdHJlYW0NCoUgUGFnZS1tYXJraW5nIG9wZXJhdG9ycyCFDQplbmRzdHJlYW0gDQplbmRvYmoNCjYgMCBvYmoNClsvUERGXQ0KZW5kb2JqDQp4cmVmDQowIDcNCjAwMDAwMDAwMDAgNjU1MzUgZiANCjAwMDAwMDAwMDkgMDAwMDAgbiANCjAwMDAwMDAwNzQgMDAwMDAgbiANCjAwMDAwMDAxMTkgMDAwMDAgbiANCjAwMDAwMDAxNzYgMDAwMDAgbiANCjAwMDAwMDAyOTUgMDAwMDAgbiANCjAwMDAwMDAzNzYgMDAwMDAgbiANCnRyYWlsZXIgDQo8PCAvU2l6ZSA3IC9Sb290IDEgMCBSID4+DQpzdGFydHhyZWYNCjM5NA0KJSVFT0Y=";
        return base64_decode($data);
    }

    /* возвращает blob-строку картинки-плашки "нет изображения" */
    private  static function __getEmptyIMG()
    {
        // base64-строка для плашки "нет изображения" 240х180
        $noimg_240x180 = 'R0lGODlh8AC0ALMPAIiIiLu7u0RERBEREe7u7iIiIszMzN3d3TMzM2ZmZqqqqlVVVZmZmXd3dwAA';
        $noimg_240x180.= 'AP///yH5BAEAAA8ALAAAAADwALQAAAT/8MlJq7046827/2AojmRpnmiqrmzrvnAsz3Rt33iu73zv';
        $noimg_240x180.= '/8CgcEgsGo/IpHLJbDqf0Kh0Sq1ar9isdsvter/gsHhMLpvP6LR6zW673/C4fE6v2+/4vH7P7/v/';
        $noimg_240x180.= 'gIGCg4SFhoeIiYqLjI2Oj5CRkpOUlZaXmJmam5ydnp+goaKjpKWmp6ipqqusra6vsLGys7S1tre4';
        $noimg_240x180.= 'ubq7vL2+v8DBwsPExcbHyMnKy8zNzs/QVAqUBAHWBxYG1gFnBgANAgMOlAEO5gAWAubjZAwF6+vk';
        $noimg_240x180.= '6+gV6uZkCfDrA/Ln6fFiGMBDkABAAAL9HNCjYI9dmHcOBhjIVM5fPYAWqh3ksQ2hBm0b/y0oWMcg';
        $noimg_240x180.= 'hDZsGUDGuFalosJ/9yoYaOgggccHAPTBW3ghpwMBFBqs4/bgAAJ9BaZVIABA3LoFE4OuI8BggYAE';
        $noimg_240x180.= 'JSvMMwDxZ9QJARI4jcjT588J9oBKaMiTQL51Ar4+cEkUJ0a7ZycINVf3AdOj+tSKcMlz7d0HBsau';
        $noimg_240x180.= 'Q+DRrM7CFXwKdumAKGV4SiUQAKwz8wN7BTibW6B1tOKINz/rdNCAwoJ1StNKGDmawmadEsEOnWCW';
        $noimg_240x180.= 'gmTduyeIhic4xOXVMTVDHNCggVN6jvVB9m1OsGjLAxYAANCVNNp9AroOuNl1desJ+hDQTMDQAYLt';
        $noimg_240x180.= 'Y28ScDpe9dn55upPeG2uIMTJweHlEP9e1sFTF20OFLDdfyQchxx15kQlUIIV0NXBbwLypZl8cE1g';
        $noimg_240x180.= 'wGIevfWSBPAsgI1jN8GT1Yf5BfUViw545lJrsu1VGQUw0nPAOihZKEFvvFX3oz51mYWSbCM4uFp7';
        $noimg_240x180.= 'NVHAIwU+bvDbAeKM1ZdmZi1kVmYELIaeOQVQIGJmHe73ZDYIFmbWTNXlGBmYTGZF2DYiQggUlREF';
        $noimg_240x180.= 'aJZHSA4G13aAcnfXOgUIYGhDfUWpwW/8mdWXPgPw1FBqDX2Z12zzWFpWgAc0UJ5FFACmXnX2IADT';
        $noimg_240x180.= 'AIeGY5GSdzF6DqdPWQNYcSAQBtOAyAU4l64ZSEZbAV1qaGl1WRmWnLEOlSmBrSSCKiD/UXjuk+mb';
        $noimg_240x180.= '+YE5LZPI0cPqsb6CGeyNEzBwmpBJXvvdsYSmeqhcivZaHUTc6ApoVwtViu25YU6QpqZSRSgBfwgo';
        $noimg_240x180.= 'gJC5iK2TQFo2XmkPquoKIKfBgNo75E/wPsArgsQ1SLB9AwLG3gbtYhCdd7xOsFyQ5nCprI0oPZCw';
        $noimg_240x180.= 'pR9L0NUETin1bWGAjZcWfgmmtle+GEQJ5MRPNQsuptUFMKvGziIrZn5yHdDysiX3BI9+wU39wMxU';
        $noimg_240x180.= 'm8OYBCIu5JJaibU4bFRmeWexOVllCeFLsiEY8wMTOlCsX18J3erVHulKH0J9GrexxEXBUxAA6lwZ';
        $noimg_240x180.= 'stXXBsfd4ZyZem5E4RFKabo7OUlc/3l1LQeAiCMWDKd9ajXkWVcCbLdApMAdPXSGCwVYNz2B1zr4';
        $noimg_240x180.= 'YXUTWWHVFphVnOPIeXYbbnIhNm6Tmieo09zRsSUcbMaqtWN+N5X9WOtF7k2u0Z2fWfsHzN5bgQLD';
        $noimg_240x180.= '2bS7sFKuI1dw4qbnmWZiGa61BAfwByZk85jFegUiCnAz0YLpk+9wZL82YQ9lA1pT8ohSt5h9rwYH';
        $noimg_240x180.= 'sEbxbBBBa3BgGxzQyPyMhg6NZCCCG1yBRq4UjI1FowYmPOEMUqjCGLCwhS94IQxnSMMa2vCGOMyh';
        $noimg_240x180.= 'DnfIwx768IdADKIQh0jEIhrxiEhMohKXyMQmOvGJUIyiFKdIxSpa8YpYzKIWt8jFLjJ68YtgDKMY';
        $noimg_240x180.= 'x0jGMprxjGhMoxrXyMY2uvGNcIyjHOdIxzra8Y54zKMe98jHPvrxj0SMAAA7';
        return base64_decode($noimg_240x180);
    }

    /* возвращает имя таблицы хранилища */
    private static function getSQLTable()
    {
        return parent::$config['table'];
    }

    /* возвращает абсолютный путь до файла, _имя_ которого переданно параметром */
    private static function getRealFileName($filename)
    {
        return $_SERVER['DOCUMENT_ROOT'].parent::$config['path'].$filename;
    }

    /* построение внутреннего имени на основе информации о файле */
    private static function getInternalFileName($fileinfo)
    {
        $a = explode('/',$fileinfo['filetype']);
        $t = microtime();
        $h = md5($fileinfo['username'].'_'.$t);
        // mask: collection_relation_hash.extension
        $ret = "{$fileinfo['collection']}_{$fileinfo['relation']}_{$h}.{$a[1]}";
        return $ret;
    }

    /* выгрузка файла из базы */
    private static function __getFileContent_db($id)
    {
        $table = self::getSQLTable();
        if (($id != null) && ($id != -1)) {
            $q = "SELECT content FROM {$table} WHERE id = {$id}";
            $r = @mysql_query($q);
            if ($r) {
                $d = mysql_fetch_assoc($r);
                $ret = $d['content'];
            } else {
                $ret = null;
            }
        } else {
            $ret = null;
        }
        return $ret;
    }

    /* выгрузка файла из хранилища на диске */
    private static function __getFileContent_disk($id)
    {
        $table = self::getSQLTable();
        if (($id != null) && ($id != -1)) {
            $q = "SELECT filesize, internal_name FROM {$table} WHERE id = {$id}";
            $r = @mysql_query($q);
            if ($r) {
                $d = mysql_fetch_assoc($r);
                $internal_file_name = self::getRealFileName($d['internal_name']);
                if (file_exists($internal_file_name)) {
                    $h = fopen($internal_file_name, "rb");
                    $ret = fread($h, $d['filesize']);
                    fclose($h);
                } else {
                    $ret = null; // catch file not found in storage directory
                }
            } else {
                $ret = null; // catch error quering fileinfo from DB
            }
        } else {
            $ret = null; // catch unknown file id
        }
        return $ret;
    }

    /* добавляет контент файла в хранилище */
    private function appendFileContent($fileinfo, $fileid)
    {
        $file_content = floadfile($fileinfo['tempname']);

        if (parent::$config['save_to_disk']) {
            // save to file
            $filename = self::getRealFileName($fileinfo['internal_name']);
            $fh = fopen($filename, "wb");
            $return = fwrite($fh, $file_content);
            fflush($fh);
            fclose($fh);
            usleep(100000);// sleep 0.1 sec
        }

        if (parent::$config['save_to_db'])
        {
            $qc = MakeUpdate(array(
                'content' => mysql_escape_string($file_content)
            ), self::getSQLTable(), " WHERE id = {$fileid} ");
            $return = mysql_query($qc);
        }
        return $return;
    }




    /* ========================== PUBLIC SECTION ======================= */

    /* возвращает пустой файл для переданного Mime-типа */
    public static function getEmptyFile($type)
    {
        $ret = '';
        switch ($type) {
            case 'pdf' : {
                $ret = self::__getEmptyPDF();
                break;
            }
            case 'image': {
                $ret = self::__getEmptyIMG();
                break;
            }
        } // switch
        return $ret;
    }


    /* WRAPPER: возвращает контент файла по указанному идентификатору */
    public static function getFileContent($id)
    {
        $ret = '';
        if (parent::$config['return_data_from'] == 'table') {
            $ret = self::__getFileContent_db($id);
        } else if (parent::$config['return_data_from'] == 'disk') {
            $ret = self::__getFileContent_disk($id);
        } else $ret = null;
        return $ret;
    }

    /* ========================  Работа со взаимосвязями =================== */
    /* * @returns filestorage[rel]-> collection */
    public static function getCollectionByRel($rel)
    {
        $table = self::getSQLTable();
        $q = "SELECT collection FROM {$table} WHERE relation = {$rel}";
        $r = @mysql_query($q) or die($q);

        if ($r) {
            $record = mysql_fetch_assoc($r);
            return $record['collection'];
        } else {
            return -1;
        }
    }

    /*
     * Отдает значение ОТНОШЕНИЯ (relation), т.е. идентификатор владельца по указанному ID в хранилище.
     * Предполагается, что название коллекции нам известно (или по крайней мере мы его
     * проверим перед дальнейшим удалением):
     * Эта информация используется для обновления поля владельца в соответствующей
     * коллекции (books, authors, articles) на значение -1. Следом нужно удалить
     * файл из хранилища соответствующим вызовом.
    */
    public static function getRelById($id)
    {
        $table = self::getSQLTable();
        $q = "SELECT relation FROM {$table} WHERE id = $id";

        $r = @mysql_query($q) or die($q);

        if ($r) {
            $record = mysql_fetch_assoc($r);
            return $record['relation'];
        } else {
            return -1;
        }
    }

    /* =========================== ИНФОРМАЦИЯ О ФАЙЛЕ ======================== */

    /* возвращает служебную информацию по файлу из хранилища */
    public static function getFileInfo($id)
    {
        $table = self::getSQLTable();

        if (($id != null) && ($id != -1)) {
            $qp = "SELECT id, username, filesize, filetype, relation, collection FROM {$table} WHERE id = $id";
            $rp = @mysql_query($qp);
            if ($rp) {
                if (@mysql_num_rows($rp) == 1) {
                    $ret = mysql_fetch_assoc($rp);
                } else {
                    $ret = null;
                }
            } else $ret = null;
        } else {
            $ret = null;
        }
        return $ret;
    }


    /* =========================== ДОБАВЛЕНИЕ ФАЙЛОВ ========================= */



    /* Добавляет файл в базу.
     * $file_array  - Элемент массива $_FILES[<имя поля инпута>]
     * $related_id  - айди владельца файла, кто он - укажет параметр $collection
     * $collection  - коллекция, она же - обновляемая таблица, в поле... (см след. строка)
     * $related_field_in_table которой мы вставим новый id вставленного в хранилище файла.
     * таким образом делается перекрестное связывание:
     * в filestorage[id файла] хранит коллекцию и идентификатор владельца
     * в коллекции[id владельца] лежит идентификатор файла (либо -1, если файла нет)
     *
     * возвращает id файла, вставленного в базу или NULL
    */
    public static function addFile($file_array, $related_id, $collection, $related_field_in_table)
    {
        if ($file_array['tmp_name'] != '')
        {
            $file_info = array(
                'username' => $file_array['name'],
                'tempname' => ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? str_replace('\\','\\\\', ($file_array['tmp_name'])) : ($file_array['tmp_name']),
                'filesize' => $file_array['size'],
                'relation' => $related_id,
                'filetype' => $file_array['type'],
                'collection' => $collection
            );
            $file_info['internal_name'] = self::getInternalFileName($file_info);

            /* insert fileinfo to DB */
            $q = MakeInsert($file_info, self::getSQLTable());
            mysql_query($q) or Die("Death on $q");
            $last_file_id = mysql_insert_id() or Die("Не удалось получить id последнего добавленного файла !");

            self::appendFileContent($file_info, $last_file_id);

            $q_api = MakeUpdate(array(
                    $related_field_in_table => $last_file_id),
                $collection, " WHERE id= $related_id ");
            mysql_query($q_api) or Die("Death on update {$collection} table with request: ".$q_api);
            $ret = $last_file_id;
        } else {
            $ret = null;
        }
        return $ret;
    }



    /* =========================== УДАЛЕНИЕ ФАЙЛОВ =========================== */

    /*  удаляет из хранилища (таблицы) файл по соответствующему ID
     * (и только - за изменения соотв. relations отвечают вызывающие скрипты) */
    public static function removeFileById($id)
    {
        $table = self::getSQLTable();

        if (($id != -1) && ($id != null))
        {
            // get intenal filename
            $qr = mysql_fetch_assoc(mysql_query("SELECT id, internal_name FROM {$table} WHERE id = {$id}"));
            $internal_name = $qr['internal_name'];

            // remove fileinfo (and content) from DB
            $query = "DELETE FROM {$table} WHERE id={$id}";
            $ret = mysql_query($query) or die("Death on: ".$query);

            // remove file from disk (пусть лучше пытается удалить файл всегда)
            // if (parent::$config['save_to_disk']) { }
            $fn = self::getRealFileName($internal_name);
            if (file_exists($fn)) {
                unlink($fn);
            }
        } else {
            $ret = -1;
        }
        return $ret;
    }


    /* удаляет файл из хранилища по заданному relation.
     * Используется в authors.action.remove - возможно избыточна */
    public static function removeFileByRel($rel, $collection)
    {
        $table = self::getSQLTable();
        if ($rel != -1)
        {
            // get intenal filename
            $q = "SELECT id, internal_name FROM {$table} WHERE relation={$rel} AND collection = '{$collection}' ";
            $qr = mysql_query($q) or Die("Death on: {$q}");
            $qf = mysql_fetch_assoc($qr);
            $internal_name = $qf['internal_name'];
            $id = $qf['id'];

            // remove fileinfo (and content) from DB
            $query = "DELETE FROM {$table} WHERE id={$id}";
            $ret = mysql_query($query) or die("Death on: ".$query);

            // remove file from disk
            $fn = self::getRealFileName($internal_name);
            if (file_exists($fn)) {
                unlink($fn);
            }

            return $ret;
        } else {
            return -1;
        }
    }

} // class

?>
<?php
require_once('core.php');

class FileStorage {
    private static $storage_table   = 'filestorage';
    private static $storage_path    = '/files/storage/';

    /* возвращает blob-строку пустого PDF-файла */
    public static function getEmptyPDF()
    {
        $data = "JVBERi0xLjQNCjEgMCBvYmoNCjw8IC9UeXBlIC9DYXRhbG9nIC9PdXRsaW5lcyAyIDAgUiAvUGFnZXMgMyAwIFIgPj4NCmVuZG9iag0KMiAwIG9iag0KPDwgL1R5cGUgT3V0bGluZXMgL0NvdW50IDAgPj4NCmVuZG9iag0KMyAwIG9iag0KPDwgL1R5cGUgL1BhZ2VzIC9LaWRzIFs0IDAgUl0gL0NvdW50IDEgPj4NCmVuZG9iag0KNCAwIG9iag0KPDwgL1R5cGUgL1BhZ2UgL1BhcmVudCAzIDAgUiAvTWVkaWFCb3ggWzAgMCA2MTIgNzkyXSAvQ29udGVudHMgNSAwIFIgL1Jlc291cmNlcyA8PCAvUHJvY1NldCA2IDAgUiA+PiA+Pg0KZW5kb2JqDQo1IDAgb2JqDQo8PCAvTGVuZ3RoIDM1ID4+DQpzdHJlYW0NCoUgUGFnZS1tYXJraW5nIG9wZXJhdG9ycyCFDQplbmRzdHJlYW0gDQplbmRvYmoNCjYgMCBvYmoNClsvUERGXQ0KZW5kb2JqDQp4cmVmDQowIDcNCjAwMDAwMDAwMDAgNjU1MzUgZiANCjAwMDAwMDAwMDkgMDAwMDAgbiANCjAwMDAwMDAwNzQgMDAwMDAgbiANCjAwMDAwMDAxMTkgMDAwMDAgbiANCjAwMDAwMDAxNzYgMDAwMDAgbiANCjAwMDAwMDAyOTUgMDAwMDAgbiANCjAwMDAwMDAzNzYgMDAwMDAgbiANCnRyYWlsZXIgDQo8PCAvU2l6ZSA3IC9Sb290IDEgMCBSID4+DQpzdGFydHhyZWYNCjM5NA0KJSVFT0Y=";
        return base64_decode($data);
    }

    /* возвращает blob-строку картинки-плашки "нет изображения" */
    public static function getEmptyIMG()
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

    /*
     * удаляет из хранилища (таблицы) файл по соответствующему ID
     * (и только - за изменения соотв. relations отвечают вызывающие скрипты)
    */
    public static function removeFileById($id)
    {
        $table = self::$storage_table;
        if ($id != -1)
        {
            $query = "DELETE FROM {$table} WHERE id=$id";
            return mysql_query($query) or die("Death on: ".$query);
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
        $table = self::$storage_table;
        $q = "SELECT relation FROM {$table} WHERE id = $id";

        $r = @mysql_query($q) or die($q);

        if ($r) {
            $record = mysql_fetch_assoc($r);
            return $record['relation'];
        } else {
            return -1;
        }
    }

    /*
     * удаляет файл из хранилища по заданному relation.
     * Используется в authors.action.remove
    */
    public static function removeFileByRel($rel, $collection)
    {
        $table = self::$storage_table;
        if ($rel != -1)
        {
            $query = "DELETE FROM {$table} WHERE relation={$rel} AND collection = '{$collection}'";
            return mysql_query($query) or die("Death on: ".$query);
        } else {
            return -1;
        }
    }

    /* @property integer $rel
     * @returns filestorage[rel]-> collection
    */
    public static function getCollectionByRel($rel)
    {
        $table = self::$storage_table;
        $q = "SELECT collection FROM {$table} WHERE relation = {$rel}";

        $r = @mysql_query($q) or die($q);

        if ($r) {
            $record = mysql_fetch_assoc($r);
            return $record['collection'];
        } else {
            return -1;
        }
    }

    /* возвращает контент файла по указанному идентификатору */
    public static function getFileContent($id)
    {
        $table = self::$storage_table;
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

    /* возвращает служебную информацию по файлу из хранилища
    */
    public static function getFileInfo($id)
    {
        $table = self::$storage_table;

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

    /* добавляет контент файла в соответствующее хранилище
    в настоящее время - в таблицу.
    */
    private function appendFileContent($filename, $fileid)
    {
        $insert_content_array = array(
            'content' => mysql_escape_string(floadfile($filename))
        );
        $qc = MakeUpdate($insert_content_array, self::$storage_table, " WHERE id = {$fileid}");
        return mysql_query($qc);
    }

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
        if ($file_array['tmp_name']!='')
        {
            $insert_data = array(
                'username' => $file_array['name'],
                'tempname' => ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? str_replace('\\','\\\\', ($file_array['tmp_name'])) : ($file_array['tmp_name']),
                'filesize' => $file_array['size'],
                'relation' => $related_id,
                'filetype' => $file_array['type'],
                'collection' => $collection
            );
            $q = MakeInsert($insert_data, self::$storage_table);
            mysql_query($q) or Die("Death on $q");
            $last_file_id = mysql_insert_id() or Die("Не удалось получить id последнего добавленного файла !");

            self::appendFileContent($insert_data['tempname'], $last_file_id);

            $q_api = MakeUpdate(array($related_field_in_table => $last_file_id), $collection, " WHERE id= $related_id ");
            mysql_query($q_api) or Die("Death on update {$collection} table with request: ".$q_api);
            $ret = $last_file_id;
        } else {
            $ret = null;
        }
        return $ret;
    }

}

?>
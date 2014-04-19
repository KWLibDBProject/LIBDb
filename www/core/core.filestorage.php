<?php
require_once('core.php');

class FileStorage {
    private static $storage_table = 'filestorage';
    private static $storage_path  = '/files/storage/';

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
    { }

    /* возвращает служебную информацию по файлу из хранилища
    */
    public static function getFileInfo($id)
    {
        $table = self::$storage_table;
        $ret = array();
        $qp = "SELECT id, username, filesize FROM {$table} WHERE id = $id";
        $rp = mysql_query($qp) or Die("Death on $qp");
        if ($rp) {
            if (@mysql_num_rows($rp) == 1) {
                $ret = mysql_fetch_assoc($rp);
            } else {
                $ret = null;
            }
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
            $q = MakeInsert($insert_data, 'filestorage');
            mysql_query($q) or Die("Death on $q");
            $last_file_id = mysql_insert_id() or Die("Не удалось получить id последнего добавленного файла !");

            self::appendFileContent($insert_data['tempname'], $last_file_id);

            $q_api = MakeUpdate(array($related_field_in_table => $last_file_id), $collection, " WHERE id= $related_id ");
            mysql_query($q_api) or Die("Death on update {$collection} table with request: ".$q_api);
        } else {}
    }

}

?>
<?php
require_once('core.php');
/*
 В filestorage желательно поле inhere (там строка - название таблицы, к которой относится файл)
 * */


class FileStorage {
    private static $filestorage = 'filestorage';

    public static function removeFile($id)
    {
        if ($id != -1)
        {
            mysql_query("DELETE FROM filestorage WHERE id=$id");
        }
    }

    public static function getFile($id)
    {

    }

    public static function addFile($file_array, $related_id, $related_table, $related_field_in_table)
    {
        if ($file_array['tmp_name']!='')
        {
            $insert_data = array(
                'username' => $file_array['name'],
                'tempname' => ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? str_replace('\\','\\\\', ($file_array['tmp_name'])) : ($file_array['tmp_name']),
                'filesize' => $file_array['size'],
                'relation' => $related_id,
                'filetype' => $file_array['type'],
                'collection' => $related_table
            );
            $insert_data['content'] = mysql_escape_string(floadfile($insert_data['tempname']));
            $q = MakeInsert($insert_data, 'filestorage');

            mysql_query($q) or Die("Death on $q");
            $last_file_id = mysql_insert_id() or Die("Не удалось получить id последнего добавленного файла !");

            $q_api = MakeUpdate(array($related_field_in_table => $last_file_id), $related_table, "WHERE id=$related_id");
            mysql_query($q_api) or Die("Death on update books table with request: ".$q_api);
        } else {}
    }

}

?>

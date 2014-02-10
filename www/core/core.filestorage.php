<?php
/*
 В filestorage желательно поле inhere (там строка - название таблицы, к которой относится файл)
 * */


class FileStorage {
    private static $filestorage = 'filestorage';

    public static function removeFile($id)
    {
        mysql_query("DELETE FROM filestorage WHERE id=$id");
    }

    public static function getFile($id)
    {

    }


}

?>

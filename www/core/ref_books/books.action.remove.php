<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.filestorage.php');
require_once('../core.kwt.php');

// удалить сборник, если в нем есть статьи НЕЛЬЗЯ

$table = 'books';

// возможно, переделать
if (!IsSet($_GET['id'])) {
    $result['error'] = 5; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
} else {
    $table = 'books';
    $link = ConnectDB();
    $id = $_GET["id"]; // айди удаляемой книжки

    $qt = "SELECT COUNT(`book`) as `bcount` FROM articles WHERE `book`=$id";

    if ($rt = mysql_query($qt)) {
        $bcount = mysql_fetch_assoc($rt);
        if ($bcount['bcount'] > 0) {
            // в книжке есть статьи, удалять нельзя
            $result["error"] = 4;
            $result['message'] = 'Нельзя удалять сборник (книгу), если в нем есть статьи!';
        } else {
            // статей нет, можно удалить
            /* вот тут нужно удалить 3 файла из таблицы STORAGE!!! */
            $book_files = mysql_fetch_assoc(mysql_query("SELECT file_cover, file_title, file_toc FROM books WHERE id=$id"));
            FileStorage::removeFile($book_files['file_cover']);
            FileStorage::removeFile($book_files['file_title']);
            FileStorage::removeFile($book_files['file_toc']);

            $q = "DELETE FROM $table WHERE (id=$id)";

            if ($r = mysql_query($q)) {
                // запрос удаление успешен
                $result["error"] = 0;
                $result['message'] = 'Сборник удален из базы данных.';

            } else {
                // DB error again
                $result["error"] = 1;
                $result['message'] = 'Ошибка удаления сборника из базы данных!';
            }
        }
    } else {
        // DB error
        $result["error"] = 2;
        $result['message'] = 'Ошибка доступа к базе данных!';
    };
    if (isAjaxCall()) {
        print(json_encode($result));
    } else {
        $override = array(
            'time' => 15,
            'target' => '/core/ref.books.show.php',
            'buttonmessage' => 'Вернуться к списку сборников',
            'message' => $result['message']
        );
        $tpl = new kwt('../ref.all.timed.callback.tpl');
        $tpl->override($override);
        $tpl->out();
    }
}
?>
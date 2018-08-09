<?php
require_once '../__required.php'; // $mysqli_link

// удалить сборник, если в нем есть статьи НЕЛЬЗЯ

// @todo: проверка прав на удаление (в сессии ли мы и кто мы?)

$table = 'books';

// возможно, переделать
if (!IsSet($_GET['id'])) {
    $result['error'] = 5; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
} else {
    $table = 'books';
    $id = intval($_GET["id"]); // айди удаляемой книжки

    $qt = "SELECT COUNT(book) as bcount FROM articles WHERE book=$id";

    if ($rt = mysqli_query($mysqli_link, $qt)) {
        $bcount = mysqli_fetch_assoc($rt);
        if ($bcount['bcount'] > 0) {
            // в книжке есть статьи, удалять нельзя
            $result["error"] = 4;
            $result['message'] = 'Нельзя удалять сборник (книгу), если в нем есть статьи!';
        } else {
            // статей нет, можно удалить
            /* вот тут нужно удалить 5 файлов из таблицы STORAGE!!! */
            $book_files = mysqli_fetch_assoc(mysqli_query($mysqli_link, "SELECT file_cover, file_title_ru, file_title_en, file_toc_ru, file_toc_en FROM books WHERE id={$id}"));
            FileStorage::removeFileById($book_files['file_cover']);
            FileStorage::removeFileById($book_files['file_title_ru']);
            FileStorage::removeFileById($book_files['file_title_en']);
            FileStorage::removeFileById($book_files['file_toc_ru']);
            FileStorage::removeFileById($book_files['file_toc_en']);

            $q = "DELETE FROM $table WHERE (id = {$id})";

            if ($r = mysqli_query($mysqli_link, $q)) {
                // запрос удаление успешен
                $result["error"] = 0;
                $result['message'] = 'Сборник удален из базы данных.';

                kwLogger::logEvent('Delete', 'books', $id, "Book removed from DB, id was {$id}");

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

        $template_dir = '$/core/_templates';
        $template_file = "ref.all_timed_callback.html";

        $template_data = array(
            'time'          => Config::get('callback_timeout') ?? 15,
            'target'        => '../list.books.show.php',
            'button_text'   => 'Вернуться к списку сборников',
            'message'       => $result['message']
        );
        echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);
    }
}

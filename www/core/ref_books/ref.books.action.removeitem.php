<?php
// удалить сборник, если в нем есть статьи НЕЛЬЗЯ

$table = 'books';

// возможно, переделать
if (!IsSet($_GET['ref_name'])) {
    $result['error'] = 5; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
} else {
    $table = $_GET['ref_name'];
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

            $q = "UPDATE $table SET deleted=1 WHERE (id=$id)";
            if ($r = mysql_query($q)) {
                // запрос удаление успешен
                $result["error"] = 0;
                $result['message'] = 'Сборник удален из базы данных.';

            } else {
                // DB error again
                $result["error"] = 1;
                $result['message'] = 'Ошибка удаления сбоника из базы данных!';
            }
        }
    } else {
        // DB error
        $result["error"] = 2;
        $result['message'] = 'Ошибка доступа к базе данных!';
    };
    print(json_encode($result));
    CloseDB($link);
}
?>
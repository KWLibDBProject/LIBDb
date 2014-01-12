<?php
// удалить рубрику (топик), если в ней есть статьи НЕЛЬЗЯ

if (!IsSet($_GET['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
} else {
    $table = $_GET['ref_name'];
    $id = $_GET["id"];
    $link = ConnectDB();

    $qt = "SELECT COUNT(`topic`) as `tcount` FROM articles WHERE `topic`=$id";

    if ($rt = mysql_query($qt)) {
        $tcount = mysql_fetch_assoc($rt);
        if ($tcount['tcount'] > 0) {
            // в книжке есть статьи, удалять нельзя
            $result["error"] = 4;
            $result['message'] = 'Нельзя удалить тематический раздел, если в нем есть статьи!';
        } else {
            // статей нет, можно удалить

            $q = "UPDATE $table SET deleted=1 WHERE (id=$id)";
            if ($r = mysql_query($q)) {
                // запрос удаление успешен
                $result["error"] = 0;
                $result['message'] = 'Тематический раздел удален из базы данных!';

            } else {
                // DB error again
                $result["error"] = 1;
                $result['message'] = 'Ошибка удаления тематического раздела из базы данных!';
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
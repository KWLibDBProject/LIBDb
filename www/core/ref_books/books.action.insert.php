<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$ref_name = 'books';
$ref_filestorage = 'filestorage';


$link = ConnectDB();

$q = array(
    'title' => mysql_escape_string($_POST['book_title']),
    'date' => mysql_escape_string($_POST['book_date']),
    'contentpages' => mysql_escape_string($_POST['book_contentpages']),
    'published' => mysql_escape_string($_POST['is_book_ready']),
);
$q['year'] = substr($q['date'],6,4);

$qstr = MakeInsert($q, $ref_name);
$res = mysql_query($qstr, $link) or Die("Невозможно вставить данные в базу  ".$qstr);
$new_id = mysql_insert_id() or Die("Не удалось получить id последней добавленной записи!");


if (isset($_FILES)) {
    // Если массив $_FILES не пуст - это означает, что файлы присоединили.
    // И, что самое главное, в EDIT - их "разлинковывали" и добавляли новые! См логику как в article.form.edit

    $book_file_rels = array();

    foreach ($_FILES as $a_file => $a_file_data) {
        $insert_array = array(
            'username' => $a_file_data['name'],
            'tempname' => ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? str_replace('\\','\\\\', $a_file_data['tmp_name']) : $a_file_data['tmp_name'],
            'filesize' => $a_file_data['size'],
            'relation' => $new_id,
            'filetype' => $a_file_data['type'],
            'collection' => 'books'
        );
        $insert_array['content'] = mysql_escape_string(floadfile($insert_array['tempname']));
        $q = MakeInsert($insert_array, $ref_filestorage);

        mysql_query($q, $link) or Die("Death on $q");
        $book_file_rels[$a_file] = mysql_insert_id() or Die("Не удалось получить id последнего добавленного файла !");

    }
    $q_rels = MakeUpdate($book_file_rels, 'books', "WHERE id=$new_id ");
    mysql_query($q_rels, $link) or Die("Death on update books table with request: ".$q_rels);

    $result['error'] = 0;
    $result['message'] .= "Данные обновлены!";

} else {
    $result['error'] = 1;
    $result['message'] .= "Не выбраны файлы для загрузки или ошибка передачи данных! <br>\r\n";
}


CloseDB($link);

if (isAjaxCall()) {
    print(json_encode($result));
} else {
    if ($result['error'] == 0) {
        $override = array(
            'time' => 10,
            'target' => '/core/ref.books.show.php',
            'buttonmessage' => 'Вернуться к списку сборников',
            'message' => 'Сборник добавлен'
        );
    } else {
        $override = array(
            'time' => 10,
            'target' => '/core/ref.books.show.php',
            'buttonmessage' => 'Вернуться к списку сборников',
            'message' => $result['message']
        );
    }
    $tpl = new kwt('../ref.all.timed.callback.tpl');
    $tpl->override($override);
    $tpl->out();
}

?>
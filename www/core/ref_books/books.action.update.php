<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$ref_name = 'books';
$ref_filestorage = 'filestorage';

$book_id = $_POST['book_id'];

$link = ConnectDB();

$q = array(
    'title'         => mysql_escape_string($_POST['book_title']),
    'date'          => mysql_escape_string($_POST['book_date']),
    'contentpages'  => mysql_escape_string($_POST['book_contentpages']),
    'published'     => mysql_escape_string($_POST['is_book_ready']),
    'year'          => substr(mysql_escape_string($_POST['book_date']), 6, 4),
    'timestamp'     => ConvertDateToTimestamp(mysql_escape_string($_POST['book_date']))
);

$qstr = MakeUpdate($q, $ref_name, " WHERE id = $book_id");
$res = mysql_query($qstr, $link) or Die("Невозможно обновить данные в базе  ".$qstr);

if (count($_FILES)>0) {
    // Если массив $_FILES не пуст - это означает, что файлы присоединили.
    // И, что самое главное, в EDIT - их "разлинковывали" и добавляли новые!

    $book_file_rels = array();

    // неважно сколько файлов пришло из формы - обработаем массив с ними в цикле
    foreach ($_FILES as $a_file => $a_file_data) {
        $insert_array = array(
            'username' => $a_file_data['name'],
            'tempname' => ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? str_replace('\\','\\\\', $a_file_data['tmp_name']) : $a_file_data['tmp_name'],
            'filesize' => $a_file_data['size'],
            'relation' => $book_id,
            'filetype' => $a_file_data['type'],
            'collection' => 'books'
        );
        $q = MakeInsert($insert_array, $ref_filestorage);
        mysql_query($q, $link) or Die("Death on $q");

        $inserted_file_id = mysql_insert_id() or Die("Не удалось получить id последнего добавленного файла !");

        /* Разделяем вставку контента и информации о файле в базу */
        $insert_content_array = array(
            'content' => mysql_escape_string(floadfile($insert_array['tempname']))
        );
        $qc = MakeUpdate($insert_content_array, $ref_filestorage, " WHERE id = {$inserted_file_id}");
        mysql_query($qc, $link);
        /* Конец разделения. Это надо оформлять функцией из filestorage.lib.php
        и переписывать на хранение в каталоге
         */


        $book_file_rels[$a_file] = $inserted_file_id;
        // a_file - одно из значений из перечисления идентификаторов файлов из инпута
        // к примеру $book_file_rels['file_toc'] = id этого файла, только что вставленного в БД
    }
    $q_rels = MakeUpdate($book_file_rels, 'books', " WHERE id=$book_id ");

    mysql_query($q_rels, $link) or Die("Death on update books table with request: ".$q_rels);

    $result['error'] = 0;
    $result['message'] .= "Данные обновлены!";

} else {
    $result['error'] = 1;
    // файлы не менялись, хотя прочие данные обновлены
    $result['message'] .= "Данные обновлены!";
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
            'message' => 'Сборник обновлен'
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
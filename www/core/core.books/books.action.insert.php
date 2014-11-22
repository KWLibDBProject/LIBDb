<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.filestorage.php');

$ref_name = 'books';

$link = ConnectDB();

$q = array(
    'title'         => mysql_escape_string($_POST['book_title']),
    'date'          => mysql_escape_string($_POST['book_date']),
    'contentpages'  => mysql_escape_string($_POST['book_contentpages']),
    'published'     => mysql_escape_string($_POST['is_book_ready']),
    'year'          => substr(mysql_escape_string($_POST['book_date']), 6, 4),
    'timestamp'     => ConvertDateToTimestamp(mysql_escape_string($_POST['book_date'])),
    'stat_date_insert' => ConvertTimestampToDate()
);

$qstr = MakeInsert($q, $ref_name);
$res = mysql_query($qstr, $link) or Die("Невозможно вставить данные в базу  ".$qstr);
$book_id = mysql_insert_id() or Die("Не удалось получить id последней добавленной записи!");


if (count($_FILES)>0) {
    // Если массив $_FILES не пуст - это означает, что файлы присоединили.
    // И, что самое главное, в EDIT - их "разлинковывали" и добавляли новые! См логику как в article.form.edit
    // неважно сколько файлов пришло из формы - обработаем массив с ними в цикле
    foreach ($_FILES as $a_file => $a_data) {
        FileStorage::addFile($a_data, $book_id, 'books', $a_file);
    }

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
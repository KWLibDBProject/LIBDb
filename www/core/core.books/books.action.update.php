<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.filestorage.php');

$ref_name = 'books';

$book_id = $_POST['book_id'];

$link = ConnectDB();

$q = array(
    'title'         => mysql_real_escape_string($_POST['book_title']),
    'date'          => mysql_real_escape_string($_POST['book_date']),
    'contentpages'  => mysql_real_escape_string($_POST['book_contentpages']),
    'published'     => mysql_real_escape_string($_POST['is_book_ready']),
    'year'          => substr(mysql_real_escape_string($_POST['book_date']), 6, 4),
    'timestamp'     => ConvertDateToTimestamp(mysql_real_escape_string($_POST['book_date'])),
    'stat_date_update' => ConvertTimestampToDate()
);

$qstr = MakeUpdate($q, $ref_name, " WHERE id = $book_id");
$res = mysql_query($qstr, $link) or Die("Невозможно обновить данные в базе  ".$qstr);

if (count($_FILES)>0) {
    // Если массив $_FILES не пуст - это означает, что файлы присоединили.
    // И, что самое главное, в EDIT - их "разлинковывали" и добавляли новые!
    // неважно сколько файлов пришло из формы - обработаем массив с ними в цикле

    foreach ($_FILES as $a_file => $a_data) {
        FileStorage::addFile($a_data, $book_id, 'books', $a_file);
    }
    $result['error'] = 0;
    $result['message'] .= "Данные обновлены, новые файлы в базу добавлены!";
} else {
    $result['error'] = 1;
    // файлы не менялись, хотя прочие данные обновлены
    $result['message'] .= "Данные обновлены!";
}

kwLogger::logEvent('Update', 'books', $book_id, "Updated book, id = {$book_id}");


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
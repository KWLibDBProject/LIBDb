<?php
require_once '../__required.php'; // $mysqli_link

$ref_name = 'books';

$now = ConvertTimestampToDate();
$q = array(
    'title'         => mysqli_real_escape_string($mysqli_link, $_POST['book_title']),
    'date'          => mysqli_real_escape_string($mysqli_link, $_POST['book_date']),
    'contentpages'  => mysqli_real_escape_string($mysqli_link, $_POST['book_contentpages']),
    'published'     => mysqli_real_escape_string($mysqli_link, $_POST['is_book_ready']),
    'year'          => substr(mysqli_real_escape_string($mysqli_link, $_POST['book_date']), 6, 4),
    'timestamp'     => ConvertDateToTimestamp(mysqli_real_escape_string($mysqli_link, $_POST['book_date'])),
    'stat_date_insert'  =>  $now,
    'stat_date_update'  =>  $now
);

$qstr = MakeInsert($q, $ref_name);
$res = mysqli_query($mysqli_link, $qstr) or die("Невозможно вставить данные в базу  ".$qstr);
$book_id = mysqli_insert_id($mysqli_link) or die("Не удалось получить id последней добавленной записи!");


if (count($_FILES)>0) {
    // Если массив $_FILES не пуст - это означает, что файлы присоединили.
    // И, что самое главное, в EDIT - их "разлинковывали" и добавляли новые! См логику как в article.form.edit
    // неважно сколько файлов пришло из формы - обработаем массив с ними в цикле
    foreach ($_FILES as $a_file => $a_data) {
        FileStorage::addFile($a_data, $book_id, 'books', $a_file);
    }

    $result['error'] = 0;
    $result['message'] = "Данные обновлены!";

 } else {
    $result['error'] = 1;
    $result['message'] = "Не выбраны файлы для загрузки или ошибка передачи данных! <br>\r\n";
}

kwLogger::logEvent('Add', 'books', $book_id, "Added book, new id = {$book_id}");

if (isAjaxCall()) {
    print(json_encode($result));
} else {
    if ($result['error'] == 0) {
        $override = array(
            'time' => $CONFIG['callback_timeout'] ?? 15,
            'target' => '/core/ref.books.show.php',
            'buttonmessage' => 'Вернуться к списку сборников',
            'message' => 'Сборник добавлен'
        );
    } else {
        $override = array(
            'time' => $CONFIG['callback_timeout'] ?? 15,
            'target' => '/core/ref.books.show.php',
            'buttonmessage' => 'Вернуться к списку сборников',
            'message' => $result['message']
        );
    }
    $tpl = new kwt('../ref.all.timed.callback.tpl');
    $tpl->override($override);
    $tpl->out();
}
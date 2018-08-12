<?php
require_once '../__required.php'; // $mysqli_link

$ref_name = 'books';

// $now = ConvertTimestampToDate(); // удалить
$q = array(
    'title'         => mysqli_real_escape_string($mysqli_link, $_POST['book_title']),
    'contentpages'  => mysqli_real_escape_string($mysqli_link, $_POST['book_contentpages']),
    'published_status'  => mysqli_real_escape_string($mysqli_link, $_POST['is_book_ready']),
    'published_date'    => DateTime::createFromFormat('d.m.Y', $_POST['book_publish_date'])->format('Y-m-d'),

    //@todo: убрать
    // 'date'          => mysqli_real_escape_string($mysqli_link, $_POST['book_date']), // must be mysql date format
    // 'year'          => substr(mysqli_real_escape_string($mysqli_link, $_POST['book_date']), 6, 4), // не нужно
    // 'timestamp'     => ConvertDateToTimestamp(mysqli_real_escape_string($mysqli_link, $_POST['book_date'])), // зачем???

    //@todo: убрать
    // 'stat_date_insert'  =>  $now, // БД
    // 'stat_date_update'  =>  $now // БД
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
    $template_dir = '$/core/_templates';
    $template_file = "ref.all_timed_callback.html";

    $template_data = array(
        'time'          => Config::get('callback_timeout') ?? 15,
        'target'        => '../list.books.show.php',
        'button_text'   => 'Вернуться к списку сборников',
    );

    $template_data['message'] = ($result['error'] == 0) ? 'Сборник добавлен' : $result['message'];


    echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);
}
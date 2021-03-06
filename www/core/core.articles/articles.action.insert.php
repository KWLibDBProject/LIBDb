<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

$result['message'] = '';
$result['error'] = 0;

if (!IsSet($_POST['caller'])) {
    $result['error'] = 1; $result['message'] .= 'Unknown caller!'; print(json_encode($result)); exit();
}

$dataset = array(
    'udc'           => str_replace(" ", "", mysqli_real_escape_string($mysqli_link, $_POST['udc'])),
    'title_en'      => mysqli_real_escape_string($mysqli_link, $_POST['title_en']),
    'title_ru'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ru']),
    'title_ua'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ua']),
    'abstract_en'   => mysqli_real_escape_string($mysqli_link, $_POST['abstract_en']),
    'abstract_ru'   => mysqli_real_escape_string($mysqli_link, $_POST['abstract_ru']),
    'abstract_ua'   => mysqli_real_escape_string($mysqli_link, $_POST['abstract_ua']),
    'keywords_en'   => mysqli_real_escape_string($mysqli_link, $_POST['keywords_en']),
    'keywords_ru'   => mysqli_real_escape_string($mysqli_link, $_POST['keywords_ru']),
    'keywords_ua'   => mysqli_real_escape_string($mysqli_link, $_POST['keywords_ua']),
    'refs_ru'       => mysqli_real_escape_string($mysqli_link, $_POST['refs_ru']),
    'refs_en'       => mysqli_real_escape_string($mysqli_link, $_POST['refs_en']),
    'refs_ua'       => mysqli_real_escape_string($mysqli_link, $_POST['refs_ru']),
    'book'          => mysqli_real_escape_string($mysqli_link, $_POST['book']),
    'topic'         => mysqli_real_escape_string($mysqli_link, $_POST['topic']),
    'pages'         => mysqli_real_escape_string($mysqli_link, $_POST['pages']),
    'doi'           => mysqli_real_escape_string($mysqli_link, $_POST['doi']),

    'date_add'      => DateTime::createFromFormat('d.m.Y', $_POST['date_add'])->format('Y-m-d'),
);


// теперь нам нужно вставить данные в БАЗУ
$query = MakeInsert($dataset,'articles');
$res = mysqli_query($mysqli_link, $query) or Die("Невозможно вставить данные в базу  ".$query);
$article_id = mysqli_insert_id($mysqli_link) or Die("Не удалось получить id последней добавленной записи!");

if (IsSet($_FILES)) {

    /* @todo: вставить эту проверку в остальные случаи загрузки данных через $_FILES ! */
    switch ($_FILES['pdffile']['error']) {
        case UPLOAD_ERR_INI_SIZE: {
            $result['error_message'] = " Ошибка: Размер загружаемого файла больше ".ini_get('upload_max_filesize')." байт!";
            break;
        }
        case UPLOAD_ERR_FORM_SIZE : {
            $result['error_message'] = " Ошибка: Размер загружаемого файла больше ".$_POST['MAX_FILE_SIZE']." байт!";
            break;
        }
        case UPLOAD_ERR_OK: {
            FileStorage::addFile($_FILES['pdffile'], $article_id, 'articles', 'pdfid');
            break;
        }
    }
} else {
    $result['error'] = 1;
    $result['message'] = "Не выбран файл для загрузки или ошибка передачи данных! <br>\r\n";
}

// потом обновить кросс-таблицу
// в едите нужно удалить старые значения, потом добавить новые
if (IsSet($_POST['authors'])) {
    $authors = $_POST['authors'];
    foreach ($authors as $n => $author) {
        $qa = "INSERT INTO cross_aa (author,article) VALUES ($author, $article_id)";
        mysqli_query($mysqli_link, $qa) or Die('error at '.$qa);
    }
} else {
    $result['error'] = 1;
    $result['message'] = "Не указаны авторы!<br>\r\n";
}

kwLogger::logEvent('Add', 'articles', $article_id, "Article added, new id is {$article_id}" );

$template_dir = '$/core/_templates';
$template_file = "ref.all_timed_callback.html";

$template_data = array(
    'time'          => Config::get('callback_timeout') ?? 15,
    'target'        => '../list.articles.show.php',
    'button_text'   => 'Вернуться к списку статей',
);

$template_data['message']
    = ($result['error'] == 0)
    ? ('Статья добавлена... ' . ($result['error_message'] ?? ''))
    : $result['message'];

echo websun_parse_template_path($template_data, $template_file, $template_dir);


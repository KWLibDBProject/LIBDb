<?php
require_once '../__required.php'; // $mysqli_link

$result['message'] = '';
$result['error'] = 0;

$article_id = $_POST['article_id'];

$link = ConnectDB();

$q = array(
    'udc'               => str_replace(" ", "", mysqli_real_escape_string($mysqli_link, $_POST['udc'])),
    'title_en'          => trim(mysqli_real_escape_string($mysqli_link, $_POST['title_en'])),
    'title_ru'          => trim(mysqli_real_escape_string($mysqli_link, $_POST['title_ru'])),
    'title_uk'          => trim(mysqli_real_escape_string($mysqli_link, $_POST['title_uk'])),
    'abstract_en'       => mysqli_real_escape_string($mysqli_link, $_POST['abstract_en']),
    'abstract_ru'       => mysqli_real_escape_string($mysqli_link, $_POST['abstract_ru']),
    'abstract_uk'       => mysqli_real_escape_string($mysqli_link, $_POST['abstract_uk']),
    'keywords_en'       => mysqli_real_escape_string($mysqli_link, $_POST['keywords_en']),
    'keywords_ru'       => mysqli_real_escape_string($mysqli_link, $_POST['keywords_ru']),
    'keywords_uk'       => mysqli_real_escape_string($mysqli_link, $_POST['keywords_uk']),
    'refs_ru'           => mysqli_real_escape_string($mysqli_link, $_POST['refs_ru']),
    'refs_en'           => mysqli_real_escape_string($mysqli_link, $_POST['refs_en']),
    'refs_uk'           => mysqli_real_escape_string($mysqli_link, $_POST['refs_ru']),
    'book'              => mysqli_real_escape_string($mysqli_link, $_POST['book']),
    'add_date'          => mysqli_real_escape_string($mysqli_link, $_POST['add_date']),
    'topic'             => mysqli_real_escape_string($mysqli_link, $_POST['topic']),
    'pages'             => mysqli_real_escape_string($mysqli_link, $_POST['pages']),
    'doi'               => mysqli_real_escape_string($mysqli_link, $_POST['doi']),
    'stat_date_update'  => ConvertTimestampToDate()
);

// теперь нам нужно вставить данные в БАЗУ (пока что с учетом вставки файла в БЛОБ)
$qstr = MakeUpdate($q, 'articles', "where id = $article_id");
$res = mysqli_query($mysqli_link, $qstr) or Die("Невозможно вставить данные в базу  ".$qstr);

$is_newfile = $_POST['currfile_changed'];

if ($is_newfile == 1) {
    // пдфку обновляли
    if (isset($_FILES)) {

        /* @todo: вставить эту проверку в остальные случаи загрузки данных через $_FILES ! */
        switch ($_FILES['pdffile']['error']) {
            case UPLOAD_ERR_INI_SIZE: {
                $result['error_message'] = " Возникла ошибка: размер загружаемого файла больше ".ini_get('upload_max_filesize')." байт!";
                break;
            }
            case UPLOAD_ERR_FORM_SIZE : {
                $result['error_message'] = " Возникла ошибка: размер загружаемого файла больше ".$_POST['MAX_FILE_SIZE']." байт!";
                break;
            }
            case UPLOAD_ERR_OK: {
                FileStorage::addFile($_FILES['pdffile'], $article_id, 'articles', 'pdfid');
                break;
            }
        }

    } else {
        $result['error'] = 1;
        $result['message'] .= "Не выбран файл для загрузки или ошибка передачи данных! <br>\r\n";
    }
}

// потом обновить кросс-таблицу
// в едите нужно удалить старые значения, потом добавить новые
if (IsSet($_POST['authors'])) {
    // удаляем старые соответствия
    $q_del = "DELETE FROM cross_aa WHERE article=$article_id";
    mysqli_query($mysqli_link, $q_del);
    // добавляем новых
    $authors = $_POST['authors'];

    foreach ($authors as $n => $author) {
        $qa = "INSERT INTO cross_aa (author, article) VALUES ($author, $article_id)";
        mysqli_query($mysqli_link, $qa) or Die('error at '.$qa);
    }

} else {
    $result['error'] = 1;
    $result['message'] .= "Не указаны авторы!<br>\r\n";
}

kwLogger::logEvent('Update', 'articles', $article_id, "Article updated, id is {$article_id}" );

CloseDB($link);

if ($result['error'] == 0) {
    $override = array(
        'time' => 10,
        'target' => '/core/ref.articles.show.php',
        'buttonmessage' => 'Вернуться к списку статей',
        'message' => 'Информация о статье в базе обновлена... '.$result['error_message']
    );
} else {
    $override = array(
        'time' => 10,
        'target' => '/core/ref.articles.show.php',
        'buttonmessage' => 'Вернуться к списку статей',
        'message' => $result['message']
    );
}
$tpl = new kwt('../ref.all.timed.callback.tpl');
$tpl->override($override);
$tpl->out();

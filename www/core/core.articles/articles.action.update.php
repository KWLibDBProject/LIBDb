<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.filestorage.php');

$result['message'] = '';
$result['error'] = 0;

$article_id = $_POST['article_id'];

$link = ConnectDB();

$q = array(
    'udc' => str_replace(" ","",mysql_escape_string($_POST['udc'])),
    'title_en' => mysql_escape_string($_POST['title_en']),
    'title_ru' => mysql_escape_string($_POST['title_ru']),
    'title_uk' => mysql_escape_string($_POST['title_uk']),
    'abstract_en' => mysql_escape_string($_POST['abstract_en']),
    'abstract_ru' => mysql_escape_string($_POST['abstract_ru']),
    'abstract_uk' => mysql_escape_string($_POST['abstract_uk']),
    'keywords_en' => mysql_escape_string($_POST['keywords_en']),
    'keywords_ru' => mysql_escape_string($_POST['keywords_ru']),
    'keywords_uk' => mysql_escape_string($_POST['keywords_uk']),
    'refs_ru' => mysql_escape_string($_POST['refs_ru']),
    'refs_en' => mysql_escape_string($_POST['refs_en']),
    'refs_uk' => mysql_escape_string($_POST['refs_ru']),
    'book' => mysql_escape_string($_POST['book']),
    'add_date' => mysql_escape_string($_POST['add_date']),
    'topic' => mysql_escape_string($_POST['topic']),
    'pages' => mysql_escape_string($_POST['pages'])
);

// теперь нам нужно вставить данные в БАЗУ (пока что с учетом вставки файла в БЛОБ)
$qstr = MakeUpdate($q,'articles',"where id=$article_id");
$res = mysql_query($qstr, $link) or Die("Невозможно вставить данные в базу  ".$qstr);

$is_newfile = $_POST['currfile_changed'];

if ($is_newfile == 1) {
    // пдфку обновляли
    if (IsSet($_FILES)) {

        /* @todo: вставить эту проверку в остальные случаи загрузки данных через $_FILES ! */
        switch ($_FILES['pdffile']['error']) {
            case UPLOAD_ERR_INI_SIZE: {
                $result['error_message'] = " Однако возникла ошибка. Размер загружаемого файла больше ".ini_get('upload_max_filesize')." байт!";
                break;
            }
            case UPLOAD_ERR_FORM_SIZE : {
                $result['error_message'] = " Однако возникла ошибка. Размер загружаемого файла больше ".$_POST['MAX_FILE_SIZE']." байт!";
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
    mysql_query($q_del);
    // добавляем новых
    $authors = $_POST['authors'];

    foreach ($authors as $n => $author) {
        $qa = "INSERT INTO cross_aa (author, article) VALUES ($author, $article_id)";
        mysql_query($qa , $link) or Die('error at '.$qa);
    }

} else {
    $result['error'] = 1;
    $result['message'] .= "Не указаны авторы!<br>\r\n";
}

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

?>
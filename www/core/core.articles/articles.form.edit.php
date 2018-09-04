<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

if (isset($_GET['id']))
{
    $id = intval($_GET['id']);
} else {
    Redirect('/core/list.articles.show.php');
}

// Технически, ответ уже будет содержать date_add, но этой конструкцией мы добавляем еще одно поле `date_add` в ответ.
// В результате обычного запроса из консоли (к примеру) в ответе будет два столбца с date_add
// первый - во внутреннем формате DATE (то есть 2018-08-23), а второй в отформатированном
//
// ДАЛЕЕ, когда мы скажем $the_article = mysqli_fetch_assoc($res_article)
//                                  - в date_add запишется сначала первое значение, а потом второе

$query = "SELECT *, DATE_FORMAT(date_add, '%d.%m.%Y') as date_add FROM articles WHERE id= {$id}";

$res_article = mysqli_query($mysqli_link, $query) or die("Невозможно получить содержимое статьи! ".$query);

$numarticles = mysqli_num_rows($res_article);

if ($numarticles == 1)
{
    $the_article = mysqli_fetch_assoc($res_article);

    // получаем авторов
    $query = "select * from cross_aa where article={$id}";
    $res_authors = mysqli_query($mysqli_link, $query) or die("Невозможно получить кросс-таблицу автор X статья! ".$query);

    $numauthors = @mysqli_num_rows($res_authors);
    $the_loadedAuthorsNum = $numauthors;

    $currAuthList = "{ ";

    if ($numauthors > 0)
    {
        for ($i=1;$i<=$numauthors;$i++)
        {
            $tmp = mysqli_fetch_assoc($res_authors);
            $currAuthList .= " $i : $tmp[author] ,";
        }
    }

    $the_currAuthList = substr($currAuthList,0,-1);
    $the_currAuthList.= '}'; // значение для currAuthorsList

    $the_mode = 'edit';
    $the_currentBook = $the_article['book'];
    $the_currentTopic = $the_article['topic'];

    // получаем ПДФ-файл
    $the_file = FileStorage::getFileInfo($the_article['pdfid']);
} else {
    $the_currAuthList = '{ }';
    $the_loadedAuthorsNum = 0;
    $the_currentBook = -1;
    $the_currentTopic = -1;

    die('<strong style="color:red; font-size: x-large">СТАТЬЯ С УКАЗАННЫМ ИДЕНТИФИКАТОРОМ В БАЗЕ НЕ ОБНАРУЖЕНА!!!</strong>');
}

?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Редактирование статьи</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="../_assets/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="../_assets/jquery-ui-1.10.3.custom.min.js"></script>
    <script type="text/javascript" src="../_assets/jquery.ui.datepicker.rus.js"></script>
    <script type="text/javascript" src="../_assets/tinymce/tinymce.min.js"></script>
    <script type="text/javascript" src="../_assets/tinymce.config.js"></script>

    <link rel="stylesheet" type="text/css" href="../_assets/core.admin.css">
    <link rel="stylesheet" type="text/css" href="articles.css">
    <link rel="stylesheet" type="text/css" href="../_assets/jquery-ui-1.10.3.custom.min.css">

    <script type="text/javascript" src="../../frontend.js"></script>
    <script type="text/javascript" src="../../frontend.options.js"></script>
    <script type="text/javascript" src="articles.js"></script>
    <script type="text/javascript">
        // Здесь, для авторов, legacyformat СТРОГО ОБЯЗАТЕЛЕН
        var authorsList = preloadOptionsList('../core.authors/authors.action.getoptionlist.php?legacyformat');

        var booksListExtended = preloadOptionsList('../core.books/books.action.getoptionlist.php');

        // nogroup - без группировки, id - с айди в []
        var topicsListExtended = preloadOptionsList('../core.topics/topics.action.getoptionlist.php?id');

        var mode = 'edit';
        // loaded values for 'EDIT' mode
        currAuthorsList = <?php echo $the_currAuthList; ?>;
        var loadedAuthorsNum = <?php echo $the_loadedAuthorsNum; ?>;
        var lastAuthorNumber = <?php echo $the_loadedAuthorsNum+1; ?>;
        var currentBook = <?php echo $the_currentBook; ?>;
        var currentTopic = <?php echo $the_currentTopic; ?>;

        // tinyMCE inits
        tinify(tiny_config['simple'], 'abstract_en');
        tinify(tiny_config['simple'], 'abstract_ru');
        tinify(tiny_config['simple'], 'abstract_ua');

        tinify(tiny_config['simple'], 'refs_en');
        tinify(tiny_config['simple'], 'refs_ru');

        $(document).ready(function () {
            // onload
            // load authors
            if (mode == 'edit') {
                for (i=1; i<=loadedAuthorsNum; i++)
                {
                    InsertAuthorSelector("#authors_list",i);
                    if (typeof currAuthorsList[i] != 'undefined') {
                        $("select[data-alselector="+i+"] option[value="+currAuthorsList[i]+"]").prop("selected",true);
                    }
                    lastAuthorNumber++;
                }
            } else if (mode == 'new') {

            }

            // load selectors
            BuildSelectorExtended('book', booksListExtended, "Выбрать...", currentBook);
            BuildSelectorExtended('topic', topicsListExtended, "Выбрать...", currentTopic);

            // WIDGETS
            $("#datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd.mm.yy',
                minDate: '01.01.2003',
                maxDate: '01.01.2025',
                showButtonPanel: true,
                showOn: "both",
                buttonImageOnly: true,
                buttonImage: "../_assets/images/calendar.gif"
            });
            $("#abstract_tabs").tabs();
            $("#keywords_tabs").tabs();

            // bindings
            // bind ADD AUTHOR button
            $(".al-add").on('click',function(){ InsertAuthorSelector("#authors_list",lastAuthorNumber); lastAuthorNumber++; });
            // bind remove 'X' button for each author
            $("#authors_list").on('click',".al-delete",function(){ $('li[data-li="'+$(this).attr('data-al')+'"]').remove(); });

            $(".button-exit").on('click',function(event){
                event.preventDefault();
                window.location.href = '/core/list.articles.show.php';
                return false;
            });

            $("#form_edit_article").submit(function(){
                var bValid = true;
                var test_authorsList = [];
                $.each( $(".an_authors") , function(id, data) {
                    test_authorsList.push($(data).val());
                });

                if (!($("#authors_list").find('li').size())) {
                    // проверка количества авторов
                    alert('Не указаны авторы!');
                    bValid = false;
                } else if (!isArrayUnique(test_authorsList)) {
                    // проверка уникальности авторов в статье
                    alert('Обнаружены неуникальные авторы! ');
                    bValid = false;
                } else if ($("#currfile_changed").val()==1 && !strpos($("input[name=pdffile]").val() , '.pdf')) {
                    // проверка PDF-файла
                    alert('Указан неправильный файл для загрузки');
                    bValid = false;
                } else if ( $("input[name='add_date']").val().length == 0 ) {
                    alert('Не указана дата!');
                    bValid = false;
                }
                return bValid;
            });

            $("#button-delete").on('click',function(){
                id = $(this).attr('name');
                window.location.href="articles.action.remove.php?id="+id;
            });

            // логика кнопок
            $("#currfile_show").on('click',function(){ // show current file
                window.location.href="../get.file.php?id="+$(this).attr('data-fileid');
            });

            $("#currfile_del").on('click',function(){
                var getting = $.get('../core.filestorage/filestorage.action.remove.php', {
                    id: $(this).attr('data-fileid'),
                    caller: 'articles',
                    subcaller: 'pdfid'
                });
                getting.done(function(data){
                    result = $.parseJSON(data);
                    if (result['error'] == 0) {
                        $("#newfile_input").removeProp("disabled");
                        $("#currfile_changed").attr("value","1");
                        $("#pdf-file-old").hide();
                        $("#pdf-file-new").show();
                    } else {
                        alert('Ошибка удаления файла!');
                    }
                });
            });

            bindScrollTopAction("#actor-scroll-top");

        });
    </script>
</head>

<body>
<form action="articles.action.update.php" method="post" enctype="multipart/form-data" id="form_edit_article">
    <input type="hidden" name="article_id" value="<?php echo $id; ?>">

    <fieldset>
        <legend>Выпускные данные:</legend>

        <label for="the_topic">Тематический раздел (рубрика): </label>
        <select name="topic" id="the_topic" required></select>

        <label for="udc">УДК:</label>
        <input type="text" name="udc" id="udc" class="text ui-widget-content ui-corner-all" value="<?php echo $the_article['udc']; ?>">
    </fieldset>

    <fieldset>
        <legend>Сборник</legend>

        <label for="pages">Статья опубликована на страницах
            <input type="text" id="pages" name="pages" value="<?php echo $the_article['pages']; ?>" required>
        </label>

        <label for="the_book">... сборника:
            <select name="book" id="the_book" required></select>
        </label>
        <br/>
        <label for="datepicker">Дата приема на публикацию:
            <input type="text" id="datepicker" name="date_add" value="<?php echo $the_article['date_add']; ?>" required>
        </label>
        <label for="doi">DOI:
            <input type="text" id="doi" name="doi" value="<?php echo $the_article['doi']; ?>" size="40">
        </label>
    </fieldset>

    <fieldset>
        <legend>PDF-file</legend>
        <input type="hidden" name="MAX_FILE_SIZE" value="10000000">

        <span id="pdf-file-old">
            <button type="button" id="currfile_show" data-fileid="<?php echo $the_file['id'];?>">Посмотреть</button>
            <label for="currfile_text">Текущий файл:</label>
            <input type="text" size="60" id="currfile_text" value="<?php echo $the_file['username']?>">
            <button type="button" id="currfile_del" data-fileid="<?php echo $the_file['id'];?>">Удалить</button>
        </span>

        <div id="pdf-file-new" class="hidden">
            <label for="newfile_input">Прикрепить НОВЫЙ PDF-файл:</label>
            <input type="file" name="pdffile" id="newfile_input" disabled>
            <input type="hidden" name="currfile_changed" id="currfile_changed" value="0">
        </div>
    </fieldset>

    <fieldset>
        <legend id="authors_legend">Авторы:</legend>
        <ul id="authors_list" class="authors_list_in_form"></ul>
        <input type="button" class="al-add" value="Добавить автора">
    </fieldset>

    <fieldset>
        <legend>Название статьи на разных языках</legend>
        <table>
            <tr>
                <td><label for="title_en">Article title</label></td>
                <td><input type="text" name="title_en" id="title_en" size="80" class="text ui-widget-content ui-corner-all" value="<?php echo $the_article['title_en'] ;?>"></td>
            </tr>
            <tr>
                <td><label for="title_ru">Название статьи:</label></td>
                <td><input type="text" name="title_ru" id="title_ru" size="80" class="text ui-widget-content ui-corner-all" value="<?php echo $the_article['title_ru'] ;?>"></td>
            </tr>
            <tr>
                <td><label for="title_ua">Назва статті:</label></td>
                <td><input type="text" name="title_ua" id="title_ua" size="80" class="text ui-widget-content ui-corner-all" value="<?php echo $the_article['title_ua'] ;?>"></td>
            </tr>
        </table>
    </fieldset>

    <fieldset class="hint" id="hint-main">
        <legend>Внимание!</legend>
        Пожалуйста, <strong>НЕ</strong> используйте избыточное форматирование при вводе аннотации, ключевых слов,
        списка литературы и прочего. Используйте только логическое выделение важных слов и понятий.
        Помните, что при выводе данных может возникнуть конфликт основных стилей сайта и ваших.
        <br/>
        <strong>Очищать форматирование </strong> <u>нужно</u> при помощи кнопки <span class="tinymce-icon-container"><i class="mce-ico mce-i-removeformat"></i></span> в редакторе (самая правая под меню).
        <br/>
        Если вы копируете переведенный блок текста из google-translate - <strong>обязательно</strong> очищайте форматирование.
        <br/>
        <strong>При вставке из ворда</strong> используйте кнопку <span class="tinymce-icon-container"><i class="mce-ico mce-i-pastetext"></i></span> (самая левая под меню).
    </fieldset>

    <fieldset>
        <legend>Аннотация на разных языках</legend>

        <div class="warning">
            Важно: аннотация будет <u>всегда</u> отображаться <em>курсивом</em> на сайте библиотеки.
            Выделять здесь текст курсивом <strong>не нужно. </strong>
        </div>

        <div id="abstract_tabs">
            <ul>
                <li><a href="#abstract-en">На английском</a></li>
                <li><a href="#abstract-ru">На русском</a></li>
                <li><a href="#abstract-ua">На украинском</a></li>
            </ul>
            <div id="abstract-en">
                <textarea id="abstract_en" name="abstract_en"><?php echo $the_article['abstract_en'] ;?></textarea>
            </div>
            <div id="abstract-ru">
                <textarea id="abstract_ru" name="abstract_ru"><?php echo $the_article['abstract_ru'] ;?></textarea>
            </div>
            <div id="abstract-ua">
                <textarea id="abstract_ua" name="abstract_ua"><?php echo $the_article['abstract_ua'] ;?></textarea>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend>Ключевые слова на разных языках</legend>
        <div id="keywords_tabs">
            <ul>
                <li><a href="#keywords-en">На английском</a></li>
                <li><a href="#keywords-ru">На русском</a></li>
                <li><a href="#keywords-ua">На украинском</a></li>
            </ul>
            <div id="keywords-en">
                <textarea id="keywords_en" name="keywords_en" cols="80" rows="6"><?php echo $the_article['keywords_en'] ;?></textarea>
            </div>
            <div id="keywords-ru">
                <textarea id="keywords_ru" name="keywords_ru" cols="80" rows="6"><?php echo $the_article['keywords_ru'] ;?></textarea>
            </div>
            <div id="keywords-ua">
                <textarea id="keywords_ua" name="keywords_ua" cols="80" rows="6"><?php echo $the_article['keywords_ua'] ;?></textarea>
            </div>
        </div>
    </fieldset>

    <fieldset class="hint" id="hint-references">
        <legend>Совет:</legend>
        Для списка литературы лучше использовать немаркированный (<span class="tinymce-icon-container"><i class="mce-ico mce-i-bullist"></i></span>)
        или маркированный (<span class="tinymce-icon-container"><i class="mce-ico mce-i-numlist"></i></span>) список.
    </fieldset>

    <fieldset>
        <legend>Источники: </legend>
        <label for="refs_ru"><strong>Список литературы:</strong></label><br>
        <textarea id="refs_ru" name="refs_ru" cols="80" rows="10"><?php echo $the_article['refs_ru'] ;?></textarea>

        <br>

        <label for="refs_en"><strong>References:</strong></label><br>
        <textarea id="refs_en" name="refs_en" cols="80" rows="10"><?php echo $the_article['refs_en'] ;?></textarea>
    </fieldset>
    <button type="submit" class="button-large" id="button-save"><strong>СОХРАНИТЬ ИЗМЕНЕНИЯ</strong></button>
    <button type="button" class="button-large" id="button-delete" name="<?php echo $id; ?>"><strong>УДАЛИТЬ СТАТЬЮ</strong></button>
    <button type="button" class="button-large button-exit"><strong>ОТМЕНИТЬ</strong></button>

    <button type="button" class="button-large float-right" id="actor-scroll-top"><strong>Наверх страницы</strong></button>
</form>

</body>

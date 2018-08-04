<?php
require_once '../__required.php'; // $mysqli_link

$SID = session_id();
if(empty($SID)) session_start();
ifNotLoggedRedirect('/core/');

?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Добавление новой статьи</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="../js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="../js/jquery-ui-1.10.3.custom.min.js"></script>
    <script type="text/javascript" src="../js/jquery.ui.datepicker.rus.js"></script>
    <script type="text/javascript" src="../js/tinymce/tinymce.min.js"></script>
    <script type="text/javascript" src="../js/tinymce.config.js"></script>

    <link rel="stylesheet" type="text/css" href="../css/core.admin.css">
    <link rel="stylesheet" type="text/css" href="articles.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui-1.10.3.custom.min.css">

    <script type="text/javascript" src="../js/core.js"></script>
    <script type="text/javascript" src="articles.js"></script>
    <script type="text/javascript">
        var authorsList = preloadOptionsList('../core.authors/ref.authors.action.getoptionlist.php');
        var booksList = preloadOptionsList('../core.books/ref.books.action.getoptionlist.php');
        var topicsList = preloadOptionsList('../core.topics/ref.topics.action.getoptionlist.php');

        var mode = 'new';

        // loaded values
        currAuthorsList = { }; // getCurrentAuthorsSelection, используется только для EDIT
        var loadedAuthorsNum = 0;
        var lastAuthorNumber = 1;
        var currentBook = 1;
        var currentTopic = 1;

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
                if (authorsList['error'] != 0) {
                    $('.al-add').prop('value','Добавьте авторов в базу!!!').attr('disabled','disabled');
                    $("#button-save").attr('disabled','disabled');
                }
            } // ничего не добавляем, у нас просто работает 1 кнопка "добавить"

            // load selectors
            BuildSelector('book',booksList,currentBook);
            BuildSelector('topic',topicsList,currentTopic);

            // WIDGETS
            $("#datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd.mm.yy',
                minDate: '01.01.2003',
                maxDate: '01.01.2020',
                showButtonPanel: true,
                showOn: "both",
                buttonImageOnly: true,
                buttonImage: "../css/images/calendar.gif"
            });
            $("#abstract_tabs").tabs({});
            $("#keywords_tabs").tabs();

            // bindings
            // bind ADD AUTHOR button
            $(".al-add").on('click',function(){ InsertAuthorSelector("#authors_list",lastAuthorNumber); lastAuthorNumber++; });
            // bind remove 'X' button for each author
            $("#authors_list").on('click',".al-delete",function(){ $('li[data-li="'+$(this).attr('data-al')+'"]').remove(); });


            $("#button-exit").on('click',function(event){
                event.preventDefault();
                window.location.href = '/core/list.articles.show.php';
                return false;
            });

            $("#form_new_article").submit(function(){
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
                } else if (!strpos($("input[name=pdffile]").val() , '.pdf')) {
                    // проверка PDF-файла
                    alert('Указан неправильный файл для загрузки');
                    bValid = false;
                } else if ( $("input[name='add_date']").val().length == 0 ) {
                    alert('Не указана дата!');
                    bValid = false;
                }
                return bValid;
            });

            /*$("#unique_test").on('click',function(){
                test_authorsList = [];
                $.each( $(".an_authors") , function(id, data) {
                    test_authorsList.push($(data).val());
                });
                alert(isArrayUnique(test_authorsList));
            } );*/

            bindScrollTopAction("#actor-scroll-top");

        });
    </script>
</head>

<body>
<form action="articles.action.insert.php" method="post" enctype="multipart/form-data" id="form_new_article">

    <fieldset>
        <legend>Выпускные данные:</legend>

        <label for="the_topic">Тематический раздел (рубрика): </label>
        <select name="topic" id="the_topic"></select>

        <label for="udc">УДК:</label>
        <input type="text" name="udc" id="udc" class="text ui-widget-content ui-corner-all">
    </fieldset>

    <fieldset>
        <legend>Сборник</legend>

        <label for="pages">Статья опубликована на страницах
            <input type="text" id="pages" name="pages" value="<?php echo $the_article['pages']; ?>">
        </label>

        <label for="the_book">... сборника:
            <select name="book" id="the_book"></select>
        </label>
        <br/>
        <label for="datepicker">Дата приема на публикацию:
            <input type="text" id="datepicker" name="date_add">
        </label>
        <label for="doi">DOI:
            <input type="text" id="doi" name="doi" size="40">
        </label>
    </fieldset>

    <fieldset>
        <legend>PDF-file</legend>
        <input type="hidden" name="MAX_FILE_SIZE" value="10000000">

        <label for="pdf">Прикрепить PDF-файл:</label>
        <input type="file" name="pdffile" id="pdf">
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
                <td><input type="text" name="title_en" id="title_en" size="80" class="text ui-widget-content ui-corner-all"></td>
            </tr>
            <tr>
                <td><label for="title_ru">Название статьи:</label></td>
                <td><input type="text" name="title_ru" id="title_ru" size="80" class="text ui-widget-content ui-corner-all"></td>
            </tr>
            <tr>
                <td><label for="title_ua">Назва статті:</label></td>
                <td><input type="text" name="title_ua" id="title_ua" size="80" class="text ui-widget-content ui-corner-all"></td>
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
                <textarea id="abstract_en" name="abstract_en"></textarea>
            </div>
            <div id="abstract-ru">
                <textarea id="abstract_ru" name="abstract_ru"></textarea>
            </div>
            <div id="abstract-ua">
                <textarea id="abstract_ua" name="abstract_ua"></textarea>
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
                <textarea id="keywords_en" name="keywords_en" cols="80" rows="6"></textarea>
            </div>
            <div id="keywords-ru">
                <textarea id="keywords_ru" name="keywords_ru" cols="80" rows="6"></textarea>
            </div>
            <div id="keywords-ua">
                <textarea id="keywords_ua" name="keywords_ua" cols="80" rows="6"></textarea>
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
        <textarea id="refs_ru" name="refs_ru" cols="80" rows="10"></textarea>

        <br>

        <label for="refs_en"><strong>References: </strong></label><br>
        <textarea id="refs_en" name="refs_en" cols="80" rows="10"></textarea>
    </fieldset>
    <input type="hidden" name="caller" value="articles.new">
    <button class="button-large" id="button-exit"><strong>ОТМЕНИТЬ</strong></button>
    <button type="submit" class="button-large" id="button-submit"><strong>СОХРАНИТЬ ИЗМЕНЕНИЯ</strong></button>

    <button type="button" class="button-large float-right" id="actor-scroll-top"><strong>Наверх страницы</strong></button>
</form>

</body>

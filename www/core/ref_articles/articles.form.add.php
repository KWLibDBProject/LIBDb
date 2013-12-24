<?php ?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Добавление новой статьи</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="../js/jquery-1.10.2.min.js"></script>
    <script src="../js/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="../js/jquery.ui.datepicker.rus.js"></script>
    <script src="../js/tinymce.min.js"></script>

    <link rel="stylesheet" type="text/css" href="../ref_articles/articles.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui-1.10.3.custom.min.css">


    <script src="../js/core.js"></script>
    <script src="ref.articles.js"></script>
    <script type="text/javascript">
        var authorsList = preloadOptionsList('../ref_authors/ref.authors.action.getoptionlist.php');
        var booksList = preloadOptionsList('../ref_books/ref.books.action.getoptionlist.php');
        var topicsList = preloadOptionsList('../ref_topics/ref.topics.action.getoptionlist.php');

        var mode = 'new';

        // loaded values
        currAuthorsList = { 1: 1, 2: 1, 3: 1 }; // getCurrentAuthorsSelection, используется только для EDIT
        var loadedAuthorsNum = 0;
        var lastAuthorNumber = 1;
        var currentBook = 1;
        var currentTopic = 1;


        // tinyMCE inits
        tinymce.init({selector:'textarea#abstract_eng',forced_root_block : "",
            force_br_newlines : true,
            force_p_newlines : false});
        tinymce.init({selector:'textarea#abstract_rus',forced_root_block : "",
            force_br_newlines : true,
            force_p_newlines : false});
        tinymce.init({selector:'textarea#abstract_ukr',forced_root_block : "",
            force_br_newlines : true,
            force_p_newlines : false});

        $(document).ready(function () {
            // onload
            // load authors
            if (mode == 'edit') {
                for (i=1; i<=loadedAuthorsNum; i++)
                {
                    InsertAuthorSelector("#authors_list",i);
                    if (typeof currAuthorsList[i] != 'undefined') {
                        $("select[data-alselector="+i+"] option[value="+currAuthorsList[i]+"]").attr("selected","selected");
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
                dateFormat: 'dd/mm/yy',
                minDate: '01/01/2003',
                maxDate: '01/01/2020',
                showButtonPanel: true,
                showOn: "both",
                buttonImageOnly: true,
                buttonImage: "../css/images/calendar.gif"
            });
            $("#abstract_tabs").tabs({});
            $("#keywords_tabs").tabs();

            // bindings
            $("#authors_list").on('click',".al-delete",function(){ $('li[data-li="'+$(this).attr('data-al')+'"]').remove(); });
            $(".al-add").on('click',function(){ InsertAuthorSelector("#authors_list",lastAuthorNumber); lastAuthorNumber++; });

            $("#button-exit").on('click',function(event){
                event.preventDefault();
                window.location.href = '../ref.articles.show.php';
                return false;
            });

            $("#form_new_article").submit(function(){
                bValid = true;
                // проверка количества авторов
                bValid = bValid && ($("#authors_list").find('li').size());
                bValid = bValid && (strpos($("input[name=pdffile]").val() , '.pdf'));
                if (!bValid) {
                    alert('Не указаны авторы или неправильный файл для загрузки');
                    return false;
                }
            });

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
        <label for="pages">Статья опубликована на страницах</label>
        <input type="text" id="pages" name="pages">
        <label for="the_book">... сборника: </label>
        <select name="book" id="the_book"></select>
        <label for="datepicker">Дата приема на публикацию:</label>
        <input type="text" id="datepicker" name="add_date">
    </fieldset>
    <fieldset>
        <legend>PDF-file</legend>
        <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
        <label for="pdf">Прикрепить PDF-файл:</label>
        <input type="file" name="pdffile" id="pdf">
    </fieldset>
    <fieldset>
        <legend id="authors_legend">Авторы:</legend>
        <ul id="authors_list" class="authorslist"></ul>
        <input type="button" class="al-add" value="Добавить автора">
    </fieldset>
    <fieldset>
        <legend>Название статьи на разных языках</legend>
        <table>
            <tr>
                <td><label for="title_eng">Article title</label></td>
                <td><input type="text" name="title_eng" id="title_eng" size="60" class="text ui-widget-content ui-corner-all"></td>
            </tr>
            <tr>
                <td><label for="title_rus">Название статьи:</label></td>
                <td><input type="text" name="title_rus" id="title_rus" size="60" class="text ui-widget-content ui-corner-all"></td>
            </tr>
            <tr>
                <td><label for="title_ukr">Назва статті:</label></td>
                <td><input type="text" name="title_ukr" id="title_ukr" size="60" class="text ui-widget-content ui-corner-all"></td>
            </tr>
        </table>
    </fieldset>

    <fieldset>
        <legend>Аннотация на разных языках</legend>
        <div id="abstract_tabs">
            <ul>
                <li><a href="#abstract-eng">На английском</a></li>
                <li><a href="#abstract-rus">На русском</a></li>
                <li><a href="#abstract-ukr">На украинском</a></li>
            </ul>
            <div id="abstract-eng">
                <textarea id="abstract_eng" name="abstract_eng"></textarea>
            </div>
            <div id="abstract-rus">
                <textarea id="abstract_rus" name="abstract_rus"></textarea>
            </div>
            <div id="abstract-ukr">
                <textarea id="abstract_ukr" name="abstract_ukr"></textarea>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend>Ключевые слова на разных языках</legend>
        <div id="keywords_tabs">
            <ul>
                <li><a href="#keywords-eng">На английском</a></li>
                <li><a href="#keywords-rus">На русском</a></li>
                <li><a href="#keywords-ukr">На украинском</a></li>
            </ul>
            <div id="keywords-eng">
                <textarea id="keywords_eng" name="keywords_eng" cols="80" rows="6"></textarea>
            </div>
            <div id="keywords-rus">
                <textarea id="keywords_rus" name="keywords_rus" cols="80" rows="6"></textarea>
            </div>
            <div id="keywords-ukr">
                <textarea id="keywords_ukr" name="keywords_ukr" cols="80" rows="6"></textarea>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend>References: </legend>
        <textarea id="refs" name="refs" cols="80" rows="10"></textarea>
    </fieldset>
    <input type="hidden" name="caller" value="articles.new">
    <button type="submit" class="button-large" id="button-submit"><strong>СОХРАНИТЬ ИЗМЕНЕНИЯ</strong></button>
</form>
<button class="button-large" id="button-exit"><strong>ОТМЕНИТЬ</strong></button>
</body>
</html>
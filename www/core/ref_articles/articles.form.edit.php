<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');


$id = IsSet($_GET['id']) ? $_GET['id'] : 1;

$link = ConnectDB();
$query = "select * from articles where id=$id"; // получаем СТАТЬЮ
$res_article = mysql_query($query) or die("Невозможно получить содержимое статьи! ".$query);
$numarticles = @mysql_num_rows($res_article);
if ($numarticles)
{
    $the_article = mysql_fetch_assoc($res_article);
}
// получаем авторов
$query = "select * from cross_aa where article=$id";
$res_authors = mysql_query($query) or die("Невозможно получить кросс-таблицу автор X статья! ".$query);

$numauthors = @mysql_num_rows($res_authors);
$the_loadedAuthorsNum = $numauthors;

$currAuthList = "{ ";

if ($numauthors>0)
{
    for ($i=1;$i<=$numauthors;$i++)
    {
        $tmp = mysql_fetch_assoc($res_authors);
        $currAuthList .= " $i : $tmp[author] ,";
    }
}

$the_currAuthList = substr($currAuthList,0,-1);
$the_currAuthList.= '}'; // значение для currAuthorsList

$the_mode = 'edit';
$the_currentBook = $the_article['book'];
$the_currentTopic = $the_article['topic'];

// получаем ПДФ-файл
$qp = "SELECT id,username,filesize FROM pdfdata WHERE articleid=$id";
$rp = mysql_query($qp,$link);
$np = @mysql_num_rows($rp);
if ($np > 0) {
    $the_file = mysql_fetch_assoc($rp);
}

CloseDB($link);
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Редактирование статьи</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="../js/jquery-1.10.2.min.js"></script>
    <script src="../js/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="../js/jquery.ui.datepicker.rus.js"></script>
    <script src="../js/tinymce.min.js"></script>

    <script src="../js/core.js"></script>
    <script src="ref.articles.js"></script>

    <link rel="stylesheet" type="text/css" href="../ref_articles/articles.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui-1.10.3.custom.min.css">
    <script type="text/javascript">
        var authorsList = preloadOptionsList('../ref_authors/ref.authors.action.getoptionlist.php');
        var booksList = preloadOptionsList('../ref_books/ref.books.action.getoptionlist.php');
        var topicsList = preloadOptionsList('../ref_topics/ref.topics.action.getoptionlist.php');

        var mode = 'edit';
        // loaded values for 'EDIT' mode
        currAuthorsList = <?php echo $the_currAuthList; ?>; // getCurrentAuthorsSelection, используется только для EDIT
        var loadedAuthorsNum = <?php echo $the_loadedAuthorsNum; ?>;
        var lastAuthorNumber = <?php echo $the_loadedAuthorsNum+1; ?>;
        var currentBook = <?php echo $the_currentBook; ?>;
        var currentTopic = <?php echo $the_currentTopic; ?>

        // tinyMCE inits
        tinymce.init({selector:'textarea#abstract_en',forced_root_block : "",
            force_br_newlines : true,
            force_p_newlines : false});
        tinymce.init({selector:'textarea#abstract_ru',forced_root_block : "",
            force_br_newlines : true,
            force_p_newlines : false});
        tinymce.init({selector:'textarea#abstract_uk',forced_root_block : "",
            force_br_newlines : true,
            force_p_newlines : false});
        tinymce.init({selector:'textarea#refs',forced_root_block : "",
            force_br_newlines : true,
            force_p_newlines : false});

        $(document).ready(function () {
            // onload, load authors
            if (mode == 'edit') {
                for (i=1; i<=loadedAuthorsNum; i++)
                {
                    InsertAuthorSelector("#authors_list",i);
                    if (typeof currAuthorsList[i] != 'undefined') {
                        $("select[data-alselector="+i+"] option[value="+currAuthorsList[i]+"]").prop("selected",true);
                    }
                    lastAuthorNumber++;
                }
            } else if (mode == 'new') {} // ничего не добавляем, у нас просто работает 1 кнопка "добавить"

            // load books selector
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
            $("#abstract_tabs").tabs();
            $("#keywords_tabs").tabs();

            // bindings
            $("#authors_list").on('click',".al-delete",function(){ $('li[data-li="'+$(this).attr('data-al')+'"]').remove(); });
            $(".al-add").on('click',function(){ InsertAuthorSelector("#authors_list",lastAuthorNumber); lastAuthorNumber++; });

            // логика кнопок
            $("#currfile_show").on('click',function(){ // show current file
                window.location.href="../getpdf.php?id="+$(this).attr('data-fileid');
            });
            $("#currfile_del").on('click',function(){
                var getting = $.get('../ref_articles/articles.action.deletepdf.php', { id: $(this).attr('data-fileid') });
                getting.done(function(data){
                    result = $.parseJSON(data);
                    if (result['error'] == 0) {
                        $("#newfile_input").removeProp("disabled");
                        $("#currfile_changed").attr("value","1");
                        $("#currfile_old").hide();
                    } else {
                        alert('Ошибка удаления файла!');
                    }
                });
            });

            $("#button-exit").on('click',function(event){
                event.preventDefault();
                window.location.href = '../ref.articles.show.php';
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
                } else if ($("#currfile_changed").val()==1 && !strpos($("input[name=pdffile]").val() , '.pdf'))
                {
                    alert('Указан неправильный файл для загрузки');
                    bValid = false;
                } else if ( $("input[name='add_date']").val().length == 0  ) {
                    alert('Не указана дата!');
                    bValid = false;
                }
                return bValid;
            });

            $("#button-delete").on('click',function(){
                id = $(this).attr('name');
                window.location.href="../ref_articles/articles.action.remove.php?id="+id;
            });

        });
    </script>
</head>

<body>
<form action="../ref_articles/articles.action.update.php" method="post" enctype="multipart/form-data" id="form_edit_article">
    <input type="hidden" name="article_id" value="<?php echo $id; ?>">

    <fieldset>
        <legend>Выпускные данные:</legend>

        <label for="the_topic">Тематический раздел (рубрика): </label>
        <select name="topic" id="the_topic"></select>

        <label for="udc">УДК:</label>
        <input type="text" name="udc" id="udc" class="text ui-widget-content ui-corner-all" value="<?php echo $the_article['udc']; ?>">
    </fieldset>
    <fieldset>
        <legend>Сборник</legend>

        <label for="pages">Статья опубликована на страницах</label>
        <input type="text" id="pages" name="pages" value="<?php echo $the_article['pages']; ?>">

        <label for="the_book">... сборника: </label>
        <select name="book" id="the_book"></select>

        <label for="datepicker">Дата:</label>
        <input type="text" id="datepicker" name="add_date" value="<?php echo $the_article['add_date']; ?>">
    </fieldset>

    <fieldset>
        <legend>PDF-file</legend>
        <input type="hidden" name="MAX_FILE_SIZE" value="10000000">

        <span id="currfile_old">
        <button type="button" id="currfile_show" data-fileid="<?php echo $the_file['id'];?>">Посмотреть</button>
        <br>
        <label for="currfile_text">Текущий файл:</label>
        <input type="text" size="60" id="currfile_text" value="<?php echo $the_file['username']?>">
        <button type="button" id="currfile_del" data-fileid="<?php echo $the_file['id'];?>">Удалить</button>
        </span>
        <br>
        <label for="newfile_input">Прикрепить НОВЫЙ PDF-файл:</label>
        <input type="file" name="pdffile" id="newfile_input" disabled>
        <input type="hidden" name="currfile_changed" id="currfile_changed" value="0">
    </fieldset>
    <fieldset>
        <legend>Авторы:</legend>
        <ul id="authors_list" class="authorslist"></ul>
        <input type="button" class="al-add" value="Добавить автора">
    </fieldset>
    <fieldset>
        <legend>Название статьи на разных языках</legend>
        <table>
            <tr>
                <td><label for="title_en">Article title</label></td>
                <td><input type="text" name="title_en" id="title_en" size="60" class="text ui-widget-content ui-corner-all" value="<?php echo $the_article['title_en'] ;?>"></td>
            </tr>
            <tr>
                <td><label for="title_ru">Название статьи:</label></td>
                <td><input type="text" name="title_ru" id="title_ru" size="60" class="text ui-widget-content ui-corner-all" value="<?php echo $the_article['title_ru'] ;?>"></td>
            </tr>
            <tr>
                <td><label for="title_uk">Назва статті:</label></td>
                <td><input type="text" name="title_uk" id="title_uk" size="60" class="text ui-widget-content ui-corner-all" value="<?php echo $the_article['title_uk'] ;?>"></td>
            </tr>
        </table>
    </fieldset>

    <fieldset>
        <legend>Аннотация на разных языках</legend>
        <div id="abstract_tabs">
            <ul>
                <li><a href="#abstract-en">На английском</a></li>
                <li><a href="#abstract-ru">На русском</a></li>
                <li><a href="#abstract-uk">На украинском</a></li>
            </ul>
            <div id="abstract-en">
                <textarea id="abstract_en" name="abstract_en"><?php echo $the_article['abstract_en'] ;?></textarea>
            </div>
            <div id="abstract-ru">
                <textarea id="abstract_ru" name="abstract_ru"><?php echo $the_article['abstract_ru'] ;?></textarea>
            </div>
            <div id="abstract-uk">
                <textarea id="abstract_uk" name="abstract_uk"><?php echo $the_article['abstract_uk'] ;?></textarea>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend>Ключевые слова на разных языках</legend>
        <div id="keywords_tabs">
            <ul>
                <li><a href="#keywords-en">На английском</a></li>
                <li><a href="#keywords-ru">На русском</a></li>
                <li><a href="#keywords-uk">На украинском</a></li>
            </ul>
            <div id="keywords-en">
                <textarea id="keywords_en" name="keywords_en" cols="80" rows="6"><?php echo $the_article['keywords_en'] ;?></textarea>
            </div>
            <div id="keywords-ru">
                <textarea id="keywords_ru" name="keywords_ru" cols="80" rows="6"><?php echo $the_article['keywords_ru'] ;?></textarea>
            </div>
            <div id="keywords-uk">
                <textarea id="keywords_uk" name="keywords_uk" cols="80" rows="6"><?php echo $the_article['keywords_uk'] ;?></textarea>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend>References: </legend>
        <textarea id="refs" name="refs" cols="80" rows="10"><?php echo $the_article['refs'] ;?></textarea>
    </fieldset>
    <button type="submit" class="button-large" id="button-save"><strong>СОХРАНИТЬ ИЗМЕНЕНИЯ</strong></button>
    <button type="button" class="button-large" id="button-delete" name="<?php echo $id; ?>"><strong>УДАЛИТЬ СТАТЬЮ</strong></button>
</form>
<button type="button" class="button-large" id="button-exit"><strong>ОТМЕНИТЬ</strong></button>
</body>
</html>
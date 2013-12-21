<?php
require_once('../core.php');
require_once('../db.php');
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

// получаем ПДФ-файл
$qp = "SELECT id,username,filesize FROM pdfdata WHERE articleid=$id";
$rp = mysql_query($qp,$link);
$np = @mysql_num_rows($rp);
if ($np > 0) {
    $the_file = mysql_fetch_assoc($rp);
}

// print_r($the_article);
// echo '<hr>';
// print_r($the_currAuthList);

CloseDB($link);
// print_r($the_article);
// print_r($the_currAuthList);
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

    <script src="../ref_articles/core.articles.js"></script>

    <link rel="stylesheet" type="text/css" href="../ref_articles/articles.css">
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui-1.10.3.custom.min.css">
    <script type="text/javascript">
        var authorsList = preloadOptionsList('../ref_authors/ref.authors.action.getoptionlist.php');
        var booksList = preloadOptionsList('../ref_books/ref.books.action.getoptionlist.php');
        var mode = '<?php echo $the_mode; ?>';
        // loaded values for 'EDIT' mode
        currAuthorsList = <?php echo $the_currAuthList; ?>; // getCurrentAuthorsSelection, используется только для EDIT
        var loadedAuthorsNum = <?php echo $the_loadedAuthorsNum; ?>;
        var lastAuthorNumber = <?php echo $the_loadedAuthorsNum+1; ?>;
        var currentBook = <?php echo $the_currentBook; ?>;

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
            } else if (mode == 'new') {} // ничего не добавляем, у нас просто работает 1 кнопка "добавить"

            // load books selector
            BuildBooksSelector('book',booksList,currentBook);

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
                        /*
                        $("#currfile_show").attr('data-fileid','').attr('disabled','disabled');
                        $("#currfile_text").val("*** ВНИМАНИЕ, ФАЙЛ УДАЛЕН ***");
                        $("#newfile_input").removeProp("disabled");
                        $(this).attr("disabled","disabled");
                        */
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
                bValid = true;
                // проверка количества авторов
                bValid = bValid && ($("#authors_list").find('li').size());
                if ($("#currfile_changed").val()==1) {
                    bValid = bValid && (strpos($("input[name=pdffile]").val() , '.pdf'));
                }
                if (!bValid) {
                    alert('Не указаны авторы или неправильный файл для загрузки');
                    return false;
                }
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
    <fieldset>
        <input type="hidden" name="article_id" value="<?php echo $id; ?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="10000000">
        <legend>Общие данные</legend>
        <label for="udc">УДК:</label>
        <input type="text" name="udc" id="udc" class="text ui-widget-content ui-corner-all" value="<?php echo $the_article['udc']; ?>">
        <label for="the_book">Статья входит в сборник: </label>
        <select name="book" id="the_book"></select>
        <label for="datepicker">Дата:</label>
        <input type="text" id="datepicker" name="add_date" value="<?php echo $the_article['add_date']; ?>">
    </fieldset>
    <fieldset>
        <!-- @todo: PDF table -->
        <legend>PDF-file</legend>
        <span id="currfile_old">
        <button type="button" id="currfile_show" data-fileid="<?php echo $the_file['id'];?>">Посмотреть</button>
        <label for="currfile_text">Текущий файл:</label>
        <input type="text" size="60" id="currfile_text" value="<?php echo $the_file['username']?>">
        <button type="button" id="currfile_del" data-fileid="<?php echo $the_file['id'];?>">Удалить</button>
        </span>
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
                <td><label for="title_eng">Article title</label></td>
                <td><input type="text" name="title_eng" id="title_eng" size="60" class="text ui-widget-content ui-corner-all" value="<?php echo $the_article['title_eng'] ;?>"></td>
            </tr>
            <tr>
                <td><label for="title_rus">Название статьи:</label></td>
                <td><input type="text" name="title_rus" id="title_rus" size="60" class="text ui-widget-content ui-corner-all" value="<?php echo $the_article['title_rus'] ;?>"></td>
            </tr>
            <tr>
                <td><label for="title_ukr">Назва статті:</label></td>
                <td><input type="text" name="title_ukr" id="title_ukr" size="60" class="text ui-widget-content ui-corner-all" value="<?php echo $the_article['title_ukr'] ;?>"></td>
            </tr>
        </table>
    </fieldset>

    <fieldset>
        <legend>Аннотация</legend>
        <div id="abstract_tabs">
            <ul>
                <li><a href="#abstract-eng">На английском</a></li>
                <li><a href="#abstract-rus">На русском</a></li>
                <li><a href="#abstract-ukr">На украинском</a></li>
            </ul>
            <div id="abstract-eng">
                <textarea id="abstract_eng" name="abstract_eng"><?php echo $the_article['abstract_eng'] ;?></textarea>
            </div>
            <div id="abstract-rus">
                <textarea id="abstract_rus" name="abstract_rus"><?php echo $the_article['abstract_rus'] ;?></textarea>
            </div>
            <div id="abstract-ukr">
                <textarea id="abstract_ukr" name="abstract_ukr"><?php echo $the_article['abstract_ukr'] ;?></textarea>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend>Ключевые слова (keywords)</legend>
        <div id="keywords_tabs">
            <ul>
                <li><a href="#keywords-eng">На английском</a></li>
                <li><a href="#keywords-rus">На русском</a></li>
                <li><a href="#keywords-ukr">На украинском</a></li>
            </ul>
            <div id="keywords-eng">
                <textarea id="keywords_eng" name="keywords_eng" cols="80" rows="6"><?php echo $the_article['keywords_eng'] ;?></textarea>
            </div>
            <div id="keywords-rus">
                <textarea id="keywords_rus" name="keywords_rus" cols="80" rows="6"><?php echo $the_article['keywords_rus'] ;?></textarea>
            </div>
            <div id="keywords-ukr">
                <textarea id="keywords_ukr" name="keywords_ukr" cols="80" rows="6"><?php echo $the_article['keywords_ukr'] ;?></textarea>
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
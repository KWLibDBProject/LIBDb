<?php
define('__ACCESS_MODE__', 'admin');
require_once '__required.php'; // $mysqli_link

/*$SID = session_id();
if(empty($SID)) session_start();*/
// ifNotLoggedRedirect('/core/');

$authors_count = DB::query("SELECT COUNT(*) FROM `authors`")->fetchColumn() ?? 0;

?>
<html>
<head>
    <title>Справочник: Список авторов</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="_assets/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="_assets/jquery.colorbox.js"></script>
    <link rel="stylesheet" type="text/css" href="_assets/colorbox.css" />

    <link rel="stylesheet" type="text/css" href="_assets/core.admin.css">
    <link rel="stylesheet" type="text/css" href="core.authors/authors.css">

    <script src="core.authors/authors.js"></script>
    <script type="text/javascript" src="../frontend.js"></script>
    <script type="text/javascript" src="../frontend.options.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            var total_authors_count = <?php echo $authors_count; ?>;

            var siteLanguage = 'lang=ru'; // required! we load letters with frontend-declared ajax function

            var url_authors_list = 'core.authors/authors.action.list.php?'+siteLanguage;

            var lettersList = preloadOptionsList('/ajax.php?actor=get_letters_as_optionlist&'+siteLanguage);

            BuildSelector('letter', lettersList, 'Выбрать...', 0);

            // bind hash selectors
            setSelectorsByHash_NEW(".search_selector");

            $(".hash_selectors").on('change', '.search_selector', function(){
                setHashBySelectors();
                // enable or disable button if first letter selected
                $("#actor-show-withselection").prop('disabled', $('select[name="letter"]').val() == 0);
            });

            // onload
            if ($('select[name="letter"]').val() != 0) {
                $("#authors_list").empty().load(url_authors_list + "&letter="+$('select[name="letter"]').val());
                $("#actor-show-withselection").prop('disabled', false);
            } else if (total_authors_count < 100) {
                $("#authors_list").empty().load(url_authors_list);
            }

            // bind exit actor
            $("#actor-exit").on('click',function(event){
                window.location.href = '/core/';
            });
            // bind add actor
            $("#actor-add-item").on('click',function(event){
                window.location.href = 'core.authors/authors.form.php';
            });
            // bind lightbox and edit action
            $('#authors_list')
                    .on('click','.action-edit',function(){
                        window.location.href = 'core.authors/authors.form.php?id='+$(this).attr('name');
                    })
                    .on('click','.lightbox',function(){
                        $.colorbox({
                            photo: true,
                            href: $(this).attr('href')
                        });
                        return false;
                    });

            // search criteria bindings
            $("#actor-show-withselection").on('click',function(){
                var query = "&";
                query+="letter="+$('select[name="letter"]').val();

                $("#authors_list").empty().load(url_authors_list + query);
            });

            $("#actor-show-all").on('click',function(){
                // reset search selector
                $('select[name="letter"]').val(0);
                setHashBySelectors();
                $("#authors_list").empty().load(url_authors_list);
            });
            $("#actor-show-abc").on('click', function(){
                $("#authors_list").empty().load(url_authors_list + '&order_by_name=yes&');
            });
        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="actor-exit"><strong><<< НАЗАД </strong></button>
<button type="button" class="button-large" id="actor-add-item">Добавить автора</button><br>
<hr>
<fieldset>
    <legend>Критерии поиска</legend>
    Первая буква имени: <form class="hash_selectors inline_form"><select name="letter" class="search_selector"><option value="0">ANY</option></select></form>
    <button id="actor-show-withselection" class="button-large" disabled>Показать выбранных</button>
    <div style="padding-right:4px; border-left: 6px solid black;display: inline;"></div>
    <button id="actor-show-all" class="button-large">Показать всех</button>
    <button id="actor-show-abc" class="button-large">Показать всех (сортировка пофамильно)</button>
</fieldset>

<fieldset class="result-list table-hl-rows">
    <legend>Результаты поиска</legend>
    <div id="authors_list">
        В базе больше 100 авторов. Сузьте критерии поиска и нажмите "Показать всех" или "Показать выбранных"
    </div>
</fieldset>

</body>
</html>

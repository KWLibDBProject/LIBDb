<?php
require_once('core.php');
// require_once('core.db.php');
// require_once('core.kwt.php');

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) header('Location: /core/');

?>
<html>
<head>
    <title>Справочник: Список авторов</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="js/jquery.colorbox.js"></script>
    <link rel="stylesheet" type="text/css" href="css/colorbox.css" />

    <link rel="stylesheet" type="text/css" href="css/core.admin.css">
    <link rel="stylesheet" type="text/css" href="ref_authors/authors.css">

    <script src="js/core.js"></script>
    <script src="ref_authors/ref.authors.js"></script>
    <script type="text/javascript" src="/tpl/frontend.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            var siteLanguage = 'lang=ru';
            var lettersList = preloadOptionsList('/ajax.php?actor=get_letters_as_optionlist&'+siteLanguage);
            BuildSelector('letter', lettersList, 0);

            // bind hash selectors
            setSelectorsByHash(".search_selector");
            $(".hash_selectors").on('change', '.search_selector', function(){
                setHashBySelectors();
            });

            // onload
            $("#authors_list").load("ref_authors/authors.action.list.php");

            // bind exit actor
            $(".actor_exit").on('click',function(event){
                window.location.href = '/core/';
            });
            // bind add actor
            $(".actor-add-item").on('click',function(event){
                window.location.href = 'ref_authors/authors.form.php';
            });
            // bind lightbox and edit action
            $('#authors_list')
                    .on('click','.actor-edit',function(){
                        window.location.href = 'ref_authors/authors.form.php?id='+$(this).attr('name');
                    })
                    .on('click','.lightbox',function(){
                        $.colorbox({
                            photo: true,
                            href: $(this).attr('href')
                        });
                        return false;
                    });
            // search criteria bindings
            $(".actor-show-withselection").on('click',function(){
                var query = "&";
                query+="letter="+$('select[name="letter"]').val();
                $("#authors_list").empty().load('ref_authors/authors.action.list.php?'+siteLanguage+query);
            });

            $(".actor-show-all").on('click',function(){
                $("#authors_list").empty().load('ref_authors/authors.action.list.php?'+siteLanguage);
            });
        });
    </script>
</head>
<body>
<button type="button" class="button-large actor-exit"><strong><<< НАЗАД </strong></button>
<button type="button" class="button-large actor-add-item">Добавить автора</button><br>
<hr>
<fieldset>
    <legend>Критерии поиска</legend>
    Первая буква имени: <form class="hash_selectors inline_form"><select name="letter" class="search_selector"><option value="0">ANY</option></select></form>
    <button class="actor-show-withselection">Показать выбранных</button>
    <button class="actor-show-all">Показать всех</button>
</fieldset>

<fieldset class="result-list table-hl-rows">
    <legend>Результаты поиска</legend>
    <div id="authors_list">
    </div>
</fieldset>

</body>
</html>

<?php
require_once('core.php');
// require_once('core.db.php');
// require_once('core.kwt.php');

$SID = session_id();
if(empty($SID)) session_start();
ifNotLoggedRedirect('/core/');

?>
<html>
<head>
    <title>Справочник: Список авторов</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="js/jquery.colorbox.js"></script>
    <link rel="stylesheet" type="text/css" href="css/colorbox.css" />

    <link rel="stylesheet" type="text/css" href="css/core.admin.css">
    <link rel="stylesheet" type="text/css" href="core.authors/authors.css">

    <script src="js/core.js"></script>
    <script src="core.authors/authors.js"></script>
    <script type="text/javascript" src="/frontend.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            var siteLanguage = 'lang=ru'; // required! we load letters with frontend-declared ajax function

            var lettersList = preloadOptionsList('/ajax.php?actor=get_letters_as_optionlist&'+siteLanguage);

            BuildSelector('letter', lettersList, 0);

            // bind hash selectors
            setSelectorsByHash(".search_selector");
            $(".hash_selectors").on('change', '.search_selector', function(){
                setHashBySelectors();
                // enable or disable button if first letter selected
                $("#actor-show-withselection").prop('disabled', !($('select[name="letter"]').val() != '0') );
            });

            // onload
            $("#authors_list").empty().load('core.authors/authors.action.list.php?'+siteLanguage+"&"+"letter="+$('select[name="letter"]').val());

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
                $("#authors_list").empty().load('core.authors/authors.action.list.php?'+siteLanguage+query);
            });

            $("#actor-show-all").on('click',function(){
                // reset search selector
                $('select[name="letter"]').val(0);
                setHashBySelectors();
                $("#authors_list").empty().load('core.authors/authors.action.list.php?'+siteLanguage);
            });
            $("#actor-show-abc").on('click', function(){
                $("#authors_list").empty().load('core.authors/authors.action.list.php?order_by_name=yes&'+siteLanguage);
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
    <button id="actor-show-abc">Отсортировать по фамилии</button>
    <button id="actor-show-all">Показать всех</button>
    Первая буква имени: <form class="hash_selectors inline_form"><select name="letter" class="search_selector"><option value="0">ANY</option></select></form>
    <button id="actor-show-withselection" disabled>Показать выбранных</button>


</fieldset>

<fieldset class="result-list table-hl-rows">
    <legend>Результаты поиска</legend>
    <div id="authors_list">
    </div>
</fieldset>

</body>
</html>

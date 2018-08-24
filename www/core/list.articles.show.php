<?php
define('__ACCESS_MODE__', 'admin');
/*$SID = session_id();
if(empty($SID)) session_start();*/

require_once '__required.php'; // $mysqli_link

// ifNotLoggedRedirect('/core/');

//@todo: не загружать статьи если их больше 100, вместо этого писать сообщение об этом

?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Список статей</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="_assets/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="../frontend.js"></script>
    <script type="text/javascript" src="../frontend.options.js"></script>

    <link rel="stylesheet" type="text/css" href="_assets/core.admin.css">
    <link rel="stylesheet" type="text/css" href="core.articles/articles.css">

    <script type="text/javascript">
        var url_extended = "core.articles/articles.action.list.php";

        var url_get_articles_count = ""; // ЗАЧЕМ оно нам? через ajax.php

        var booksList = preloadOptionsList('core.books/books.action.getoptionlist.php');

        var topicsList = preloadOptionsList('core.topics/topics.action.getoptionlist.php');

        var authorsList = preloadOptionsList('core.authors/authors.action.getoptionlist.php');



        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            BuildSelectorExtended('with_author',authorsList, '');
            BuildSelectorExtended('with_book',booksList, '');
            BuildSelectorExtended('with_topic',topicsList, '');

            setSelectorsByHash(".search_selector");
            $(".hash_selectors").on('change', '.search_selector', function(){
                setHashBySelectors();
            });

            // если хэш установлен - нужно загрузить статьи согласно выбранным позициям
            wlh = (window.location.hash).substr(1);
            if (wlh !== '') {
                query = "?";
                query+="author="+$('select[name="with_author"]').val();
                query+="&topic="+$('select[name="with_topic"]').val();
                query+="&book="+$('select[name="with_book"]').val();
            } else {
                query = '';
            }

            //??? дергаем базу на предмет количества статей

            // загружаем статьи согласно стартовым селекторам
            $("#articles_list").empty().load(url_extended + query);

            $("#button-newarticle").on('click',function(){
                location.href = 'core.articles/articles.form.add.php';
            });
            $("#button-exit").on('click',function(){
                location.href = '/core/';
            });
            $('#articles_list')
                    .on('click','.action-download-pdf',function(){
                        window.location.href="get.file.php?id="+$(this).attr('name')
                    })
                    .on('click','.action-edit',function(){
                        location.href = 'core.articles/articles.form.edit.php?id='+$(this).attr('name');
                    });

            $("#button-show-withselection").on('click',function(){
                query = "?";
                query+="author="+$('select[name="with_author"]').val();
                query+="&topic="+$('select[name="with_topic"]').val();
                query+="&book="+$('select[name="with_book"]').val();
                $("#articles_list").empty().load(url_extended+query);
            });

            $("#button-reset-selection").on('click',function(){
                $('select[name="with_author"]').val(0);
                $('select[name="with_topic"]').val(0);
                $('select[name="with_book"]').val(0);
            });

            $("#button-show-all").on('click',function(){
                $("#articles_list").empty().load(url_extended);
            });


        });
    </script>
</head>

<body>
<button id="button-exit" class="button-large button-bold"><<< НАЗАД </button>
<button id="button-newarticle" class="button-large">Добавить статью </button>
<hr>
<fieldset class="hash_selectors">
    <legend>Критерии отбора</legend>

    <table border="0">
        <tr>
            <td>
                Автор:
            </td>
            <td>
                <select name="with_author" class="search_selector"><option value="0">ЛЮБОЙ</option></option></select>
            </td>
        </tr>
        <tr>
            <td>
                Тематический раздел:&nbsp;&nbsp;&nbsp;
            </td>
            <td>
                <select name="with_topic" class="search_selector"><option value="0">ЛЮБОЙ</option></select>
            </td>
        </tr>
        <tr>
            <td>
                Сборник (книга):
            </td>
            <td>
                <select name="with_book" class="search_selector"><option value="0">ЛЮБОЙ</option></select>
            </td>
        </tr>
    </table>
    <hr/>
    <button id="button-show-withselection" class="button-large">Показать выбранное</button>
    <button id="button-reset-selection" class="button-large">Сбросить критерии</button>
    <button id="button-show-all" class="button-large hidden">Показать ВСЕ статьи</button>
</fieldset>
<fieldset class="result-list table-hl-rows">
    <legend>Результаты поиска</legend>
    <div id="articles_list">
    </div>
</fieldset>


</body>
</html>
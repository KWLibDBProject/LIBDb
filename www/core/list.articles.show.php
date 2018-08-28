<?php
define('__ACCESS_MODE__', 'admin');
/*$SID = session_id();
if(empty($SID)) session_start();*/

require_once '__required.php'; // $mysqli_link

// ifNotLoggedRedirect('/core/');

$articles_count = DB::query("SELECT COUNT(id) FROM `articles`")->fetchColumn() ?? 0;

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
        $(document).ready(function () {
            var total_articles_count = <?php echo $articles_count; ?>;

            $.ajaxSetup({cache: false});

            var siteLanguage = 'lang=ru'

            var url_extended = "core.articles/articles.action.list.php";

            var booksList = preloadOptionsList('core.books/books.action.getoptionlist.php');

            var topicsList = preloadOptionsList('core.topics/topics.action.getoptionlist.php');

            var authorsList = preloadOptionsList('core.authors/authors.action.getoptionlist.php');

            var firstLettersList = preloadOptionsList('/ajax.php?actor=get_letters_as_optionlist&' + siteLanguage);

            BuildSelectorExtended('with_author', authorsList, '');
            BuildSelectorExtended('with_book', booksList, '');
            BuildSelectorExtended('with_topic', topicsList, '');
            BuildSelector('with_letter', firstLettersList, 'Выбрать...', 0);

            setSelectorsByHash(".search_selector");
            $(".hash_selectors").on('change', '.search_selector', function () {
                setHashBySelectors();
            });

            // если хэш установлен - нужно загрузить статьи согласно выбранным позициям
            wlh = (window.location.hash).substr(1);
            if (wlh !== '') {
                query = "?";
                query += "author=" + $('select[name="with_author"]').val();
                query += "&topic=" + $('select[name="with_topic"]').val();
                query += "&book=" + $('select[name="with_book"]').val();
                query += "&firstletter=" + $('select[name="with_letter"]').val();
            } else {
                query = '';
            }

            // onLoad
            if (query.length) {
                $("#articles_list").empty().load(url_extended + query);
            } else if (total_articles_count < 100) {
                $("#articles_list").empty().load(url_extended);
            }

            $("#button-newarticle").on('click', function () {
                location.href = 'core.articles/articles.form.add.php';
            });
            $("#button-exit").on('click', function () {
                location.href = '/core/';
            });
            $('#articles_list')
                .on('click', '.action-download-pdf', function () {
                    window.location.href = "get.file.php?id=" + $(this).attr('name')
                })
                .on('click', '.action-edit', function () {
                    location.href = 'core.articles/articles.form.edit.php?id=' + $(this).attr('name');
                });

            $("#button-show-withselection").on('click', function () {
                query = "?";
                query += "author=" + $('select[name="with_author"]').val();
                query += "&topic=" + $('select[name="with_topic"]').val();
                query += "&book=" + $('select[name="with_book"]').val();
                query += "&firstletter=" + $('select[name="with_letter"]').val();

                $("#articles_list").empty().load(url_extended + query);
            });

            $("#button-reset-selection").on('click', function () {
                $('select[name="with_author"]').val(0);
                $('select[name="with_topic"]').val(0);
                $('select[name="with_book"]').val(0);
                $('select[name="with_letter"]').val(0);
            });

            $("#button-show-all").on('click', function () {
                $("#articles_list").empty().load(url_extended);
            });


        });
    </script>
</head>

<body>
<button id="button-exit" class="button-large button-bold"><<< НАЗАД</button>
<button id="button-newarticle" class="button-large">Добавить статью</button>
<hr>
<fieldset class="hash_selectors">
    <legend>Критерии отбора статей</legend>

    <table border="0">
        <tr>
            <td>
                Первая буква фамилии
            </td>
            <td>
                <select name="with_letter"><option value="0">ANY</option></select>
                (одного из авторов)
            </td>
        </tr>
        <tr>
            <td>
                Автор (один из):
            </td>
            <td>
                <select name="with_author" class="search_selector">
                    <option value="0">ЛЮБОЙ</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                Тематический раздел:&nbsp;&nbsp;&nbsp;
            </td>
            <td>
                <select name="with_topic" class="search_selector">
                    <option value="0">ЛЮБОЙ</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                Сборник (книга):
            </td>
            <td>
                <select name="with_book" class="search_selector">
                    <option value="0">ЛЮБОЙ</option>
                </select>
            </td>
        </tr>
    </table>
    <hr/>
    <button id="button-show-withselection" class="button-large">Показать выбранное</button>
    <button id="button-reset-selection" class="button-large">Сбросить критерии</button>
    <button id="button-show-all" class="button-large">Показать <strong>ВСЕ</strong> статьи</button>
</fieldset>
<fieldset class="result-list table-hl-rows">
    <legend>Результаты поиска</legend>
    <div id="articles_list">
        В базе больше 100 статей. Сузьте критерии поиска и нажмите "Показать выбранное" или "Показать все статьи"
    </div>
</fieldset>


</body>
</html>
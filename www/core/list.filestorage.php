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
    <title>FileStorage::List</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="js/jquery.colorbox.js"></script>
    <link rel="stylesheet" type="text/css" href="css/colorbox.css" />

    <link rel="stylesheet" type="text/css" href="css/core.admin.css">

    <script type="text/javascript" src="js/core.js"></script>
    <script type="text/javascript" src="js/core.excel.js"></script>
    <script type="text/javascript" src="/frontend.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});

            setSelectorsByHash(".search_selector");
            $(".hash_selectors").on('change', '.search_selector', function(){
                setHashBySelectors();
            });

            // onload
            $("#files_list").load("core.filestorage/filestorage.action.list.php");

            // bind exit actor
            $("#actor-exit").on('click',function(event){
                window.location.href = '/core/';
            });
            // bind lightbox and edit action
            $('#files_list')
                    .on('click','.lightbox',function(){
                        $.colorbox({
                            photo: true,
                            href: $(this).attr('href')
                        });
                        return false;
                    });
            // search criteria bindings
            $("#actor-show-withselection").on('click', function() {
                var query = "collection="+$('select[name="collection"]').val();
                $("#files_list").empty().load('core.filestorage/filestorage.action.list.php?'+query);
            });

            $("#actor-select-collection").on('change', function() {
                var flag = !!($('select[name="collection"]').val() == 'all' );
                $("#actor-show-withselection").prop('disabled', flag);
            });

            $("#actor-show-all").on('click',function(){
                // reset search selector
                $('select[name="collection"]').val('all');
                $("#actor-show-withselection").prop('disabled', true);
                setHashBySelectors();
                $("#files_list").empty().load('core.filestorage/filestorage.action.list.php');
            });
            $("#actor-export-excel").on('click', function(){
                tableToExcel('exportable', 'export');
            })
        });
    </script>
    <style type="text/css">
        .center {
            text-align: center;
        }
        form.inline_form {
            display: inline;
        }
        button[disabled] {
            color: gray;
        }
        #actor-exit {
            float:right;
        }
        .hint {
            display: none;
        }

    </style>
</head>
<body>
<fieldset>
    <legend>Критерии поиска</legend>
    <button id="actor-show-all">Показать всё</button>
    Коллекция: <form class="hash_selectors inline_form">
    <select id="actor-select-collection" name="collection" class="search_selector">
        <option value="all">&nbsp;</option>
        <option value="articles">Статьи</option>
        <option value="authors">Авторы</option>
        <option value="books">Сборники</option>
    </select></form>
    <button id="actor-show-withselection" disabled>Показать выбранную коллекцию</button>
    <button id="actor-export-excel">Export to Excel</button>
    <button type="button" id="actor-exit"><strong><<< НАЗАД </strong></button>
    <div class="hint">
        <hr>
    </div>
</fieldset>

<fieldset class="result-list table-hl-rows">
    <legend>Результаты поиска</legend>
    <div id="files_list">
    </div>
</fieldset>

</body>
</html>


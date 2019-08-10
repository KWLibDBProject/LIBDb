<?php
define('__ACCESS_MODE__', 'admin');
require_once '__required.php'; // $mysqli_link

$files_count = DB::getRowCount(FileStorage::getStorageTable());
$files_limit = 100;

$files_message = pluralForm($files_count, 'файл|файла|файлов');

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>FileStorage::List</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="_assets/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="_assets/jquery.colorbox.js"></script>
    <link rel="stylesheet" type="text/css" href="_assets/colorbox.css" />

    <link rel="stylesheet" type="text/css" href="_assets/core.admin.css">

    <script type="text/javascript" src="_assets/core.excel.js"></script>
    <script type="text/javascript" src="../frontend.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            var url_files_list = "core.filestorage/filestorage.action.list.php";
            var total_files_count = <?php echo $files_count; ?>;
            var limit_files_count = <?php echo $files_limit; ?>;

            $.ajaxSetup({cache: false});

            // on change search selectors SET new window-hash
            $(".hash-selectors").on('change', '.search-selector', function(){
                var flag = !!($('select[name="collection"]').val() == 'all');
                flag = flag && !!($('select[name="sort-type"]').val() == 'id');
                flag = flag && !!($('select[name="sort-order"]').val() == 'ASC');
                $("#actor-show-selection").prop('disabled', flag);

                setHashBySelectors('.search-selector');
            });

            // onload full list
            if (total_files_count < limit_files_count) {
                $("#files_list").empty().load(url_files_list);
            }

            // bind exit actor
            $("#actor-exit").on('click',function(event){
                window.location.href = '/core/';
            });
            // bind lightbox action
            $('#files_list')
                    .on('click','.lightbox',function(){
                        $.colorbox({
                            photo: true,
                            href: $(this).attr('href')
                        });
                        return false;
                    });

            // search criteria bindings
            $("#actor-show-selection").on('click', function() {
                var query = "collection=" + $('select[name="collection"]').val();
                    query+= "&sort-type="  + $('select[name="sort-type"]').val();
                    query+= "&sort-order=" + $('select[name="sort-order"]').val();
                clearHash();
                $("#files_list").empty().load(url_files_list + '?' + query);
            });

            $("#actor-show-all").on('click' , function() {
                $('select[name="collection"]').val('all');
                $('select[name="sort-type"]').val('id');
                $('select[name="sort-order"]').val('ASC');
                clearHash();
                $("#files_list").empty().load(url_files_list);
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
        button[disabled] {
            color: gray;
        }
        .float-right {
            float: right;
        }

    </style>
</head>
<body>
<fieldset>
    <legend>Критерии поиска и сортировки</legend>
    <form class="hash-selectors inline-form">
        <button id="actor-show-all">Показать всё</button>
        <button type="button" id="actor-exit" class="float-right"><strong><<< НАЗАД </strong></button>
        <hr/>
        Коллекция:
        <select id="actor-select-collection" name="collection" class="search-selector">
            <option value="all">&nbsp;</option>
            <option value="articles">Статьи</option>
            <option value="authors">Авторы</option>
            <option value="books">Сборники</option>
        </select>
        Критерий сортировки:
        <select id="actor-select-sort-type" name="sort-type" class="search-selector">
            <option value="id">&nbsp;</option>
            <option value="username">Пользовательское имя</option>
            <option value="stat_date_insert">Дата загрузки</option>
            <option value="filesize">Размер</option>
            <option value="relation">Связь (relation)</option>
            <option value="stat_download_counter">Количество загрузок</option>
        </select>
        Порядок сортировки:
        <select id="actor-select-sort-order" name="sort-order" class="search-selector">
            <option value="ASC">По возрастанию А..Я</option>
            <option value="DESC">По убыванию Я..А</option>
        </select>
        <button type="button" id="actor-show-selection" disabled>Показать выбранное</button>
        <hr/>
        <button type="button" id="actor-export-excel" class="float-right">Export to Excel</button>
    </form>
</fieldset>
<fieldset class="result-list table-hl-rows">
    <legend>Результаты поиска</legend>
    <div id="files_list">
        В базе <?php echo "{$files_count} {$files_message}"; ?>. Укажите критерии поиска и нажмите "Показать выбранное" или "Показать всё".
    </div>
</fieldset>

</body>
</html>


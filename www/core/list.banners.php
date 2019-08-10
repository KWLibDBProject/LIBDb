<?php
define('__ACCESS_MODE__', 'admin');
require_once '__required.php'; // $mysqli_link

/*
Ref.banners BASED AT Ref.abstract

Banners table: {
id PK AI
data_url_image varchar 255
data_url_href varchar 255
data_alt varchar 100        alternative text
data_is_visible int 0|1
data_comment varchar 64
}

*/
$reference = 'banners'; // вообще то если ref не задано - работать не с чем
$return = '';

$action = isset($_GET['action']) ? $_GET['action'] : 'no-action';
$action = getAllowedValue($action, array(
    'insert', 'update', 'remove', 'load', 'list', 'row-list', 'no-action'
), 'no-action');


switch ($action) {
    case 'insert':
    {
        $q = array(
            'data_url_image' => mysqli_real_escape_string($mysqli_link, $_GET['data_url_image']),
            'data_url_href' => mysqli_real_escape_string($mysqli_link, $_GET['data_url_href']),
            'data_alt' => mysqli_real_escape_string($mysqli_link, $_GET['data_alt']),
            'data_is_visible' => mysqli_real_escape_string($mysqli_link, $_GET['data_is_visible']),
            'data_comment' => mysqli_real_escape_string($mysqli_link, $_GET['data_comment']),
        );
        $qstr = MakeInsert($q, $reference);
        $res = mysqli_query($mysqli_link, $qstr) or Die("Unable to insert data to DB!".$qstr);
        $new_id = mysqli_insert_id($mysqli_link) or Die("Unable to get last insert id! Last request is [$qstr]");

        $result['message'] = $qstr;
        $result['error'] = 0;
        $return = json_encode($result);
        break;
    } // case 'insert'
    case 'update':
    {
        $id = $_GET['id'];
        $q = array(
            'data_url_image' => mysqli_real_escape_string($mysqli_link, $_GET['data_url_image']),
            'data_url_href' => mysqli_real_escape_string($mysqli_link, $_GET['data_url_href']),
            'data_alt' => mysqli_real_escape_string($mysqli_link, $_GET['data_alt']),
            'data_is_visible' => mysqli_real_escape_string($mysqli_link, $_GET['data_is_visible']),
            'data_comment' => mysqli_real_escape_string($mysqli_link, $_GET['data_comment']),
        );

        $qstr = MakeUpdate($q, $reference, "WHERE id=$id");
        $res = mysqli_query($mysqli_link, $qstr) or Die("Unable update data : ".$qstr);

        $result['message'] = $qstr;
        $result['error'] = 0;
        $return = json_encode($result);
        break;
    } // case 'update
    case 'remove':
    {
        $id = $_GET['id'];
        $q = "DELETE FROM $reference WHERE (id=$id)";
        if ($r = mysqli_query($mysqli_link, $q)) {
            // запрос удаление успешен
            $result["error"] = 0;
            $result['message'] = 'Удаление успешно';

        } else {
            // DB error again
            $result["error"] = 1;
            $result['message'] = 'Ошибка удаления!';
        }
        $return = json_encode($result);
        break;
    } // case 'remove
    case 'load':
    {   // get single record
        $id = $_GET['id'];
        $query = "SELECT * FROM $reference WHERE id=$id";
        $res = mysqli_query($mysqli_link, $query) or die("Невозможно получить содержимое справочника! ".$query);
        $ref_numrows = mysqli_num_rows($res);

        if ($ref_numrows != 0) {
            $result['data'] = mysqli_fetch_assoc($res);
            $result['error'] = 0;
            $result['message'] = '';
        } else {
            $result['error'] = 1;
            $result['message'] = 'Ошибка базы данных!';
        }
        $return = json_encode($result);
        break;
    } // case 'load'
    case 'list':
    {   // get full list
        $query = "SELECT * FROM $reference";
        $res = mysqli_query($mysqli_link, $query) or die("mysqli_query_error: ".$query);

        $ref_numrows = @mysqli_num_rows($res) ;
        $return = <<<TABLE_START
<table border="1" width="100%">
<tr>
    <th width="1%">(id)</th>
    <th>Banner</th>
    <th>URL href</th>
    <th>IMAGE href</th>
    <th>Alternative text</th>
    <th width="7%">Active?</th>
    <th width="7%">Control</th>
</tr>
TABLE_START;
        if ($ref_numrows > 0) {
            while ($ref_record = mysqli_fetch_assoc($res))
            {
                $is_visible = ($ref_record['data_is_visible']==1) ? "Да" : "Нет";
                $return.= <<<TABLE_EACHROW
<tr>
    <td>{$ref_record['id']}</td>
    <td>
        <img src="{$ref_record['data_url_image']}">
    </td>
    <td><a href="{$ref_record['data_url_href']}" target="_blank">{$ref_record['data_url_href']}</a></td>
    <td>
        <small>{$ref_record['data_url_image']}</small>
    </td>
    <td>{$ref_record['data_alt']}</td>
    <td class="centred_cell">{$is_visible}</td>
    <td class="centred_cell"><button class="actor-edit button-edit" name="{$ref_record['id']}">Edit</button></td>
</tr>
TABLE_EACHROW;
            }
        } else {
            $return .= <<<TABLE_IS_EMPTY
<tr><td colspan="7">Таблица баннеров пуста!</td></tr>
TABLE_IS_EMPTY;
        }
        break;
    } // case 'list'
    case 'row-list': { // возвращает LI-список (VIEW!) баннеров
        $query = "SELECT * FROM $reference WHERE data_is_visible=true";
        $res = mysqli_query($mysqli_link, $query) or die("mysqli_query_error: ".$query);
        $res_numrows = @mysqli_num_rows($res);
        $return = '';
        if ($res_numrows > 0)
        {
            while ($row = mysqli_fetch_assoc($res)) {
                $return .= <<<EACH_BANNER
                <li class="banner-item">
                    <a href="{$row['data_url_href']}" target="_blank" class="banner-item-href">
                        <img src="{$row['data_url_image']}">
                    </a>
                </li>
EACH_BANNER;
            }
        }
        break;
    } // case 'row-list'
    case 'no-action': {
        ?>
    <html lang="ru">
    <head>
    <title>Работа со справочником баннеров</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="_assets/jquery-1.10.2.min.js"></script>
    <script src="_assets/jquery-ui-1.10.3.custom.min.js"></script>
    <link rel="stylesheet" type="text/css" href="_assets/jquery-ui-1.10.3.custom.min.css">

    <style type="text/css">
        body {
            font-family: "Trebuchet MS", "Helvetica", "Arial",  "Verdana", "sans-serif";
            font-size: 62.5%;
        }
        label, input { display:block; }
        input.text {
            margin-bottom:12px;
            width:95%;
            padding: .4em;
        }
        fieldset {
            padding:0;
            border:0;
            margin-top:25px;
        }
        h1 {
            font-size: 1.2em;
            margin: .6em 0;
        }
        .ui-dialog .ui-state-error {
            padding: .3em;
        }
        #ref_list {
            height: 500px;
            width: 99%;
            border: 1px solid gray;
            overflow-y: scroll;
        }
        .centred_cell, th {
            text-align: center;
        }
        .button-large {
            height: 60px;
        }
    </style>
    <script type="text/javascript">
        var ref_name = '<?php echo $reference ?>';
        var button_id = 0;

        function ShowErrorMessage(message)
        {
            alert(message);
        }

        function Banner_CallAddItem(source, id)
        {
            var $form = $(source).find('form');
            var url = $form.attr("action");
            var getting = $.get(url, {
                data_url_image: $form.find("input[name='add_data_url_image']").val(),
                data_url_href: $form.find("input[name='add_data_url_href']").val(),
                data_comment: $form.find("input[name='add_data_comment']").val(),
                data_alt: $form.find("input[name='add_data_alt']").val(),
                //@todo
                data_is_visible : ($form.find("input[name='add_data_is_visible']").attr("checked") ? 1 : 0)
            } );
            getting.done(function(data) {
                var result = $.parseJSON(data);
                if (result['error']==0) {
                    $("#ref_list").empty().load("?action=list");
                    $( source ).dialog( "close" );
                } else {
                    $( source ).dialog( "close" );
                }
            });
        }
        function Banner_CallUpdateItem(source, id)
        {
            var $form = $(source).find('form');
            var getting = $.get($form.attr("action"), {
                data_url_image: $form.find("input[name='edit_data_url_image']").val(),
                data_url_href: $form.find("input[name='edit_data_url_href']").val(),
                data_comment: $form.find("input[name='edit_data_comment']").val(),
                data_alt: $form.find("input[name='edit_data_alt']").val(),
                //@todo
                data_is_visible : ($form.find("input[name='edit_data_is_visible']").prop("checked") ? 1 : 0),
                id: id
            } );
            getting.done(function(data){
                var result = $.parseJSON(data);
                if (result['error']==0) {
                    $("#ref_list").empty().load("?action=list");
                    $( source ).dialog( "close" );
                } else {
                    $( source ).dialog( "close" ); // Some errors, show message!
                }
            });


        }
        function Banner_CallRemoveItem(target, id)
        {
            var getting = $.get('?action=remove', { id: id });
            getting.done(function(data){
                var result = $.parseJSON(data);
                if (result['error'] == 0) {
                    $('#ref_list').empty().load("?action=list");
                    $( target ).dialog( "close" );
                } else {
                    ShowErrorMessage(result['message']);
                    $( target ).dialog( "close" );
                }
            });

        }
        function Banner_CallLoadItem(target, id)
        {
            var getting = $.get('?action=load', {
                id: id
            });
            var $form = $(target).find('form');
            getting.done(function(data){
                var result = $.parseJSON(data);
                if (result['error'] == 0) {
                    $form.find("input[name='edit_data_url_image']").val( result['data']['data_url_image'] );
                    $form.find("input[name='edit_data_url_href']").val( result['data']['data_url_href'] );
                    $form.find("input[name='edit_data_alt']").val( result['data']['data_alt'] );
                    $form.find("input[name='edit_data_is_visible']").prop("checked", !!parseInt(result['data']['data_is_visible']));
                    $form.find("input[name='edit_data_comment']").val( result['data']['data_comment'] );
                } else {
                    ShowErrorMessage("Ошибка загрузки данных!");
                }
            });
        }

        $(document).ready(function () {
            $.ajaxSetup({cache: false, async: false });

            $("#ref_list").load("?action=list");

            /* вызов и обработчик диалога ADD-ITEM */
            $("#actor-add").on('click',function() {
                $('#add_form').dialog('open');
            });

            $( "#add_form" ).dialog({
                autoOpen: false,
                height: 400,
                width: 600,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Добавить",
                        click: function() {
                            Banner_CallAddItem(this);
                            $(this).find('form').trigger('reset');
                            // логика добавления
                            $( this ).dialog( "close" );
                        }
                    },
                    {
                        text: "Сброс",
                        click: function() {
                            $(this).find('form').trigger('reset');
                        }
                    },
                    {
                        text: "Отмена",
                        click: function() {
                            $(this).find('form').trigger('reset');
                            // просто отмена
                            $( this ).dialog( "close" );
                        }

                    }
                ],
                close: function() {
                    $(this).find('form').trigger('reset');
                }
            });

            /* вызов и обработчик диалога редактирования */

            $('#ref_list').on('click', '.actor-edit', function() {
                var button_id = $(this).attr('name');
                Banner_CallLoadItem("#edit_form", button_id);
                $('#edit_form').dialog('open');
            });
            
            $( "#edit_form" ).dialog({
                autoOpen: false,
                height: 400,
                width: 600,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Принять и обновить данные",
                        click: function() {
                            Banner_CallUpdateItem(this, button_id);
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    },
                    {
                        text: "Удалить значение из базы",
                        click: function() {
                            Banner_CallRemoveItem(this, button_id);
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    }
                ],
                close: function() {
                    $(this).find('form').trigger('reset');
                }
            });

            $("#actor-exit").on('click',function(event){
                location.href = '/core/';
            });

        });
    </script>
    </head>
    <body>
    <button type="button" class="button-large" id="actor-exit"><strong> <<< НАЗАД </strong></button>
    <button type="button" class="button-large" id="actor-add"><strong>Добавить баннер</strong></button><br>

    <div id="add_form" title="Добавить запись в таблицу баннеров">
        <form action="?action=insert">
            <fieldset>
                <label for="add_data_url_href">Ссылка на сайт, предоставляющий баннер:</label>
                <input type="text" name="add_data_url_href" id="add_data_url_href" class="text ui-widget-content ui-corner-all">

                <label for="add_data_url_image">Ссылка на изображение: </label>
                <input type="text" name="add_data_url_image" id="add_data_url_image" class="text ui-widget-content ui-corner-all">

                <label for="add_data_alt">Альтернативный текст (ALT-атрибут): </label>
                <input type="text" name="add_data_alt" id="add_data_alt" class="text ui-widget-content ui-corner-all">

                <label for="add_data_comment">Комментарий:</label>
                <input type="text" name="add_data_comment" id="add_data_comment" class="text ui-widget-content ui-corner-all">

                Показывать баннер? : <input type="checkbox" name="add_data_is_visible" id="add_data_is_visible" style="display: inline">

            </fieldset>
        </form>
    </div>
    <div id="edit_form" title="Изменить запись в таблице баннеров">
        <form action="?action=update">
            <fieldset>
                <label for="edit_data_url_href">Ссылка на сайт, предоставляющий баннер:</label>
                <input type="text" name="edit_data_url_href" id="edit_data_url_href" class="text ui-widget-content ui-corner-all">

                <label for="edit_data_url_image">Ссылка на изображение: </label>
                <input type="text" name="edit_data_url_image" id="edit_data_url_image" class="text ui-widget-content ui-corner-all">

                <label for="edit_data_alt">Альтернативный текст (ALT-атрибут): </label>
                <input type="text" name="edit_data_alt" id="edit_data_alt" class="text ui-widget-content ui-corner-all">

                <label for="edit_data_comment">Комментарий: </label>
                <input type="text" name="edit_data_comment" id="edit_data_comment" class="text ui-widget-content ui-corner-all">

                Показывать баннер? : <input type="checkbox" name="edit_data_is_visible" id="edit_data_is_visible" style="display: inline">

            </fieldset>
        </form>
    </div>
    <hr>
    <fieldset class="result-list">
        <div id="ref_list">
        </div>
    </fieldset>

    </body>
    </html>
    <?php
        break;
    }

} //switch

print($return);
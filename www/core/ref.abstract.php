<?php
require_once('core.php');
require_once('core.db.php');

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) header('Location: /core/');

/*
Abstract reference module

Abstact table: {
id PK AI
data_int default 0
data_str default `NULL`
data_comment varchar 64
}

*/
require_once('core.db.php');

$reference = isset($_GET['ref']) ? $_GET['ref'] : ''; // вообще то если ref не задано - работать не с чем
$return = '';

if (isset($_GET['ref'])) {
    $action = isset($_GET['action']) ? $_GET['action'] : 'no-action';
    $link = ConnectDB();

    switch ($action) {
        case 'get-comments': // get comments
        {
            $result = array();
            $q = "SELECT column_name, column_comment FROM information_schema.COLUMNS
WHERE TABLE_NAME = '{$reference}'";
            $r = mysql_query($q);
            $rn = @mysql_num_rows($r);
            if ($rn > 0) {
                while ($row = mysql_fetch_assoc($r)) {
                    $result['data'][ $row['column_name'] ] = $row['column_comment'];
                }
                $result['state'] = 'ok';
            } else {
                $result['state'] = 'error';
            }
            $return = json_encode($result);
            break;
        } // case 'get-comments'
        case 'insert':
        {
            $q = array(
                'data_int' => mysql_escape_string($_GET['data_int']),
                'data_str' => mysql_escape_string($_GET['data_str']),
                'data_comment' => mysql_escape_string($_GET['data_comment']),
            );
            $qstr = MakeInsert($q, $reference);
            $res = mysql_query($qstr, $link) or Die("Unable to insert data to DB!".$qstr);
            $new_id = mysql_insert_id() or Die("Unable to get last insert id! Last request is [$qstr]");

            $result['message'] = $qstr;
            $result['error'] = 0;
            $return = json_encode($result);
            break;
        } // case 'insert'
        case 'update':
        {
            $id = $_GET['id'];
            $q = array(
                'data_int' => mysql_escape_string($_GET['data_int']),
                'data_str' => mysql_escape_string($_GET['data_str']),
                'data_comment' => mysql_escape_string($_GET['data_comment']),
            );

            $qstr = MakeUpdate($q, $reference, "WHERE id=$id");
            $res = mysql_query($qstr, $link) or Die("Unable update data : ".$qstr);

            $result['message'] = $qstr;
            $result['error'] = 0;
            $return = json_encode($result);
            break;
        } // case 'update
        case 'remove':
        {
            $id = $_GET['id'];
            $q = "DELETE FROM $reference WHERE (id=$id)";
            if ($r = mysql_query($q)) {
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
        {
            $id = $_GET['id'];
            $query = "SELECT * FROM $reference WHERE id=$id";
            $res = mysql_query($query) or die("Невозможно получить содержимое справочника! ".$query);
            $ref_numrows = mysql_num_rows($res);

            if ($ref_numrows != 0) {
                $result['data'] = mysql_fetch_assoc($res);
                $result['error'] = 0;
                $result['message'] = '';
            } else {
                $result['error'] = 1;
                $result['message'] = 'Ошибка базы данных!';
            }
            $return = json_encode($result);
            break;
        } // case 'load'
        case 'advlist':
        {
            $fields = array();
            $rows = array();
            $r_fields = mysql_query("SELECT column_name, column_comment FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$reference}'");
            while ($a_field = mysql_fetch_assoc($r_fields)) {
                $fields [ $a_field['column_name'] ] = $a_field['column_comment'];
            }
            $fields['control'] = 'control';
            foreach ($fields as $f_index=>$f_content ) {
                $rows[0][ $f_index ] = ($f_content != '') ? $f_content : $f_index;
            }

            $return = '';
            $r_data = mysql_query("SELECT * FROM $reference");


            if (@mysql_num_rows($r_data) > 0)
            {
                while ($ref_record = mysql_fetch_assoc($r_data))
                {
                    $ref_record['control'] = <<<xxx
<button class="action-edit button-edit" name="{$ref_record['id']}">Edit</button>
xxx;
                    $rows[] = $ref_record;
                }
            }
            // printr($rows);die;
            // визуализация
            $return .= <<<ADV_TABLE_START
<table border="1" width="100%">
ADV_TABLE_START;
            if (count($rows) > 1) {
                foreach ($rows as $n => $row)
                {
                    $td_start = ($n == 0) ? '<th>' : "<td>\r\n";
                    $td_end = ($n == 0) ? '</th>' : "</td>\r\n";
                    $return .= "<tr>\r\n";

                    foreach ($rows [ $n ] as $r_content) {
                        $return .= <<<ADV_TABLE_TR
    {$td_start} {$r_content} &nbsp; {$td_end}
ADV_TABLE_TR;
                    }

                    $return .= "</tr>\r\n";
                }
            } else {
                $return .= '<tr><td colspan="' . count($rows[0]) . '">Справочник пуст!</td></tr>';
            }

            $return .= "</table>\r\n";
            break;
        }
        case 'list':
        {
            $query = "SELECT * FROM $reference";
            $res = mysql_query($query) or die("mysql_query_error: ".$query);

            $ref_numrows = @mysql_num_rows($res) ;
            $return = <<<TABLE_START
<table border="1" width="100%">
    <tr>
        <th width="5%">(id)</th>
        <th width="10%">Числовые данные</th>
        <th>Строковые данные</th>
        <th>Комментарий</th>
        <th width="7%">Управление</th>
    </tr>
TABLE_START;
            if ($ref_numrows > 0) {
                while ($ref_record = mysql_fetch_assoc($res))
                {
                    $return.= <<<TABLE_EACHROW
<tr>
    <td>{$ref_record['id']}</td>
    <td>{$ref_record['data_int']}&nbsp;</td>
    <td>{$ref_record['data_str']}&nbsp;</td>
    <td>{$ref_record['data_comment']}&nbsp;</td>
    <td class="centred_cell"><button class="action-edit button-edit" name="{$ref_record['id']}">Edit</button></td>
</tr>
TABLE_EACHROW;
                }
            } else {
                $return .= <<<TABLE_IS_EMPTY
<tr><td colspan="5">Справочник пуст!</td></tr>
TABLE_IS_EMPTY;
            }
            break;
        } // case 'list'
        case 'no-action': {
            ?>
        <html>
        <head>
        <title>Работа с абстрактным справочником [<?php echo $reference ?>]</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="js/jquery-ui-1.10.3.custom.min.js"></script>
        <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.10.3.custom.min.css">

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

        function Abstract_ReloadContent(target)
        {
            $(target).empty().load("?action=advlist&ref="+ref_name);
        }

        function Abstract_LoadFieldComments(reference)
        {
            var ret = {};
            $.get('?action=get-comments&ref='+reference).done(function(data){
                var result = $.parseJSON(data);
                if (result['state'] != 'error') {
                    ret = result['data'];
                }
            });

            return ret;
        }

        function ShowErrorMessage(message)
        {
            alert(message);
        }

        function Abstract_CallAddItem(source, id)
        {
            var $form = $(source).find('form');
            url = $form.attr("action");
            var getting = $.get(url, {
                data_int: $form.find("input[name='add_data_int']").val(),
                data_str: $form.find("input[name='add_data_str']").val(),
                data_comment: $form.find("input[name='add_data_comment']").val(),
                ref: ref_name
            } );
            getting.done(function(data){
                result = $.parseJSON(data);
                if (result['error']==0) {
                    Abstract_ReloadContent("#ref_list");
                    // $("#ref_list").empty().load("?action=list&ref="+ref_name);
                    $( source ).dialog( "close" );
                } else {
                    $( source ).dialog( "close" );
                }
            });
        }
        function Abstract_CallUpdateItem(source, id)
        {
            var $form = $(source).find('form');
            var getting = $.get($form.attr("action"), {
                data_int: $form.find("input[name='edit_data_int']").val(),
                data_str: $form.find("input[name='edit_data_str']").val(),
                data_comment: $form.find("input[name='edit_data_comment']").val(),
                ref: ref_name,
                id: id
            } );
            getting.done(function(data){
                result = $.parseJSON(data);
                if (result['error']==0) {
                    // $("#ref_list").empty().load("?action=list&ref="+ref_name);
                    Abstract_ReloadContent("#ref_list");
                    $( source ).dialog( "close" );
                } else {
                    $( source ).dialog( "close" ); // Some errors, show message!
                }
            });


        }
        function Abstract_CallRemoveItem(target, id)
        {
            var getting = $.get('?action=remove', {
                ref: ref_name,
                id: id
            });
            getting.done(function(data){
                result = $.parseJSON(data);
                if (result['error'] == 0) {
                    // $('#ref_list').empty().load("?action=list&ref="+ref_name);
                    Abstract_ReloadContent("#ref_list");
                    $( target ).dialog( "close" );
                } else {
                    ShowErrorMessage(result['message']);
                    $( target ).dialog( "close" );
                }
            });

        }
        function Abstract_CallLoadItem(target, id)
        {
            var getting = $.get('?action=load', {
                id: id,
                ref: ref_name
            });
            var $form = $(target).find('form');
            getting.done(function(data){
                var result = $.parseJSON(data);
                if (result['error'] == 0) {
                    $form.find("input[name='edit_data_int']").val( result['data']['data_int'] );
                    $form.find("input[name='edit_data_str']").val( result['data']['data_str'] );
                    $form.find("input[name='edit_data_comment']").val( result['data']['data_comment'] );
                } else {
                    // ошибка загрузки
                }
            });
        }
        function Abstract_SetFieldLabels(data)
        {
            if (typeof data !== 'undefined') {
                if ( data['data_int'] != '') {
                    $("#add_ref_data_int_label").html( data['data_int'] );
                    $("#edit_ref_data_int_label").html( data['data_int'] );
                }
                if ( data['data_str'] != '') {
                    $("#add_ref_data_str_label").html( data['data_str'] );
                    $("#edit_ref_data_str_label").html( data['data_str'] );
                }
            }
        }

        $(document).ready(function () {
            $.ajaxSetup({cache: false, async: false });
            // var ref_field_comments = Abstract_LoadFieldComments();
            Abstract_SetFieldLabels( Abstract_LoadFieldComments(ref_name) );

            // $("#ref_list").load("?action=list&ref="+ref_name);
            Abstract_ReloadContent("#ref_list");

            /* вызов и обработчик диалога ADD-ITEM */
            $("#actor-add").on('click',function() {
                $('#add_form').dialog('open');
            });

            $( "#add_form" ).dialog({
                autoOpen: false,
                height: 300,
                width: 500,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Добавить",
                        click: function() {
                            Abstract_CallAddItem(this);
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

            $('#ref_list').on('click', '.action-edit', function() {
                button_id = $(this).attr('name');

                Abstract_CallLoadItem("#edit_form", button_id);
                $('#edit_form').dialog('open');
            });
            $( "#edit_form" ).dialog({
                autoOpen: false,
                height: 300,
                width: 500,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Принять и обновить данные",
                        click: function() {
                            Abstract_CallUpdateItem(this, button_id);
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    },
                    {
                        text: "Удалить значение из базы",
                        click: function() {
                            Abstract_CallRemoveItem(this, button_id);
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
        <button type="button" class="button-large" id="actor-add"><strong>Добавить запись в справочник</strong></button><br>

        <div id="add_form" title="Добавить запись в справочник [<?php echo $reference ?>]">
            <form action="?action=insert">
                <fieldset>
                    <label for="add_data_str" id="add_ref_data_str_label">Строковое поле:</label>
                    <input type="text" name="add_data_str" id="add_data_str" class="text ui-widget-content ui-corner-all">

                    <label for="add_data_int" id="add_ref_data_int_label">Числовое поле: </label>
                    <input type="text" name="add_data_int" id="add_data_int" class="text ui-widget-content ui-corner-all">

                    <label for="add_data_comment">Комментарий:</label>
                    <input type="text" name="add_data_comment" id="add_data_comment" class="text ui-widget-content ui-corner-all">
                </fieldset>
            </form>
        </div>
        <div id="edit_form" title="Изменить запись в справочнике [<?php echo $reference ?>]">
            <form action="?action=update">
                <fieldset>
                    <label for="edit_data_str" id="edit_ref_data_str_label">Строковое поле:</label>
                    <input type="text" name="edit_data_str" id="edit_data_str" class="text ui-widget-content ui-corner-all">

                    <label for="edit_data_int" id="edit_ref_data_int_label">Числовое поле: </label>
                    <input type="text" name="edit_data_int" id="edit_data_int" class="text ui-widget-content ui-corner-all">

                    <label for="edit_data_comment">Комментарий:</label>
                    <input type="text" name="edit_data_comment" id="edit_data_comment" class="text ui-widget-content ui-corner-all">
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
    CloseDB($link);

    print($return);
} else {
    Die('При вызове не указан идентификатор справочника! Работать не с чем! ');
}
?>
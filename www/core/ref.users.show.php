<?php
require_once('core.php');
require_once('core.db.php');
require_once('core.kwt.php');

$SID = session_id();
if(empty($SID)) session_start();
ifNotLoggedRedirect('/core/');

?>
<html>
<head>
    <title>Справочник: Список пользователей</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/jquery-ui-1.10.3.custom.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/core.admin.css">

    <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.10.3.custom.min.css">
    <link rel="stylesheet" type="text/css" href="css/core.ui.css">

    <script src="js/core.js"></script>
    <script src="core.users/users.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            $("#ref_list").load("core.users/users.action.list.php?ref="+ref_name);

            /* вызов и обработчик диалога ADD-ITEM */
            $("#actor-add").on('click',function() {
                $('#add-form').dialog('open');
            });
            $( "#add-form" ).dialog({
                autoOpen: false,
                height: 500,
                width: 500,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Добавить пользователя",
                        click: function() {
                            Users_CallAddItem(this);
                            $(this).find('form').trigger('reset');
                            // логика добавления
                            $( this ).dialog( "close" );
                        }
                    },
                    {
                        text: "Отмена",
                        click: function() {
                            $(this).find('form').trigger('reset');
                            // просто отмена
                            $( this ).dialog( "close" );
                        }

                    },
                    {
                        text: "Сброс",
                        click: function() {
                            $(this).find('form').trigger('reset');
                        }
                    }
                ],
                close: function() {
                    $(this).find('form').trigger('reset');
                }
            });

            /* вызов и обработчик диалога редактирования */
            $('#ref_list').on('click', '.actor-edit', function() {
                button_id = $(this).attr('name');
                Users_CallLoadItem("#edit_form",button_id);
                $('#edit_form').dialog('open');
            });

            $( "#edit_form" ).dialog({
                autoOpen: false,
                height: 500,
                width: 500,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Принять и обновить данные",
                        click: function() {
                            Users_CallUpdateItem(this, button_id);
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    },
                    {
                        text: "Удалить пользователя из базы",
                        click: function() {
                            // @todo: логика УДАЛЕНИЯ с конфирмом
                            Users_CallRemoveItem(this, button_id);
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
                window.location.href = '/core/';
                return false;
            });

        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="actor-exit"><strong><<< НАЗАД </strong></button>
<button type="button" class="button-large" id="actor-add">Добавить пользователя</button><br>

<div id="add-form" title="Добавить пользователя">
    <form action="core.users/users.action.insert.php">
        <fieldset>
            <label for="add_name">Ф.И.О. (полностью)</label>
            <input type="text" name="add_name" id="add_name" class="text ui-widget-content ui-corner-all">
            <label for="add_email">E-Mail:</label>
            <input type="text" name="add_email" id="add_email" value="" class="text ui-widget-content ui-corner-all">
            <label for="add_phone">Телефон для связи:</label>
            <input type="text" name="add_phone" id="add_phone" value="" class="text ui-widget-content ui-corner-all">
            <label for="add_permissions">Уровень доступа (0-250):</label>
            <input type="text" name="add_permissions" id="add_permissions" value="" class="text ui-widget-content ui-corner-all">
            <label for="add_login">Имя пользователя (login):</label>
            <input type="text" name="add_login" id="add_login" value ="" class="text ui-widget-content ui-corner-all">
            <label for="add_password">Пароль:</label>
            <input type="text" name="add_password" id="add_password" value ="" class="text ui-widget-content ui-corner-all">
        </fieldset>
    </form>
</div>
<div id="edit_form" title="Изменить пользователя">
    <form action="core.users/users.action.update.php">
        <fieldset>
            <label for="edit_name">Ф.И.О. (полностью)</label>
            <input type="text" name="edit_name" id="edit_name" class="text ui-widget-content ui-corner-all">
            <label for="edit_email">E-Mail:</label>
            <input type="text" name="edit_email" id="edit_email" value="" class="text ui-widget-content ui-corner-all">
            <label for="edit_phone">Телефон для связи:</label>
            <input type="text" name="edit_phone" id="edit_phone" value="" class="text ui-widget-content ui-corner-all">
            <label for="edit_permissions">Уровень доступа (0-250)</label>
            <input type="text" name="edit_permissions" id="edit_permissions" value="" class="text ui-widget-content ui-corner-all">
            <label for="edit_login">Имя пользователя (login):</label>
            <input type="text" name="edit_login" id="edit_login" value="" class="text ui-widget-content ui-corner-all">
            <label for="edit_password">Пароль:</label>
            <input type="text" name="edit_password" id="edit_password" value="" class="text ui-widget-content ui-corner-all">
        </fieldset>
    </form>
</div>
<hr>
<fieldset class="result-list table-hl-rows">
    <div id="ref_list">
    </div>
</fieldset>

</body>
</html>
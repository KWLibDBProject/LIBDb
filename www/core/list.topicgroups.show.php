<?php
define('__ACCESS_MODE__', 'admin');
require_once '__required.php'; // $mysqli_link

?>
<html>
<head>
    <title>Справочник: Тематические разделы</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="_assets/jquery-1.10.2.min.js"></script>
    <script src="_assets/jquery-ui-1.10.3.custom.min.js"></script>
    <link rel="stylesheet" type="text/css" href="_assets/jquery-ui-1.10.3.custom.min.css">
    <link rel="stylesheet" type="text/css" href="_assets/core.ui.css">

    <link rel="stylesheet" type="text/css" href="_assets/core.admin.css">

    <script src="core.topicgroups/topicgroups.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            $("#tg_list").load("core.topicgroups/topicgroups.action.list.php");

            /* вызов и обработчик диалога ADD-ITEM */
            $("#actor-add").on('click',function() {
                $('#add_form').dialog('open');
            });

            $( "#add_form" ).dialog({
                autoOpen: false,
                height: 500,
                width: 600,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Добавить",
                        click: function() {
                            Topicgroups_CallAddItem(this, "#tg_list");
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

            $('#tg_list').on('click', '.action-edit', function() {
                button_id = $(this).attr('name');
                Topicgroups_CallLoadItem("#edit_form",button_id);
                $('#edit_form').dialog('open');
            });

            $( "#edit_form" ).dialog({
                autoOpen: false,
                height: 500,
                width: 600,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Принять и обновить данные",
                        click: function() {
                            Topicgroups_CallUpdateItem(this, button_id, "#tg_list");
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    },
                    {
                        text: "Удалить группу",
                        click: function() {
                            Topicgroups_CallRemoveItem(this, button_id, "#tg_list");
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
            });

        });
    </script>
</head>
<body>
<button type="button" id="actor-exit" class="button-large"><strong><<< НАЗАД </strong></button>
<button type="button" id="actor-add"  class="button-large">Добавить группу тематических разделов</button><br>

<div id="add_form" title="Добавить группу тематических разделов">
    <form action="core.topicgroups/topicgroups.action.insert.php">
        <fieldset>
            <label for="add_title_en">Английское название:</label>
            <input type="text" name="add_title_en" id="add_title_en" class="text ui-widget-content ui-corner-all">
            <label for="add_title_ru">Русское название:</label>
            <input type="text" name="add_title_ru" id="add_title_ru" class="text ui-widget-content ui-corner-all">
            <label for="add_title_ua">Украинское название:</label>
            <input type="text" name="add_title_ua" id="add_title_ua" class="text ui-widget-content ui-corner-all">
            <label for="add_display_order">Display order <br>(меньше - раньше)</label>
            <input type="text" name="add_display_order" id="add_display_order" class="text ui-widget-content ui-corner-all">
        </fieldset>
        Пожалуйста, вводите всё маленькими (строчными) буквами.
    </form>
</div>
<div id="edit_form" title="Изменить группу тематических разделов">
    <form action="core.topicgroups/topicgroups.action.update.php">
        <fieldset>
            <label for="edit_title_en">Английское название:</label>
            <input type="text" name="edit_title_en" id="edit_title_en" class="text ui-widget-content ui-corner-all">
            <label for="edit_title_ru">Русское название:</label>
            <input type="text" name="edit_title_ru" id="edit_title_ru" class="text ui-widget-content ui-corner-all">
            <label for="edit_title_ua">Украинское название:</label>
            <input type="text" name="edit_title_ua" id="edit_title_ua" class="text ui-widget-content ui-corner-all">
            <label for="edit_display_order">Display order <br>(меньше - раньше)</label>
            <input type="text" name="edit_display_order" id="edit_display_order" class="text ui-widget-content ui-corner-all">
        </fieldset>
        Пожалуйста, всё маленькими (строчными) буквами.
     </form>
</div>

<hr>
<fieldset class="result-list table-hl-rows">
    <div id="tg_list" class="list-limited-height">
    </div>
</fieldset>

</body>
</html>
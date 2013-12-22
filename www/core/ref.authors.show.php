<?php
// calls with parameter 'authors'
require_once('core.php');
require_once('db.php');

$ref_prompt = 'Добавление автора';

//@todo: отбор списка авторов, валидация данных:
// по хорошему, отбор листинга нужен по каким-то критериям из выпадающего списка
// в AddItem и UpdateItem ввести валидацию данных (как минимум емейла) как в http://jqueryui.com/dialog/#modal-form
?>
<html>
<head>
    <title>Справочник: Список авторов</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/jquery-ui-1.10.3.custom.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/ref.main.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.10.3.custom.min.css">
    <link rel="stylesheet" type="text/css" href="css/ref.ui.css">

    <script type="text/javascript">
        var ref_name = "authors";
        var button_id = 0;
        $(document).ready(function () {
            function CallAddItem(source)
            {
                var $form = $(source).find('form');
                url = $form.attr("action");
                var posting = $.post(url, {
                    name_rus: $form.find("input[name='add_name_rus']").val(),
                    name_eng: $form.find("input[name='add_name_eng']").val(),
                    name_ukr: $form.find("input[name='add_name_ukr']").val(),
                    title_eng : $form.find("input[name='add_title_eng']").val(),
                    title_rus : $form.find("input[name='add_title_rus']").val(),
                    title_ukr : $form.find("input[name='add_title_ukr']").val(),
                    workplace: $form.find("textarea[name='add_workplace']").val(),
                    ref_name: ref_name,
                    email: $form.find("input[name='add_email']").val()
                } );
                posting.done(function(data){

                    result = $.parseJSON(data);
                    if (result['error']==0) { // update list
                        $("#ref_list").empty().load("ref_authors/ref.authors.action.list.php?ref="+ref_name);
                        $( source ).dialog( "close" );
                    } else {
                        // Some errors, show message!
                        $( source ).dialog( "close" );
                    }
                });
            }
            function CallLoadItem(destination, id) // номер записи, целевая форма
            {
                url = 'ref_authors/ref.authors.action.getitem.php';
                var getting = $.get(url, {
                    id: id,
                    ref: ref_name
                });

                var $form = $(destination).find('form');

                getting.done(function(data){
                    result = $.parseJSON(data);
                    if (result['error'] == 0) {
                        // загружаем данные в поля формы
                        $form.find("input[name='edit_name_rus']").val( result['data']['name_rus'] );
                        $form.find("input[name='edit_name_eng']").val( result['data']['name_eng'] );
                        $form.find("input[name='edit_name_ukr']").val( result['data']['name_ukr'] );
                        $form.find("input[name='edit_title_rus']").val( result['data']['title_rus'] );
                        $form.find("input[name='edit_title_eng']").val( result['data']['title_eng'] );
                        $form.find("input[name='edit_title_ukr']").val( result['data']['title_ukr'] );
                        $form.find("input[name='edit_email']").val( result['data']['email'] );
                        $form.find("textarea[name='edit_workplace']").val(result['data']['workplace']);
                    } else {
                        // ошибка загрузки
                    }
                });
            }

            function CallUpdateItem(source, id)
            {
                var $form = $(source).find('form');
                url = $form.attr("action");
                var posting = $.post(url, {
                    name_rus: $form.find("input[name='edit_name_rus']").val(),
                    name_eng: $form.find("input[name='edit_name_eng']").val(),
                    name_ukr: $form.find("input[name='edit_name_ukr']").val(),
                    title_eng : $form.find("input[name='edit_title_eng']").val(),
                    title_rus : $form.find("input[name='edit_title_rus']").val(),
                    title_ukr : $form.find("input[name='edit_title_ukr']").val(),
                    workplace: $form.find("textarea[name='edit_workplace']").val(),
                    ref_name: ref_name,
                    email: $form.find("input[name='edit_email']").val(),
                    id: id
                } );
                posting.done(function(data){
                    result = $.parseJSON(data);
                    if (result['error']==0) { // update list
                        $("#ref_list").empty().load("ref_authors/ref.authors.action.list.php?ref="+ref_name);
                        $( source ).dialog( "close" );
                    } else {
                        // Some errors, show message!
                        $( source ).dialog( "close" );
                    }
                });
            }
            function CallRemoveItem(target, id)
            {
                url = 'ref_authors/ref.authors.action.removeitem.php?ref='+ref_name;
                var getting = $.get(url, {
                    ref_name: ref_name,
                    id: id
                });
                getting.done(function(data){
                    result = $.parseJSON(data);
                    if (result['error'] == 0) {
                        $('#ref_list').empty().load("ref_authors/ref.authors.action.list.php?ref="+ref_name);
                        $( target ).dialog( "close" );
                    } else {
                        $( target ).dialog( "close" );
                    }
                });

            }

            $("#ref_list").load("ref_authors/ref.authors.action.list.php?ref="+ref_name);

            /* вызов и обработчик диалога ADD-ITEM */
            $("#add_item").on('click',function() {
                $('#add_form').dialog('open');
            });
            $( "#add_form" ).dialog({
                autoOpen: false,
                height: 600,
                width: 500,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Добавить автора",
                        click: function() {
                            CallAddItem(this);
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
            $('#ref_list').on('click', '.edit_button', function() {
                button_id = $(this).attr('name');
                CallLoadItem("#edit_form",button_id);
                $('#edit_form').dialog('open');
            });

            $( "#edit_form" ).dialog({
                autoOpen: false,
                height: 600,
                width: 500,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Принять и обновить данные",
                        click: function() {
                            CallUpdateItem(this, button_id);
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    },
                    {
                        text: "Удалить автора из базы",
                        click: function() {
                            //@todo: логика УДАЛЕНИЯ с конфирмом

                            CallRemoveItem(this, button_id);
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    }
                ],
                close: function() {
                    $(this).find('form').trigger('reset');
                }
            });
            $("#button-exit").on('click',function(event){
                event.preventDefault();
                window.location.href = 'admin.html';
            });

        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="button-exit"><strong>ВЕРНУТЬСЯ В АДМИНКУ</strong></button>
<div id="add_form" title="Добавить автора">
    <form action="ref_authors/ref.authors.action.add.php">
        <fieldset>
            <label for="add_name_rus">Ф.И.О. (русский)</label>
            <input type="text" name="add_name_rus" id="add_name_rus" class="text ui-widget-content ui-corner-all">
            <label for="add_name_eng">Ф.И.О. (английский)</label>
            <input type="text" name="add_name_eng" id="add_name_eng" class="text ui-widget-content ui-corner-all">
            <label for="add_name_ukr">Ф.И.О. (украинский)</label>
            <input type="text" name="add_name_ukr" id="add_name_ukr" class="text ui-widget-content ui-corner-all">
            <label for="add_email">Email</label>
            <input type="text" name="add_email" id="add_email" value="" class="text ui-widget-content ui-corner-all">
            <label for="add_title_eng">Title (eng)</label>
            <input type="text" name="add_title_eng" id="add_title_eng" value="" class="text ui-widget-content ui-corner-all">
            <label for="add_title_rus">Титул (рус)</label>
            <input type="text" name="add_title_rus" id="add_title_rus" value="" class="text ui-widget-content ui-corner-all">
            <label for="add_title_ukr">Титул (укр)</label>
            <input type="text" name="add_title_ukr" id="add_title_ukr" value="" class="text ui-widget-content ui-corner-all">
            <label for="add_workplace">Место работы</label>
            <textarea name="add_workplace" id="add_workplace" class="text ui-widget-content ui-corner-all" cols="50" rows="5"></textarea>
        </fieldset>
    </form>
</div>
<div id="edit_form" title="Добавить автора">
    <form action="ref_authors/ref.authors.action.update.php">
        <fieldset>
            <label for="edit_name_rus">Ф.И.О. (русский)</label>
            <input type="text" name="edit_name_rus" id="edit_name_rus" class="text ui-widget-content ui-corner-all">
            <label for="edit_name_eng">Ф.И.О. (английский)</label>
            <input type="text" name="edit_name_eng" id="edit_name_eng" class="text ui-widget-content ui-corner-all">
            <label for="edit_name_ukr">Ф.И.О. (украинский)</label>
            <input type="text" name="edit_name_ukr" id="edit_name_ukr" class="text ui-widget-content ui-corner-all">
            <label for="edit_email">Email</label>
            <input type="text" name="edit_email" id="edit_email" value="" class="text ui-widget-content ui-corner-all">
            <label for="edit_title_eng">Title (eng)</label>
            <input type="text" name="edit_title_eng" id="edit_title_eng" value="" class="text ui-widget-content ui-corner-all">
            <label for="edit_title_rus">Титул (рус)</label>
            <input type="text" name="edit_title_rus" id="edit_title_rus" value="" class="text ui-widget-content ui-corner-all">
            <label for="edit_title_ukr">Титул (укр)</label>
            <input type="text" name="edit_title_ukr" id="edit_title_ukr" value="" class="text ui-widget-content ui-corner-all">
            <label for="edit_workplace">Место работы</label>
            <textarea name="edit_workplace" id="edit_workplace" class="text ui-widget-content ui-corner-all" cols="50" rows="5"></textarea>
        </fieldset>
    </form>
</div>



<button id="add_item"  class="button-large">Добавить автора</button><br>

<hr>
<div id="ref_list">
</div>

</body>
</html>

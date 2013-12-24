<html>
<head>
    <title>Справочник: Тематические разделы</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/jquery-ui-1.10.3.custom.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/ref.main.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.10.3.custom.min.css">
    <link rel="stylesheet" href="css/ref.ui.css">


    <script src="js/core.js"></script>
    <script src="ref_topics/ref.topics.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            $("#ref_list").load("ref_topics/ref.topics.action.list.php?ref="+ref_name);

            /* вызов и обработчик диалога ADD-ITEM */
            $("#add_item").on('click',function() {
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
                            Topics_CallAddItem(this);
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

            $('#ref_list').on('click', '.edit_button', function() {
                button_id = $(this).attr('name');
                Topics_CallLoadItem("#edit_form",button_id);
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
                            Topics_CallUpdateItem(this, button_id);
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    },
                    {
                        text: "Удалить топик из базы",
                        click: function() {
                            Topics_CallRemoveItem(this, button_id);
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
                window.location.href = 'admin.html';
            });

        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="button-exit"><strong>ВЕРНУТЬСЯ В АДМИНКУ</strong></button>
<button id="add_item"  class="button-large">Добавить тему (топик)</button><br>

<div id="add_form" title="Добавить тематический раздел">
    <form action="ref_topics/ref.topics.action.insert.php">
        <fieldset>
            <label for="add_title">Название:</label>
            <input type="text" name="add_title" id="add_title" class="text ui-widget-content ui-corner-all">
            <label for="add_shortname">Алиас (короткое имя):</label>
            <input type="text" class="text ui-widget-content ui-corner-all" id="add_shortname" name="add_shortname">
        </fieldset>
    </form>
</div>
<div id="edit_form" title="Изменить тематический раздел">
    <form action="ref_topics/ref.topics.action.update.php">
        <fieldset>
            <label for="edit_title">Название:</label>
            <input type="text" name="edit_title" id="edit_title" class="text ui-widget-content ui-corner-all">
            <label for="edit_shortname">Алиас (короткое имя):</label>
            <input type="text" class="text ui-widget-content ui-corner-all" id="edit_shortname" name="edit_shortname">
        </fieldset>
     </form>
</div>



<hr>
<div id="ref_list">
</div>

</body>
</html>

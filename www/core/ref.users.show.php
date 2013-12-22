<html>
<head>
    <title>Справочник: Список пользователей</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/jquery-ui-1.10.3.custom.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/ref.main.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.10.3.custom.min.css">
    <link rel="stylesheet" href="css/ref.ui.css">

    <script src="js/core.js"></script>
    <script src="ref_users/ref.users.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#ref_list").load("ref_users/ref.users.action.list.php?ref="+ref_name);

            /* вызов и обработчик диалога ADD-ITEM */
            $("#add_item").on('click',function() {
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
            $('#ref_list').on('click', '.edit_button', function() {
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
            $("#button-exit").on('click',function(event){
                window.location.href = 'admin.html';
                return false;
            });

        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="button-exit"><strong>ВЕРНУТЬСЯ В АДМИНКУ</strong></button>
<button id="add_item" class="button-large">Добавить пользователя</button><br>

<div id="add-form" title="Добавить пользователя">
    <form action="ref_users/ref.users.action.insert.php">
        <fieldset>
            <label for="add_name">Ф.И.О. (полностью)</label>
            <input type="text" name="add_name" id="add_name" class="text ui-widget-content ui-corner-all">
            <label for="add_email">Email</label>
            <input type="text" name="add_email" id="add_email" value="" class="text ui-widget-content ui-corner-all">
            <label for="add_permissions">Права</label>
            <input type="text" name="add_permissions" id="add_permissions" value="" class="text ui-widget-content ui-corner-all">
            <label for="add_login">Имя пользователя</label>
            <input type="text" name="add_login" id="add_login" value ="" class="text ui-widget-content ui-corner-all">
            <label for="add_password">Пароль</label>
            <input type="text" name="add_password" id="add_password" value ="" class="text ui-widget-content ui-corner-all">
        </fieldset>
    </form>
</div>
<div id="edit_form" title="Изменить пользователя">
    <form action="ref_users/ref.users.action.update.php">
        <fieldset>
            <label for="edit_name">Ф.И.О. (полностью)</label>
            <input type="text" name="edit_name" id="edit_name" class="text ui-widget-content ui-corner-all">
            <label for="edit_email">Email</label>
            <input type="text" name="edit_email" id="edit_email" value="" class="text ui-widget-content ui-corner-all">
            <label for="edit_permissions">Права</label>
            <input type="text" name="edit_permissions" id="edit_permissions" value="" class="text ui-widget-content ui-corner-all">
            <label for="edit_login">Имя пользователя</label>
            <input type="text" name="edit_login" id="edit_login" value="" class="text ui-widget-content ui-corner-all">
            <label for="edit_password">Пароль</label>
            <input type="text" name="edit_password" id="edit_password" value="" class="text ui-widget-content ui-corner-all">
        </fieldset>
    </form>
</div>
<hr>
<div id="ref_list">
</div>

</body>
</html>
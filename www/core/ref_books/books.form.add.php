<html>
<head>
    <title>Сборники -- добавление</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="../js/jquery-1.10.2.min.js"></script>
    <script src="../js/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="../js/jquery.ui.datepicker.rus.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui-1.10.3.custom.min.css">

    <link rel="stylesheet" type="text/css" href="/core/css/core.admin.css">
    <link rel="stylesheet" type="text/css" href="books.css">

    <script src="../js/core.js"></script>

    <script type="text/javascript">
    $(document).ready(function () {
        $("#button-exit").on('click',function(event){
            window.location.href = '../ref.books.show.php';
        });
        $("#button-remove").on('click',function(event){
            // window.location.href = 'books.action.remove.php?id='+author_id;
            alert('false');
        });
        $("#form_book").submit(function(){
            var bValid = true;
            if ($('input[name="file_cover"]').val() == '') {
                alert('Обязательно укажите файл с обложкой (изображение в формате JPG/GIF/PNG) ! ');
                bValid = false;
            }
            if (!strpos($('input[name="file_title"]').val() , '.pdf')) {
                alert('Файл с титульным листом должен быть в формате PDF! ');
                bValid = false;
            }
            if (!strpos($('input[name="file_toc"]').val() , '.pdf')) {
                alert('Файл с оглавлением должен быть в формате PDF! ');
                bValid = false;
            }
            return bValid;
        });
        // WIDGETS
        $("#book_datepicker").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd/mm/yy',
            minDate: '01/01/2003',
            maxDate: '01/01/2020',
            showButtonPanel: true
            // showOn: "both",
            // buttonImageOnly: true,
            // buttonImage: "../css/images/calendar.gif"
        });
        $("#book_title").focus();

    });
    </script>
</head>
<body>

<form action="books.action.insert.php" method="post" enctype="multipart/form-data" id="form_book">
    <input type="hidden" name="MAX_FILE_SIZE" value="30000000">
    <fieldset class="fields_area rounded">
        <legend>Данные о сборнике</legend>
        <div class="field">
            <label for="book_title">Название:</label>
            <input type="text" name="book_title" id="book_title">
        </div>
        <div class="field">
            <label for="book_datepicker">Дата (год) выпуска:</label>
            <input type="text" class="book_datepicker" id="book_datepicker" name="book_date">
        </div>
        <div class="field">
            <label for="book_contentpages">Страницы со статьями:</label>
            <input type="text" name="book_contentpages" id="book_contentpages">
        </div>
        <div class="field">
            <label for="is_book_ready">
                Выпущен ли сборник:
            </label>
            <select name="is_book_ready" id="is_book_ready"><option value="0">Нет (в работе)</option><option value="1">Да (опубликован)</option></select>
        </div>
    </fieldset>

    <fieldset class="fields_area rounded">
        <legend>Файлы</legend>
        <div class="field">
            <label for="file_cover">Обложка (изображение)</label>
            <input type="file" name="file_cover" id="file_cover" size="40">
            <button class="file-unlink" name="file_cover" disabled>X</button>
        </div>
        <div class="field">
            <label for="file_title">Титульный лист (PDF-file)</label>
            <input type="file" name="file_title" id="file_title" size="40">
            <button class="file-unlink" name="file_title" disabled>X</button>
        </div>
        <div class="field">
            <label for="file_toc">Оглавление (PDF-file)</label>
            <input type="file" name="file_toc" id="file_toc" size="40">
            <button class="file-unlink" name="file_toc" disabled>X</button>
        </div>
    </fieldset>
    <fieldset class="fields_area rounded">
        <legend>Управление</legend>
        <button type="button" class="button-large" id="button-exit"><strong>ВЕРНУТЬСЯ К СПИСКУ СБОРНИКОВ</strong></button>
        <button disabled type="button" class="button-large" id="button-remove"><strong>УДАЛИТЬ СБОРНИК</strong></button>
        <button type="submit" class="button-large" ><strong>ДОБАВИТЬ СБОРНИК</strong></button>
    </fieldset>
</form>

</body>
</html>
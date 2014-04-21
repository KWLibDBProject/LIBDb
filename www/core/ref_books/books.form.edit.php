<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.filestorage.php');

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) header('Location: /core/');

$id = IsSet($_GET['id']) ? $_GET['id'] : -1;

if ($id != -1)
{
    $link = ConnectDB();
    $q = "select * from books where id=$id";
    $r = mysql_query($q) or die("Death at : $q");

    if (@mysql_num_rows($r) > 0) {
        $book = mysql_fetch_assoc($r);

        // теперь надо загрузить информацию о файлах!
        // file_cover
        if ($book['file_cover'] != -1 )
        {
            $f = FileStorage::getFileInfo($book['file_cover']);

            $book['file_cover_data']['id'] = $f['id'];
            $book['file_cover_data']['username'] = $f['username'];
            $book['file_cover_data']['disabled_flag'] = '';
        } else {
            $book['file_cover_data']['id'] = -1;
            $book['file_cover_data']['username'] = 'Файл еще указан!!! Нажмите `удалить` и загрузите файл!';
            $book['file_cover_data']['disabled_flag'] = 'disabled';
        }
        // file_title
        if ($book['file_title'] != -1 )
        {
            $f = FileStorage::getFileInfo($book['file_title']);

            $book['file_title_data']['id'] = $f['id'];
            $book['file_title_data']['username'] = $f['username'];
            $book['file_title_data']['disabled_flag'] = '';
        } else {
            $book['file_title_data']['id'] = -1;
            $book['file_title_data']['username'] = 'Файл еще указан!!! Нажмите `удалить` и загрузите файл!';
            $book['file_title_data']['disabled_flag'] = 'disabled';
        }
        // file_toc
        if ($book['file_toc'] != -1 )
        {
            $f = FileStorage::getFileInfo($book['file_toc']);

            $book['file_toc_data']['id'] = $f['id'];
            $book['file_toc_data']['username'] = $f['username'];
            $book['file_toc_data']['disabled_flag'] = '';
        } else {
            $book['file_toc_data']['id'] = -1;
            $book['file_toc_data']['username'] = 'Файл еще указан!!! Нажмите `удалить` и загрузите файл!';
            $book['file_toc_data']['disabled_flag'] = 'disabled';
        }
        // file_toc_en
        if ($book['file_toc_en'] != -1 )
        {
            $f = FileStorage::getFileInfo($book['file_toc_en']);

            $book['file_toc_en_data']['id'] = $f['id'];
            $book['file_toc_en_data']['username'] = $f['username'];
            $book['file_toc_en_data']['disabled_flag'] = '';
        } else {
            $book['file_toc_en_data']['id'] = -1;
            $book['file_toc_en_data']['username'] = 'Файл еще указан!!! Нажмите `удалить` и загрузите файл!';
            $book['file_toc_en_data']['disabled_flag'] = 'disabled';
        }
        $isBookExists = 1;
    } else {
        $isBookExists = 0;
        $book['published'] = 0;
    }
    CloseDb($link);
} else {
    Die('Некорректный вызов! ');
}
?>
<html>
<head>
    <title>Сборники -- редактирование</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="../js/jquery-1.10.2.min.js"></script>
    <script src="../js/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="../js/jquery.ui.datepicker.rus.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/jquery-ui-1.10.3.custom.min.css">

    <script src="../js/jquery.colorbox.js"></script>
    <link rel="stylesheet" href="../css/colorbox.css" />

    <link rel="stylesheet" type="text/css" href="books.css">
    <link rel="stylesheet" type="text/css" href="/core/css/core.admin.css">

    <script src="../js/core.js"></script>

    <script type="text/javascript">
        var we_can_delete_file = false;
        var isBookExists = <?php echo $isBookExists ?>;
        var is_published = {
            'error' : 0,
            'data' : {
                '0' : 'Нет (в работе)',
                '1' : 'Да (опубликован)'
            }
        };
        function ShowErrorMessage(message)
        {
            alert(message);
        }

        $(document).ready(function () {
            if (0 == isBookExists) {
                $('#form_book').hide();
                $('#no_book_warning').show();
            } else {
                BuildSelector('is_book_ready', is_published, <?php echo $book['published'] ?>);
            }

            $(".button-exit").on('click',function(event){
                window.location.href = '../ref.books.show.php';
            });
            $("#button-remove").on('click',function(event){
                if (confirm("Вы уверены, что хотите удалить сборник?")) {
                    window.location.href = 'books.action.remove.php?id='+<? echo $id ?>;
                }

            });

            $("#form_book").submit(function(){
                var bValid = true;
                if (($('input[name=file_cover_changed]').val() == 1) && ($('input[name="file_cover"]').val() == '')) {
                    ShowErrorMessage('Обязательно укажите файл с обложкой (изображение в формате JPG/GIF/PNG) ! ');
                    bValid = false;
                }
                if (($('input[name=file_title_changed]').val() == 1) &&  !strpos($('input[name="file_title"]').val() , '.pdf')) {
                    ShowErrorMessage('Файл с титульным листом должен быть в формате PDF! ');
                    bValid = false;
                }
                if (($('input[name=file_toc_changed]').val() == 1) &&  !strpos($('input[name="file_toc"]').val() , '.pdf')) {
                    ShowErrorMessage('Файл с оглавлением должен быть в формате PDF! ');
                    bValid = false;
                }
                if (($('input[name=file_toc_en_changed]').val() == 1) &&  !strpos($('input[name="file_toc_en"]').val() , '.pdf')) {
                    ShowErrorMessage('Файл с английским оглавлением должен быть в формате PDF! ');
                    bValid = false;
                }
                return bValid;
            });
            // WIDGETS
            $("#book_datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd.mm.yy',
                minDate: '01.01.2003',
                maxDate: '01.01.2020',
                showButtonPanel: true
            });
            $("#book_title").focus();


            $(".current_file_show").on('click', function(){
                window.location.href="../getfile.php?id="+$(this).attr('data-fileid');
            });
            $(".current_file_lightbox").on('click', function(){
                var link = "../getimage.php?id="+$(this).attr('data-fileid');
                $.colorbox({
                    href: link,
                    photo: true
                });
            });

            $(".current_file_remove").on('click', function(){
                // запрос на удаление делаем ТОЛЬКО для тех полей, в которых файл вставлен
                // (то есть атрибут 'disabled' кнопки "посмотреть" не установлен (точнее typeof атрибута === undefined)
                if ( (typeof $(this).siblings('.current_file_show').attr('disabled')) === 'undefined' )
                {
                    we_can_delete_file = confirm(' Действительно удалить файл '+$(this).siblings('input').val() + ' ? ');
                } else { we_can_delete_file = true;  }

                if (we_can_delete_file) {
                    var div_id = $(this).attr('data-name');
                    var getting = $.get('../core.filestorage/filestorage.action.remove.php', {
                        id: $(this).attr('data-fileid'),
                        caller: 'books',
                        subcaller: div_id
                    });
                    getting.done(function(data){
                        result = $.parseJSON(data);
                        if (result['error'] == 0)
                        {
                            $('#'+div_id+"_newfile_input").removeProp("disabled");
                            $('#'+div_id+'_new').show().find("input[name="+div_id+"_changed]").attr("value","1");
                            $('#'+div_id+'_old').hide();
                        } else {
                            // alert('Ошибка удаления файла!');
                        }
                    }); // getting.done
                }
            });
        });
    </script>
    <style>
        #no_book_warning {
            font-weight: bold;
            font-size: 150%;
            color: red;
        }
    </style>
</head>
<body>
<div id="no_book_warning" class="hidden">
    СБОРНИК С УКАЗАННЫМ ИДЕНТИФИКАТОРОМ В БАЗЕ НЕ ОБНАРУЖЕН!!!
    <button type="button" class="button-large button-exit"><strong>ВЕРНУТЬСЯ К СПИСКУ СБОРНИКОВ</strong></button>
</div>

<form action="books.action.update.php" method="post" enctype="multipart/form-data" id="form_book">
    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
    <input type="hidden" name="MAX_FILE_SIZE" value="30000000">
    <fieldset class="fields_area rounded">
        <legend>Данные о сборнике</legend>
        <div class="field">
            <label for="book_title">Название:</label>
            <input type="text" name="book_title" id="book_title" value="<?php echo $book['title']?>">
        </div>
        <div class="field">
            <label for="book_datepicker">Дата (год) выпуска:</label>
            <input type="text" class="book_datepicker" id="book_datepicker" name="book_date" value="<?php echo $book['date']?>">
        </div>
        <div class="field">
            <label for="book_contentpages">Страницы со статьями:</label>
            <input type="text" name="book_contentpages" id="book_contentpages" value="<?php echo $book['contentpages']?>">
        </div>
        <div class="field">
            <label for="is_book_ready">
                Выпущен ли сборник:
            </label>
            <select name="is_book_ready" id="is_book_ready"></select>
        </div>
    </fieldset>
    <div class="clear"></div>

    <fieldset>
        <legend>Файл обложки</legend>
        <div id="file_cover_old">
            <button type="button" class="current_file_lightbox" data-fileid="<?php echo $book['file_cover_data']['id'];?>" <?echo $book['file_cover_data']['disabled_flag']?>>Посмотреть</button>
            <label for="file_cover_old_text">Текущий файл</label>
            <input type="text" size="60" id="file_cover_old_text" value="<?php echo $book['file_cover_data']['username']?>">
            <button type="button" data-name="file_cover" class="current_file_remove" data-fileid="<?php echo $book['file_cover_data']['id'];?>">Удалить</button>
        </div>
        <div id="file_cover_new" class="hidden">
            <label for="file_cover_newfile_input">Прикрепить НОВЫЙ файл (JPEG/PNG/GIF):</label>
            <input type="file" name="file_cover" id="file_cover_newfile_input" size="60" disabled>
            <input type="hidden" name="file_cover_changed" id="file_cover_changed" value="0">
        </div>
    </fieldset>

    <fieldset>
        <legend>Файл титульного листа</legend>
        <div id="file_title_old">
            <button type="button" class="current_file_show" data-fileid="<?php echo $book['file_title_data']['id'];?>" <?echo $book['file_title_data']['disabled_flag']?>>Посмотреть</button>
            <label for="file_title_old_text">Текущий файл</label>
            <input type="text" size="60" id="file_title_old_text" value="<?php echo $book['file_title_data']['username']?>">
            <button type="button" data-name="file_title" class="current_file_remove" data-fileid="<?php echo $book['file_title_data']['id'];?>">Удалить</button>
        </div>
        <div id="file_title_new" class="hidden">
            <label for="file_title_newfile_input">Прикрепить НОВЫЙ PDF-файл:</label>
            <input type="file" name="file_title" id="file_title_newfile_input" size="60" disabled>
            <input type="hidden" name="file_title_changed" id="file_title_changed" value="0">
        </div>
    </fieldset>

    <fieldset>
        <legend>Файл оглавления</legend>
        <div id="file_toc_old">
            <button type="button" class="current_file_show" data-fileid="<?php echo $book['file_toc_data']['id'];?>" <?echo $book['file_toc_data']['disabled_flag']?>>Посмотреть</button>
            <label for="file_toc_old_text">Текущий файл</label>
            <input type="text" size="60" id="file_toc_old_text" value="<?php echo $book['file_toc_data']['username']?>">
            <button type="button" data-name="file_toc" class="current_file_remove" data-fileid="<?php echo $book['file_toc_data']['id'];?>">Удалить</button>
        </div>
        <div id="file_toc_new" class="hidden">
            <label for="file_toc_newfile_input">Прикрепить НОВЫЙ PDF-файл:</label>
            <input type="file" name="file_toc" id="file_toc_newfile_input" size="60" disabled>
            <input type="hidden" name="file_toc_changed" id="file_toc_changed" value="0">
        </div>
    </fieldset>

    <fieldset>
        <legend>Файл английского оглавления</legend>
        <div id="file_toc_en_old">
            <button type="button" class="current_file_show" data-fileid="<?php echo $book['file_toc_en_data']['id'];?>" <?echo $book['file_toc_en_data']['disabled_flag']?>>Посмотреть</button>
            <label for="file_toc_en_old_text">Текущий файл</label>
            <input type="text" size="60" id="file_toc_en_old_text" value="<?php echo $book['file_toc_en_data']['username']?>">
            <button type="button" data-name="file_toc_en" class="current_file_remove" data-fileid="<?php echo $book['file_toc_en_data']['id'];?>">Удалить</button>
        </div>
        <div id="file_toc_en_new" class="hidden">
            <label for="file_toc_en_newfile_input">Прикрепить НОВЫЙ PDF-файл:</label>
            <input type="file" name="file_toc_en" id="file_toc_en_newfile_input" size="60" disabled>
            <input type="hidden" name="file_toc_en_changed" id="file_toc_en_changed" value="0">
        </div>
    </fieldset>

    <div class="clear"></div>

    <fieldset class="fields_area rounded">
        <legend>Управление</legend>
        <button type="button" class="button-large button-exit"><strong>ВЕРНУТЬСЯ К СПИСКУ СБОРНИКОВ</strong></button>
        <button type="button" class="button-large" id="button-remove"><strong>УДАЛИТЬ СБОРНИК</strong></button>
        <button type="submit" class="button-large" ><strong>ОБНОВИТЬ СБОРНИК</strong></button>
    </fieldset>
</form>

</body>
</html>
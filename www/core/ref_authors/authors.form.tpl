<html>
<head>
    <title>{%page_title%}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="../js/jquery-1.10.2.min.js"></script>
    <script src="../js/tinymce.min.js"></script>

    <link rel="stylesheet" type="text/css" href="/core/css/core.admin.css">
    <link rel="stylesheet" type="text/css" href="authors.css">
    <style type="text/css"></style>

    <script src="../js/core.js"></script>
    <script src="ref.authors.js"></script>

    <script src="../js/jquery.colorbox.js"></script>
    <link rel="stylesheet" href="../css/colorbox.css" />

    <script type="text/javascript">
        // tinyMCE inits
        $(document).ready(function () {
            $.ajaxSetup({cache: false, async: false});

            selfhoodList = preloadOptionsList('../ref.abstract.getoptionslist.php?ref=ref_selfhood');
            currentSelfhood = 0;
            author_id = {%author_id%};

            if (author_id != -1) {
                currentSelfhood = Authors_LoadRecord("#form_edit_author", author_id, 'bio');
                $(".actor-button-remove").show().on('click',function(event){
                    window.location.href = 'authors.action.remove.php?id='+author_id;
                });
            } else {
                $("select[name='selfhood']").prop('disabled', true); // значение селекта 'selfhood' для нового автора
            }
            $("#form_edit_author").show();
            /* флажки установим (полностью отвязывая блок с файлами от оверрайда шаблона
            для ADD (author_id == -1) - true, EDIT: false */
            $("button[name='file_current_id_show']").prop('disabled', (author_id == -1) );


            //@hint: создать селектор 'selfhood' (мы создаем его, лишь узнав текущее значение)
            BuildSelector('selfhood', selfhoodList, currentSelfhood);

            $(".actor-button-exit").on('click',function(event){
                window.location.href = '/core/ref.authors.show.php';
            });

            $("#is_es").on('change', function(event){
                $("select[name='selfhood']").prop('disabled', !this.checked);
                // Участник редколлегии КАК МИНИМУМ тех-редактор, сменим значение селектора 'selfhood'
                //@hint: +this.checked преобразует bool -> int
                $("select[name='selfhood'] option[value='"+ +this.checked +"']").prop("selected",true);
            });

            /* photo actors */
            $(".actor-file-current-remove").on('click', function(){
                var getting = $.get('../ref.filestorage/filestorage.action.remove.php', {
                    id: $(this).attr('data-fileid'), // тут почему то Undefined
                    caller: 'authors',
                    subcaller: 'photo_id'
                });
                getting.done(function(data){
                    result = $.parseJSON(data);
                    if (result['error'] == 0)
                    {
                        $('#file_new_input').removeProp("disabled");
                        $('#file_new').show().find("input[name=file_current_changed]").attr("value","1");
                        $('#file_current').hide();
                    } else {
                        // alert('Ошибка удаления файла!');
                    }
                }); // getting.done
            });

            $(".actor-file-current-show").on('click', function(){
                var link = "../getimage.php?id="+$(this).attr('data-fileid');
                $.colorbox({
                    href: link,
                    photo: true
                });
            });
            // вставить проверку валидации данных по сабмиту. Надо ли?
            //

            tinymce.init({
                selector:'textarea#bio_en',forced_root_block : "",
                force_br_newlines : true,
                force_p_newlines : false
            });
            tinymce.init({
                selector:'textarea#bio_ru',forced_root_block : "",
                force_br_newlines : true,
                force_p_newlines : false
            });
            tinymce.init({
                selector:'textarea#bio_uk',forced_root_block : "",
                force_br_newlines : true,
                force_p_newlines : false
            });

            $("#name_en").focus();
        });
    </script>
</head>
<body>

<form action="{%form_call_script%}" method="post" enctype="multipart/form-data" id="form_edit_author" class="hidden">
    <button type="button" class="button-large actor-button-exit" id="button-exit"><strong>ВЕРНУТЬСЯ К СПИСКУ АВТОРОВ</strong></button>
    <button type="button" class="button-large actor-button-remove" id="button-remove"><strong>УДАЛИТЬ АВТОРА</strong></button>
    <button type="submit" class="button-large" ><strong>{%submit_button_text%}</strong></button>
    <hr>
    <input type="hidden" name="id">
    <fieldset>
        <label for="name_en">Name, surname</label><br>
        <input type="text" name="name_en" id="name_en" size="40" value="">
        <br>

        <label for="name_ru">Ф.И.О. (русский)</label><br>
        <input type="text" name="name_ru" id="name_ru" size="40" value="">
        <br>

        <label for="name_uk">Ф.И.О. (украинский)</label><br>
        <input type="text" name="name_uk" id="name_uk" size="40" value="">
    </fieldset>
    <fieldset>
        <label for="title_en">Звание, ученая степень, должность (на английском)</label><br>
        <input type="text" name="title_en" id="title_en" size="40" value="">
        <br>

        <label for="title_ru">Звание, ученая степень, должность</label><br>
        <input type="text" name="title_ru" id="title_ru" size="40" value="">
        <br>

        <label for="title_uk">Званна, вчена ступiнь, посада</label><br>
        <input type="text" name="title_uk" id="title_uk" size="40" value="">

    </fieldset>
    <fieldset>
        <legend>Контактные данные:</legend>

        <label for="email">E-Mail</label><br>
        <input type="text" name="email" id="email" value="">

        <br>

        <label for="phone">Телефон для связи</label><br>
        <input type="text" name="phone" id="phone" value="">

        <br>

        <label for="workplace">Место работы</label><br>
        <textarea name="workplace" id="workplace" cols="90" rows="5"></textarea>
    </fieldset>
    <hr>

    <fieldset id="es_fieldset">
        <legend>
            Расширенные сведения об авторе
        </legend>
        <dl class="h-layout">
            <dt class="w250">Участник редакционной коллегии:</dt>
            <dd><input type="checkbox" name="is_es" id="is_es"></dd>
            <dt class="w250">
                Роль в редакционной коллегии:
            </dt>
            <dd>
                <select name="selfhood">
                    <option value="0">[0] НЕТ</option>
                </select>
            </dd>
        </dl>

        <!-- photo inputs -->
        <div id="file_current">
            <label for="file_current_input">
                <button type="button" class="actor-file-current-show lightboxed" name="file_current_id_show">Фотография</button>
            </label>
            <input type="text" size="60" id="file_current_input" name="file_current_input" value="Нажмите *удалить* и добавьте фотографию автора">
            <button type="button" name="file_current_id_remove" class="actor-file-current-remove">Удалить</button>
        </div>

        <div id="file_new" class="hidden">
            <label for="file_new_input">Прикрепить НОВЫЙ файл (JPEG/PNG/GIF):</label>
            <input type="file" name="file_new_input" id="file_new_input" size="60" disabled>
            <input type="hidden" name="file_current_changed" id="file_current_changed" value="0">
            <div class="hint"></div>
        </div>
        <br>
        <!-- bio -->
        <h2>Биография на различных языках</h2>

        <h3>Биография на английском языке</h3>
        <textarea name="bio_en" id="bio_en" cols="90" rows="7"></textarea>

        <h3>Биография на русском языке</h3>
        <textarea name="bio_ru" id="bio_ru" cols="90" rows="7"></textarea>

        <h3>Биография на украинском языке</h3>
        <textarea name="bio_uk" id="bio_uk" cols="90" rows="7"></textarea>

    </fieldset>
    <hr>
    <button type="submit" class="button-large" ><strong>{%submit_button_text%}</strong></button>
</form>

</body>
</html>

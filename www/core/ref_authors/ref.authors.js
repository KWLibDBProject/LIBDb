var ref_name = 'authors';
/* возвращает текущее значение "самости" для нужного автора */
function Authors_LoadRecord(destination, id, tinyarea_target) // номер записи, целевая форма
{
    var innerSelfhood = 0;
    var url = 'authors.action.getitem.php';
    var getting = $.get(url, {
        id: id,
        ref: ref_name
    });
    var $form = $(destination);
    getting.done(function(data){
        result = $.parseJSON(data);
        if (result['error'] == 0) {
            innerSelfhood = result['data']['selfhood'];
            // загружаем данные в поля формы
            $form.find("input[name='id']").val( result['data']['id'] );
            $form.find("input[name='name_ru']").val( result['data']['name_ru'] );
            $form.find("input[name='name_en']").val( result['data']['name_en'] );
            $form.find("input[name='name_uk']").val( result['data']['name_uk'] );
            $form.find("input[name='title_ru']").val( result['data']['title_ru'] );
            $form.find("input[name='title_en']").val( result['data']['title_en'] );
            $form.find("input[name='title_uk']").val( result['data']['title_uk'] );
            $form.find("input[name='email']").val( result['data']['email'] );
            $form.find("input[name='phone']").val( result['data']['phone'] );
            $form.find("textarea[name='workplace']").val(result['data']['workplace']);
            /*
            * $result['data']['is_es'] - это строка. Её надо преобразовать к числу, а потом
            * применять к нему логические преобразования для установки чекбоксов. Если parseInt()
            * не сделать - строка '0' (не участник редколлегии) интерпретируется все равно как TRUE!
            * старая версия - это (result['data']['is_es'] != 0),
            * что быстрее - сравнение строки или parseInt() - тема для анализа :)
            * // $form.find("input[name='is_es']").prop("checked", !!(result['data']['is_es'] != 0));
            * // $form.find("input[name='is_es']").prop("checked", !!parseInt(result['data']['is_es']));
            * */
            //@hint: правильное преобразование строкового значения "ложь" (как 0) в число/флаг
            $form.find("input[name='is_es']").prop("checked", !!parseInt(result['data']['is_es']));
            // Если is_es установлен - надо селекту selfhood задать prop(disabled, false),т.е. задать инвертированное значение чекбокса!
            $form.find("select[name='selfhood']").attr('disabled', !parseInt(result['data']['is_es']));

            // установить значение атрибута "selfhood'
            /* photo */
            $form.find("input[name='file_current_input']").val( result['data']['photo_username'] ); // ===  value="{%file_current_username%}"
            $form.find("button[name='file_current_id_remove']").attr('data-fileid', result['data']['photo_id'] ); // === data-fileid="{%file_current_id%}"
            $form.find("button[name='file_current_id_show']").attr('data-fileid', result['data']['photo_id'] ); // === data-fileid="{%file_current_id%}"

            if (result['data']['bio'] != '') // prevent auto focus for empty biography field
            {
                tinyMCE.get(tinyarea_target).setContent(result['data']['bio']); // вместо $form.find("textarea[name='bio']").val(result['data']['bio']);
            }

        } else {
            // ошибка загрузки
        }
    });
    return innerSelfhood;
}

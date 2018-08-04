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
        var result = $.parseJSON(data);
        if (result['error'] == 0) {
            innerSelfhood = result['data']['selfhood'];
            // загружаем данные в поля формы
            $form.find("input[name='id']").val( result['data']['id'] );
            $form.find("input[name='name_ru']").val( result['data']['name_ru'] );
            $form.find("input[name='name_en']").val( result['data']['name_en'] );
            $form.find("input[name='name_ua']").val( result['data']['name_ua'] );
            $form.find("input[name='title_ru']").val( result['data']['title_ru'] );
            $form.find("input[name='title_en']").val( result['data']['title_en'] );
            $form.find("input[name='title_ua']").val( result['data']['title_ua'] );
            $form.find("input[name='email']").val( result['data']['email'] );
            $form.find("input[name='phone']").val( result['data']['phone'] );
            $form.find("textarea[name='workplace_en']").val(result['data']['workplace_en']);
            $form.find("textarea[name='workplace_ru']").val(result['data']['workplace_ru']);
            $form.find("textarea[name='workplace_ua']").val(result['data']['workplace_ua']);
            // Если is_es установлен - надо селекту selfhood задать prop(disabled, false),т.е. задать инвертированное значение чекбокса!
            $form.find("input[name='is_es']").prop("checked", !!parseInt(result['data']['is_es']));
            // установить значение атрибута "selfhood'
            $form.find("select[name='selfhood']").attr('disabled', !parseInt(result['data']['is_es']));

            /* photo */
            $form.find("input[name='file_current_input']").val( result['data']['photo_username'] ); // ===  value="{%file_current_username%}"
            $form.find("button[name='file_current_id_remove']").attr('data-fileid', result['data']['photo_id'] ); // === data-fileid="{%file_current_id%}"
            $form.find("button[name='file_current_id_show']").attr('data-fileid', result['data']['photo_id'] ); // === data-fileid="{%file_current_id%}"

            // ВНЕЗАПНО: не работает: tinyMCE.get(tinyarea_target).setContent(result['data']['bio']);
            if (result['data']['bio_en'] != '')
            {
                $form.find("textarea[name='bio_en']").val(result['data']['bio_en']);
            }
            if (result['data']['bio_ru'] != '')
            {
                $form.find("textarea[name='bio_ru']").val(result['data']['bio_ru']);
            }
            if (result['data']['bio'] != '')
            {
                $form.find("textarea[name='bio_ua']").val(result['data']['bio_ua']);
            }
        } else {
            // ошибка загрузки
        }
    });
    return innerSelfhood;
}

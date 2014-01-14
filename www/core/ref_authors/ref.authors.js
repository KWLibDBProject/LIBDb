var ref_name = 'authors';
function Authors_LoadRecord(destination, id, tinyarea_target) // номер записи, целевая форма
{
    url = 'authors.action.getitem.php';
    var getting = $.get(url, {
        id: id,
        ref: ref_name
    });
    var $form = $(destination);
    getting.done(function(data){
        result = $.parseJSON(data);
        if (result['error'] == 0) {
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
            $form.find("input[name='is_es']").prop("checked", !!(result['data']['is_es'] != 0));
            /* Если у нас is_es == 1 - нам надо показать филдсет '' */
            result['data']['is_es'] == 0 ? $("#es_fieldset").hide() : $("#es_fieldset").show();

            tinyMCE.get(tinyarea_target).setContent(result['data']['bio']); // вместо $form.find("textarea[name='bio']").val(result['data']['bio']);

        } else {
            // ошибка загрузки
        }
    });
}

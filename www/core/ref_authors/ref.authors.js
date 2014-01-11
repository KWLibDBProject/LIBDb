var ref_name = 'authors';
function Authors_LoadRecord(destination, id) // номер записи, целевая форма
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
            $form.find("input[name='name_rus']").val( result['data']['name_rus'] );
            $form.find("input[name='name_eng']").val( result['data']['name_eng'] );
            $form.find("input[name='name_ukr']").val( result['data']['name_ukr'] );
            $form.find("input[name='title_rus']").val( result['data']['title_rus'] );
            $form.find("input[name='title_eng']").val( result['data']['title_eng'] );
            $form.find("input[name='title_ukr']").val( result['data']['title_ukr'] );
            $form.find("input[name='email']").val( result['data']['email'] );
            $form.find("input[name='phone']").val( result['data']['phone'] );
            $form.find("textarea[name='workplace']").val(result['data']['workplace']);
            $form.find("input[name='is_es']").prop("checked", !!(result['data']['is_es'] != 0)); // simplified ternar form
        } else {
            // ошибка загрузки
        }
    });
}


/* useless */
var ref_name = 'news';
function News_LoadRecord(destination, id, tt_en, tt_ru, tt_uk) // номер записи, целевая форма
{
    url = 'news.action.getitem.php';
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
            $form.find("input[name='comment']").val( result['data']['comment'] );
            $form.find("input[name='date_add']").val( result['data']['date_add'] );
            $form.find("input[name='title_en']").val( result['data']['title_en'] );
            $form.find("input[name='title_ru']").val( result['data']['title_ru'] );
            $form.find("input[name='title_uk']").val( result['data']['title_uk'] );

            tinyMCE.get(tt_ru).setContent(result['data']['text_ru']);
            tinyMCE.get(tt_uk).setContent(result['data']['text_uk']);
            tinyMCE.get(tt_en).setContent(result['data']['text_en']);
            window.scroll(0,0);
        } else {
            // ошибка загрузки
        }
    });
}

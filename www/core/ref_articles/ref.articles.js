function InsertAuthorSelector(targetdiv,selectorName) // N - идентификатор (номер) селекта
{
    $('<li data-li="'+selectorName+'"><label>Автор </label>' +
        '<select class="an_authors" name="authors['+selectorName+']" data-alselector="'+selectorName+'"></select>' +
        '<input value="X" type="button" class="al-delete" data-al="'+selectorName+'"></li>').appendTo(targetdiv);
    if (authorsList['error'] == 0) {
        $.each(authorsList['data'], function(id, value) {
            $('select[data-alselector='+selectorName+']').append('<option value="'+id+'">'+value+'</option>');
        });
    } else {
        $('select[data-alselector='+selectorName+']').append('<option value="-1">'+authorsList['data']['-1']+'</option>');
    }
}


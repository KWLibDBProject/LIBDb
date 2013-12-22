function preloadOptionsList(url) // Загружает данные (кэширование)
{
    var ret;
    $.ajax({
        url: url,
        async: false,
        cache: false,
        type: 'GET',
        success: function(data){
            ret = $.parseJSON(data);
        }
    });
    return ret;
}

// формирует SELECTOR/OPTIONS list с текущим элементом равным [currentid]
function BuildSelector(target,data,currentid) // currentid is 1 for NEW
{
    $.each(data['data'], function(id, value){
        $("select[name="+target+"]").append('<option value="'+id+'">'+value+'</option>')
    });
    if (data['error'] == 0) {
        currentid = (typeof currentid != 'undefined') ? currentid : 1;
        $("select[name="+target+"] option[value="+ currentid +"]").attr("selected","selected");
    } else {
        $("select[name="+target+"]").attr('disabled','disabled');
    }
}

function InsertAuthorSelector(targetdiv,selectorName) // N - идентификатор (номер) селекта
{
    // was: $('<li data-li="'+selectorName+'"><label>Автор  № '+selectorName+ '</label>' + ...
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

function strpos (haystack, needle, offset) {
    var i = (haystack+'').indexOf(needle, (offset || 0));
    return i === -1 ? false : i;
}
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
        // $("select[name="+target+"] option[value="+ currentid +"]").attr("selected","selected");
        $("select[name="+target+"] option[value="+ currentid +"]").prop("selected",true);
    } else {
        $("select[name="+target+"]").attr('disabled','disabled');
    }
}

function strpos (haystack, needle, offset) {
    var i = (haystack+'').indexOf(needle, (offset || 0));
    return i === -1 ? false : i;
}

// $('#comboBx').append($("<option></option>").attr("value",key).text(value));
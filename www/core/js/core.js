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
// target - ИМЯ селектора
function BuildSelector(target, data, currentid) // currentid is 1 for NEW
{
    if (data['error'] == 0) {
        var _target = "select[name='"+target+"']";
        $.each(data['data'], function(id, value){
            $(_target).append('<option value="'+id+'">'+value+'</option>');
        });
        var _currentid = (typeof currentid != 'undefined') ? currentid : 1;
        $("select[name="+target+"] option[value="+ _currentid +"]").prop("selected",true);
    } else {
        $("select[name="+target+"]").prop('disabled',true);
    }
}

function strpos (haystack, needle, offset) {
    var i = (haystack+'').indexOf(needle, (offset || 0));
    return i === -1 ? false : i;
}

/* Привязывает стили и действия к элементу "scroll to top" */
function bindScrollTopAction(target)
{
    $(target).css('float','right').attr('title', 'Наверх').on('click', function(){
        // window.scroll(0,0);
        $('html, body').animate({scrollTop:0}, 'slow');
        return false;
    });
}
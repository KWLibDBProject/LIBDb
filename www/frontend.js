/* Разделить строку по параметрам © http://a2x.ru/?p=140 */
function getQuery( queryString , limiter)
{
    var vars = queryString.split((limiter || '&')); //делим строку по & - parama1=1
    var arr = new Array();
    for (var i=0; i<vars.length; i++)
    {
        var pair = vars[i].split("="); //делим параметр со значением по =, и пишем в ассоциативный массив arr['param1'] = 1
        arr[pair[0]] = pair[1];
    }
    return arr;
}

function setHashBySelectors()
{
    // see http://stackoverflow.com/a/5340658
    // оптимизация, создавать временный массив не обязательно, можно наращивать выходную строку сразу в
    // цикле перебора селектов. Только не забыть убрать конечный "&".
    // для русских букв возможно потребуется экранирование
    var hashstr = '';
    var arr = {};
    $.each( $(".search_selector") , function(id, data) {
        var val = $(data).val();
        var name = $(data).attr('name');
        if (val != '0')
            hashstr += name + "=" + val + "&";
        // hashstr += encodeURIComponent(key) + "=" + encodeURIComponent(arr[key]) + "&";
    } );
    hashstr = hashstr.substring(0, hashstr.length-1); //chop off last "&"
    window.location.hash = hashstr;
}

function setSelectorsByHash(target)
{
    var sel_name;
    var sel_value;
    var hashes_arr = getQuery((window.location.hash).substr(1));

    $.each( $(target), function(id, data) {
        sel_name = $(data).attr('name'); // selector's name attribute
        sel_value = hashes_arr[sel_name] != 'undefined' ? hashes_arr[sel_name] : 0;
        $(target+"[name="+sel_name+"] option[value="+sel_value+"]").prop("selected",true);
    } );
}

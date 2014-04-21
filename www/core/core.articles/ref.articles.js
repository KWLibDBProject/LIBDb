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

function isArrayUnique(arr)
{
    for (var j = 0, R = 1, J = arr.length - 1; j < J; j++)
        for (var k = j + 1, K = J + 1; k < K; k++) R *= arr[j] - arr[k];
    R = !!R;
    return R;
}

function isArrayUniqueByBK(arr)
{
    for (var i = 0; i < arr.length - 1; i++)
        for (var j = i + 1; j < arr.length; j++)
            if (arr[i]==arr[j])
                return false;
    return true;
}
/**
 * Загружает данные
 *
 * @param url
 * @returns {boolean}
 */
function preloadOptionsList(url)
{
    var ret = false;
    $.ajax({
        url: url,
        async: false,
        cache: false,
        type: 'GET',
        success: function(data){
            ret = $.parseJSON(data);
        }
    });
    // if (ret['error'] != 0 ) { ret = false; }
    return ret;
}

/**
 * формирует SELECTOR/OPTIONS list с текущим элементом равным [currentid]
 * data format:
 * {
 *    state: ok, error: 0,
 *    data:  {
 *      n:  {
 *             type:   group       | option
 *             value:  (useless)   | item id in reference
 *             text:   group title | option text
 *             comment:        comment
 *          }
 *    }
 * }
 *
 * calling params: (target, data, [default_option, [selected_value]] )
 *
 *
 * @param target_name = name нужного селекта
 * @param data                  = json-объект со значениями
 * @param default_option_string = строка с текстом стартовой опции (в самом верху списка)
 * @param value_of_selected_option = [0]   значение (value) у опции, которую мы выбираем после загрузки списка
 * @constructor
 */
function BuildSelectorExtended(target_name, data, default_option_string, value_of_selected_option)
{
    var not_a_first_option_group = 0;
    var ret = '', last_group = '';
    var curr_id = value_of_selected_option || 0;
    var _target = "select[name='" + target_name + "']";
    var dos = (default_option_string == '') ? 'Выбрать!' : default_option_string;

    if (data['error'] == 0) {
        ret = '<option value="0" data-group="*">'+ dos +'</option>';
        console.log(dos);

        $.each(data['data'] , function(id, value){
            /*
            upgrade (идея от 2018-08-08, раннее утро):
            if (typeof value == 'object') {
                блок ниже с проверкой типов
            } elseif (typeof value == 'string' {
                ret+= '<option value="'+value['value']+'" data-group="'+ last_group +'">'+value['text']+'</option>';

                // В общем, аналогично этому блоку в BuildSelectorLegacy

            }
             */

            // insert:

            if (value['type'] == 'group') {
                // add optiongroup
                if (last_group != value['text']) {
                    last_group = value['text'];
                    if (not_a_first_option_group) ret += '</optgroup>';
                    ret += '<optgroup label="'+ value['text'] +'">';
                    not_a_first_option_group++;
                }
            }

            if (value['type'] == 'option') {
                // add option
                ret += '<option value="'+value['value']+'" data-group="'+ last_group +'">'+value['text']+'</option>';
            }
        });
        if (not_a_first_option_group > 0) {
            ret += '</optiongroup>';
        }
        $(_target).empty().append ( ret );
        Selector_SetOption(target_name, curr_id);
    }
    else {
        $("select[name="+target_name+"]").prop('disabled',true);
    }
}

/**
 *
 */
function BuildSelectorEmpty(target_name, default_value_string, default_value)
{
    var _target = "select[name='" + target_name + "']";
    var dos = (default_value_string == '') ? 'Выбрать!' : default_value_string;
    var dv = default_value || 0;
    var ret = '<option value="'+ dv  +'" data-group="*">'+ dos +'</option>';
    $(_target).empty().append ( ret );
}

/*

/**
 *
 * @todo: 2014 год: добавить параметр "форма" в которой ищем значение
 * @param name
 * @param option_value
 * @constructor
 */
function Selector_SetOption(name, option_value)
{
    var cid = option_value || 0;
    $("select[name="+name+"] option[value="+ cid +"]").prop("selected",true);
}

/**
 *
 *
 * @param target          target form (value of ID attr or jquery object)
 * @param selector_name   имя селекта
 * @param value_for_undefined
 * @returns {*}
 */
function getSelectedOptionValue(target, selector_name, value_for_undefined)
{
    var t;
    var vou = value_for_undefined || 0;
    if (typeof target === 'string') {
        t = $("#"+target);
    } else if (typeof target === 'object') {
        t = target;
    } else {
        return false;
    }
    var v = t.find("select[name='"+selector_name+"'] option:selected").val();
    if (typeof v === 'undefined') {
        v = vou
    }
    return v;
}

/**
 *
 * @param target
 * @param selector_name
 * @returns {*}
 */
function getSelectedOptionText(target, selector_name)
{
    var t;
    if (typeof target === 'string') {
        t = $("#"+target);
    } else if (typeof target === 'object') {
        t = target;
    } else {
        return false;
    }
    return t.find("select[name='"+selector_name+"'] option:selected").html();
}

/**
 * формирует SELECTOR/OPTIONS list с текущим элементом равным [currentid]
 *
 *
 * @param target_name - ИМЯ селектора
 * @param data
 * @param default_option_string
 * @param value_of_selected_option
 * @constructor
 */
function BuildSelector(target_name, data, default_option_string, value_of_selected_option) //
{
    var dos = (default_option_string == '') ? 'Выбрать!' : default_option_string;
    var curr_id = value_of_selected_option || 0;
    var _target = "select[name='" + target_name + "']";
    var ret = '';

    if (data['error'] == 0) {
        ret = '<option value="0" data-group="*">'+ dos +'</option>';
        $(_target).empty().append ( ret );

        $.each(data['data'], function(id, value){
            $(_target).append('<option value="'+id+'">'+value+'</option>');
        });

        if (typeof value_of_selected_option != 'undefined') {
            Selector_SetOption(target_name, curr_id);
        }
    } else {
    }

    $("select[name="+target_name+"]").prop('disabled',!(data['error']==0));
}

/**
 *
 * @param target_name
 * @constructor
 */
function DisableSelectorByName(target_name) {
    $("select[name="+target_name+"]").prop('disabled', true);
}

/**
 *
 * @param target_name
 * @constructor
 */
function EnableSelectorByName(target_name) {
    $("select[name="+target_name+"]").prop('disabled', false);
}


/**
 * формирует SELECTOR/OPTIONS list с текущим элементом равным [currentid]
 * target - ИМЯ селектора
 *
 * USELESS
 *
 * @param target
 * @param data
 * @param currentid
 * @constructor
 */
function BuildSelector__OLD(target, data, currentid) // currentid is 1 for NEW
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


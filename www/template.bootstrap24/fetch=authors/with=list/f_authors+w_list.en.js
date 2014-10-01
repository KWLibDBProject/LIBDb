function calledOnSwitchLanguage()
{
    window.location.hash = '';
}


var siteLanguage = '&lang=en';
// load data
var lettersList = preloadOptionsList('ajax.php?actor=get_letters_as_optionlist'+siteLanguage);

BuildSelector('letter', lettersList, 0);

url_base = "ajax.php?actor=load_authors_selected_by_letter"+siteLanguage;
url_all = "ajax.php?actor=load_authors_selected_by_letter&letter=0"+siteLanguage;

$("#output_list").empty().load(url_all);

$("#button-show-withselection").on('click',function(){
    query = "&";
    query+="letter="+$('select[name="letter"]').val();
    $("#output_list").empty().load(url_base+query);
});

$("#button-show-all").on('click',function(){
    $('select[name="letter"]').val(0);
    setHashBySelectors(); // сброс хэша!
    $("#output_list").empty().load(url_all);
});
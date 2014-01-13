// load data
var lettersList = preloadOptionsList('core/ajax.frontend.php?actor=load_letters_optionlist');

BuildSelector('select_by_letter', lettersList, 0);

url = "core/ajax.frontend.php?actor=load_authors_selected_by_letter";

$("#output_list").empty().load(url);

$("#button-show-withselection").on('click',function(){
    query = "&";
    query+="letter="+$('select[name="select_by_letter"]').val();
    alert(url+query);
});
$("#button-show-all").on('click',function(){
    $("#output_list").empty().load(url);
});

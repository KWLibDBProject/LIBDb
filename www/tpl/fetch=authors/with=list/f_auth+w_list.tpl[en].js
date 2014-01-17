// load data
var lettersList = preloadOptionsList('ajax.php?actor=get_letters_as_optionlist&lang=en');

BuildSelector('letter', lettersList, 0);

url_q = "ajax.php?actor=load_authors_selected_by_letter&lang=en";
url_s = "ajax.php?actor=load_authors_all&lang=en";

$("#output_list").empty().load(url_s);

$("#button-show-withselection").on('click',function(){
    query = "&";
    query+="letter="+$('select[name="letter"]').val();
    $("#output_list").empty().load(url_q+query);
});

$("#button-show-all").on('click',function(){
    $("#output_list").empty().load(url);
});

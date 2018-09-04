var tiny_config = {
    'full' : {
        theme: "modern",
        language: 'ru',

        forced_root_block : "",
        force_br_newlines : true,
        force_p_newlines : false,

        plugins: [ "advlist lists autolink link image anchor responsivefilemanager charmap insertdatetime paste searchreplace contextmenu code textcolor template hr pagebreak table print preview wordcount visualblocks visualchars" ],
        formats: {
            strikethrough : {inline : 'del'},
            underline : {inline : 'span', 'classes' : 'underline', exact : true}
        },
        // templates: "/core/_assets/tinymce/templates/templates.json",
        insertdatetime_formats: ["%d.%m.%Y", "%H:%m", "%d/%m/%Y"],
        contextmenu: "link image responsivefilemanager | inserttable cell row column deletetable | charmap",
        toolbar1: "pastetext | undo redo | link unlink anchor | forecolor backcolor | styleselect formatselect fontsizeselect | template | print preview code | pastetext removeformat",
        toolbar2: "responsivefilemanager image | bold italic underline subscript superscript strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table inserttime ",
        image_advtab: true, // advanced tab (without rel or class add)
        // responsive filemanager
        relative_urls: false,
        document_base_url: "/filestorage/",
        external_filemanager_path:"/core/_assets/filemanager/",
        filemanager_title:"Responsive Filemanager" ,
        external_plugins: { "filemanager" : "/core/_assets/filemanager/plugin.js"}
    },
    'no-menu' : {
        forced_root_block : "",
        plugins: [ "charmap link paste hr anchor preview print tabfocus table textcolor" ],
        menu: [],
        force_br_newlines : true,
        force_p_newlines : false,
        language: 'ru',
        toolbar: " undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image ",
    },
    'simple' : {
        forced_root_block : "",
        theme: "modern",
        plugins: [ "charmap link paste hr anchor preview print tabfocus table textcolor " ],
        toolbar: " pastetext | undo redo | bold italic underline subscript superscript | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | paste | removeformat",
        force_br_newlines : true,
        force_p_newlines : false
    },
    'simplest' : {
        forced_root_block : "",
        plugins: [ "link paste hr anchor preview print tabfocus table textcolor" ],
        force_br_newlines : true,
        force_p_newlines : false
    },
    'form_core_authors' :{
        theme       : "modern",
        // skin        : "light",
        language    : "ru",

        forced_root_block   : "",
        force_br_newlines   : true,
        force_p_newlines    : false,

        plugins     : [ "advlist lists link anchor charmap paste searchreplace code preview" ],
        formats     : {
            strikethrough : {inline : 'del'},
            underline : {inline : 'span', 'classes' : 'underline', exact : true}
        },
        contextmenu : "link image responsivefilemanager | inserttable cell row column deletetable | charmap",
        // templates   : "/core/_assets/tinymce/templates/templates.json",
        insertdatetime_formats: ["%d.%m.%Y", "%H:%m", "%d/%m/%Y"],
        toolbar1    : "bold italic underline subscript superscript strikethrough | charmap | pastetext | undo redo | link unlink anchor | print preview code | pastetext removeformat",
        image_advtab: true // advanced tab (without rel or class add)
    }
};

/*
* config must be tiny_config['key']
* elem must be html textarea ID ONLY (like 'bio_ru', not 'textarea#bio_ru' !!)
* */
function tinify(config, elem, mode)
{
    m = (typeof mode != 'undefined') ? mode : true;
    tinyMCE.settings = config;
    m ? tinyMCE.execCommand('mceAddEditor', true, elem) : tinyMCE.execCommand('mceRemoveEditor', false, elem);
}
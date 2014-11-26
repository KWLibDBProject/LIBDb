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
        templates: "/core/js/tinymce/templates/templates.json",
        insertdatetime_formats: ["%d.%m.%Y", "%H:%m", "%d/%m/%Y"],
        contextmenu: "link image responsivefilemanager | inserttable cell row column deletetable | charmap",
        toolbar1: "pastetext | undo redo | link unlink anchor | forecolor backcolor | styleselect formatselect fontsizeselect | template | print preview code | pastetext removeformat",
        toolbar2: "responsivefilemanager image | bold italic underline subscript superscript strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table inserttime ",
        image_advtab: true, // advanced tab (without rel or class add)
        // responsive filemanager
        relative_urls: false,
        document_base_url: "/filestorage/",
        external_filemanager_path:"/core/js/filemanager/",
        filemanager_title:"Responsive Filemanager" ,
        external_plugins: { "filemanager" : "/core/js/filemanager/plugin.js"}
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
        plugins: [ "charmap link paste hr anchor preview print tabfocus table textcolor " ],
        toolbar: " pastetext | undo redo | bold italic underline subscript superscript | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | paste | removeformat",
        force_br_newlines : true,
        force_p_newlines : false
    }
};


function tinify(config, elem, mode)
{
    m = (typeof mode != 'undefined') ? mode : true;
    tinyMCE.settings = config;
    m ? tinyMCE.execCommand('mceAddEditor', true, elem) : tinyMCE.execCommand('mceRemoveEditor', false, elem);
}
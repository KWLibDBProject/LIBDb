<?php
require_once '../__required.php'; // $mysqli_link

/* @todo: оптимизировать блок. его как-то можно сократить, ведь неустановенность коллекции === all !*/

// коллекция
$collection = at($_GET, 'collection', 'all');
$collection = getAllowedValue( $collection , array(
    'all', 'articles', 'authors', 'books'
), 'all');

$where =
    ($collection != "all")
    ? " WHERE collection = '{$collection}'"
    : " ";

// порядок сортировки
$sortorder  =
    (isset($_GET['sort-order']) && ($_GET['sort-order'] !='ASC'))
    ? " DESC "
    : " ASC ";

// критерий сортировки
$sort_type = $_GET['sort-type'] ?? 'id';
$sort_type = getAllowedValue( $sort_type, array(
    'id', 'username', 'stat_date_insert', 'filesize', 'relation', 'stat_download_counter'
), '');

$sortby =
    (!empty($sort_type) && $sort_type != 'id')
    ? " ORDER BY {$sort_type} {$sortorder} "
    : ' ';

$fs_table = FileStorage::getStorageTable();

/* move to method */
$q = "
SELECT 
    id, username, internal_name, filesize, relation, collection, filetype, stat_download_counter, stat_date_insert
FROM 
    {$fs_table} 
{$where} 
{$sortby}
";

$r = @mysqli_query($mysqli_link, $q);
/**/

$filestorage_list = [];
if ($r) {
    while ($filestorage_item = mysqli_fetch_assoc($r)) {
        $ext_a = explode('/',$filestorage_item['filetype']);

        switch ($filestorage_item['collection']) {
            case 'books': {
                $qr = mysqli_fetch_assoc(mysqli_query($mysqli_link, "SELECT title FROM books WHERE id = {$filestorage_item['relation']}"));
                $qt = $qr['title'];
                $filestorage_item['external_link'] = "{$qt}";
                $filestorage_item['external_link'] = '→ <a href="/?fetch=articles&with=book&id='.$filestorage_item['relation'].'" target="_blank"> '.$qt.'</a>';
                break;
            }
            case 'articles': {
                $filestorage_item['external_link'] = '<a href="/?fetch=articles&with=info&id='.$filestorage_item['relation'].'" target="_blank"> ==> </a>';
                break;
            }
            case 'authors': {
                $filestorage_item['external_link'] = '<a href="/?fetch=authors&with=info&id='.$filestorage_item['relation'].'" target="_blank"> ==> </a>';
                break;
            }
        }
        $filestorage_item['size'] = ConvertToHumanBytes($filestorage_item['filesize'], 2);
        $filestorage_item['ext'] = $ext_a[1];
        switch ($filestorage_item['ext']) {
            case 'pdf' : {
                $filestorage_item['filelink'] = '<a href="/core/get.file.php?id='.$filestorage_item['id'].'">'.$filestorage_item['username'].'</a>';
                break;
            }
            default: { // image
                $filestorage_item['filelink'] = '<a href="/core/get.image.php?id='.$filestorage_item['id'].'" class="lightbox" target="_blank">'.$filestorage_item['username'].'</a>';
                break;
            }

        }
        $filestorage_item['internal_name_link'] = '<a href="/files/storage/'.$filestorage_item['internal_name'].'" target="_blank">'.$filestorage_item['internal_name'].'</a>';
        $filestorage_item['stat_date_insert'] = date('d/m/Y', strtotime($filestorage_item['stat_date_insert'])); //@todo: проверить формат

        $filestorage_list[ $filestorage_item['id'] ] = $filestorage_item;
    }
}

// template

$template_dir = '$/core/core.filestorage';
$template_file = "_template.filestorage.list.html";

$template_data = array(
    'filestorage_list' =>  $filestorage_list
);

echo websun_parse_template_path($template_data, $template_file, $template_dir);


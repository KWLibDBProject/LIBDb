<?php
require_once '../__required.php'; // $mysqli_link

/* @todo: оптимизировать блок. его как-то можно сократить, ведь неустановенность коллекции === all !*/
// коллекция
$collection =
    (isset($_GET['collection']))
    ? $_GET['collection']
    : "all";
$collection = getAllowedValue( $collection , array(
    'all', 'articles', 'authors', 'books'
));
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
$sort_type = $_GET['sort-type'];
$sort_type = getAllowedValue( $sort_type, array(
    'id', 'username', 'stat_date_insert', 'filesize', 'relation', 'stat_download_counter'
));

$sortby =
    (!empty($sort_type) && $sort_type != 'id')
    ? " ORDER BY {$sort_type} {$sortorder} "
    : ' ';

$fs_table = FileStorageConfig::$config['table'];

$q = "
SELECT id, username, internal_name, filesize, relation, collection, filetype, stat_download_counter, stat_date_insert
FROM {$fs_table} {$where} {$sortby}";

$r = @mysqli_query($mysqli_link, $q);

$fs = array();
if ($r) {
    while ($f = mysqli_fetch_assoc($r)) {
        $ext_a = explode('/',$f['filetype']);

        switch ($f['collection']) {
            case 'books': {
                $qr = mysqli_fetch_assoc(mysqli_query($mysqli_link, "SELECT title FROM books WHERE id = {$f['relation']}"));
                $qt = $qr['title'];
                $f['external_link'] = "{$qt}";
                $f['external_link'] = '→ <a href="/?fetch=articles&with=book&id='.$f['relation'].'" target="_blank"> '.$qt.'</a>';
                break;
            }
            case 'articles': {
                $f['external_link'] = '<a href="/?fetch=articles&with=info&id='.$f['relation'].'" target="_blank"> ==> </a>';
                break;
            }
            case 'authors': {
                $f['external_link'] = '<a href="/?fetch=authors&with=info&id='.$f['relation'].'" target="_blank"> ==> </a>';
                break;
            }
        }
        $f['size'] = ConvertToHumanBytes($f['filesize'], 2);
        $f['ext'] = $ext_a[1];
        switch ($f['ext']) {
            case 'pdf' : {
                $f['filelink'] = '<a href="/core/getfile.php?id='.$f['id'].'">'.$f['username'].'</a>';
                break;
            }
            default: { // image
                $f['filelink'] = '<a class="lightbox" target="_blank" href="/core/getimage.php?id='.$f['id'].'">'.$f['username'].'</a>';
                break;
            }

        }
        $f['internal_name_link'] = '<a href="/files/storage/'.$f['internal_name'].'" target="_blank">'.$f['internal_name'].'</a>';
        $f['stat_date_insert'] = date('d/m/Y', strtotime($f['stat_date_insert']));

        $fs[ $f['id'] ] = $f;
    }
}

?>

<table class="" border="1" width="100%" id="exportable">
    <caption></caption>
    <tr>
        <th>icon</th>
        <th>User filename</th>
        <th>Internal filename</th>
        <th>Date upload</th>
        <th>Size</th>
        <th>Collection</th>
        <th> <img title="Сколько раз скачивали" alt="↓" src="/core/core.filestorage/download.png" /> </th>
        <th>Link</th>
    </tr>

    <?php foreach($fs as $i => $file) {?>
    <tr>
        <td class="center"><?=$file['ext']?></td>
        <td><?=$file['filelink']?></td>
        <td><?=$file['internal_name_link']?></td>
        <td class="center"><?=$file['stat_date_insert']?></td>
        <td class="center"><?=$file['size']?></td>
        <td class="center"><?=$file['collection']?></td>
        <td class="center"><small> <?=$file['stat_download_counter'] ?> </small></td>
        <td class="center"><?=$file['external_link']?></td>
    </tr>
    <?php } ?>
</table>
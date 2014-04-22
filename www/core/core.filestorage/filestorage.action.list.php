<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.filestorage.php');

$link = ConnectDB();

$where = (isset($_GET['collection']) && ($_GET['collection'] !='all')) ? " WHERE collection = '{$_GET['collection']}'" : " ";

$fs_table = FileStorageConfig::$config['table'];

$q = "SELECT id, username, internal_name, filesize, relation, collection, filetype FROM {$fs_table} {$where}";
$r = @mysql_query($q);

$fs = array();
if ($r) {
    while ($f = mysql_fetch_assoc($r)) {
        $ext_a = explode('/',$f['filetype']);

        switch ($f['collection']) {
            case 'books': {
                $qr = mysql_fetch_assoc(mysql_query("SELECT title FROM books WHERE id = {$f['relation']}"));
                $qt = $qr['title'];
                $f['external_link'] = "{$qt}";
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
        $f['size'] = ConvertToHumanBytes($f['filesize']);
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

        $fs[ $f['id'] ] = $f;
    }
}
CloseDB($link);

?>
<style>

</style>
<table class="" border="1" width="100%" id="exportable">
    <caption></caption>
    <tr>
        <th>icon</th>
        <th>User filename</th>
        <th>Internal filename</th>
        <!-- <th>Date upload</th> -->
        <th>Size</th>
        <th>Collection</th>
        <th>Link</th>
    </tr>

    <?php foreach($fs as $i => $file) {?>
    <tr>
        <td class="center"><?=$file['ext']?></td>
        <td><?=$file['filelink']?></td>
        <td><?=$file['internal_name_link']?></td>
        <td class="center"><?=$file['size']?></td>
        <td class="center"><?=$file['collection']?></td>
        <td class="center"><?=$file['external_link']?></td>
    </tr>
    <?php } ?>
</table>
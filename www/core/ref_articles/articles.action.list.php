<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$ref_filestorage = 'filestorage';

/* файл вызывается черех аякс лоадер
Варианты аргументов:

author - показать статьи АВТОРА
topic - показать статьи в топике
book - показать статьи в сборнике
*/

$link = ConnectDB();
/* этот же запрос существует в frontend.php (DB_BuildQuery)*/
$query = "
SELECT DISTINCT articles.id, articles.title_en AS title_en, articles.title_ru AS title_ru, articles.title_uk AS title_uk, udc, pdfid, add_date, pages,
topics.title_ru AS ttitle,
books.title AS btitle
from articles, cross_aa,topics,books
WHERE
cross_aa.article=articles.id
AND
articles.deleted=0
AND
topics.id=articles.topic
AND
books.id=articles.book";

$query .= (IsSet($_GET['author'])   && $_GET['author']!=0)  ? " AND cross_aa.author = $_GET[author] "   : "";
$query .= (IsSet($_GET['book'])     && $_GET['book']!=0 )   ? " AND articles.book = $_GET[book] "       : "";
$query .= (IsSet($_GET['with=topic'])    && $_GET['with=topic'] !=0 ) ? " AND articles.with=topic = $_GET[topic] "     : "";

$query .= " ORDER BY articles.id";

$res = mysql_query($query) or die("Death on : $query");
$articles_count = @mysql_num_rows($res);

if ($articles_count>0) {
    while ($an_article = mysql_fetch_assoc($res))
    {
        $id = $an_article['id']; // айди статьи
        $all_articles[$id] = $an_article; // ВСЯ статья

        // получить информацию о связанной ПДФке
        $qp = "SELECT id, username, filesize FROM $ref_filestorage WHERE relation = $id";
        $rp = mysql_query($qp) or Die("Death on $qp");
        if (@mysql_num_rows($rp) > 0)
        {
            $all_articles[$id]['pdffile'] = mysql_fetch_assoc($rp);
        }

        // получить информацию об авторах
        $r_auths = mysql_query("SELECT authors.name_ru,authors.title_ru,authors.id FROM authors,cross_aa WHERE authors.id=cross_aa.author AND cross_aa.article=$id ORDER BY cross_aa.id");
        $r_auths_count = @mysql_num_rows($r_auths);

        if ($r_auths_count>0)
        {
            // $i=1;
            while ($an_author = mysql_fetch_assoc($r_auths))
            {
                $all_articles[$id]['authors'] .= <<<ArticlesAL_AuthorsEach
<li> <a href="/?fetch=authors&with=info&id={$an_author['id']}&lang=ru" target="_blank">{$an_author['name_ru']}</a> ({$an_author['title_ru']})</li>
ArticlesAL_AuthorsEach;
                // $i++;
            }
            // $all_articles[$id]['authors'] = substr($all_articles[$id]['authors'],0,-4);
        }
    }
}
CloseDB($link);

$return = <<<ArticlesAL_Start
<table border="1" width="100%">
ArticlesAL_Start;

$return .= <<<ArticlesAL_TH
    <tr>
        <th width="3%">id</th>
        <th>Тематический<br> раздел</th>
        <th>Сборник</th>
        <th width="7%">УДК</th>
        <th>Авторы</th>
        <th>Название</th>
        <th width="7%">Дата</th>
        <th width="105" colspan="1">Control<br><small>PDF info</small></th>
    </tr>
ArticlesAL_TH;

if ($articles_count > 0) {
    foreach ($all_articles as $a_id => $a_article)
    {
        $row = $a_article;
        $return .= <<<ArticlesAL_Each
<tr>
<td>{$row['id']}</td>
<td>{$row['ttitle']}</td>
<td><nobr>{$row['btitle']}</nobr></td>
<td>{$row['udc']}</td>
<td>
<ol class="articles-list-table-authors-list">
{$row['authors']}
</ol>
</td>
<td><small>{$row['title_ru']}</small></td>
<td class="center_cell"><small>{$row['add_date']}</small></td>
<td>
    <button class="download-pdf" name="{$row['pdffile']['id']}" data-text="Show PDF"></button>
    <button class="edit_button" name="{$row['id']}" data-text="Edit"></button>
</td>
</tr>
ArticlesAL_Each;
    }
} else {
    $return .= <<<ArticlesAL_Nothing
<tr><td colspan="8">Статей не найдено</td></tr>

ArticlesAL_Nothing;
}

$return .= <<<ArticlesAL_End
</table>
ArticlesAL_End;

print($return);
?>
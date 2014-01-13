<?php
require_once('core.php');
// функции, к которым обращается фронтэнд (сам сайт)

// вот нам и понадобился вложенный шаблон... причем не просто вложенный, а повторяемый, типа
// "повторить следующий блок N раз, заменяя какие-то переменные следующими:"
function DBLoadTopics()
{
    $q = "SELECT * FROM topics WHERE deleted=0";
    $r = mysql_query($q);
    $ret = <<<DBLoadTopicsStart

<ul>

DBLoadTopicsStart;
    while ($topic = mysql_fetch_assoc($r)) {
        $ret.= <<<DBLoadTopicsItem
        <li><a href="?fetch=articles&with=topic&id={$topic['id']}">{$topic['title']}</a></li>
DBLoadTopicsItem;
        // <li><a href="?fetch=articles&with=topic&id={$topic['id']}">{$topic['title']}</a></li>
        // <li><a href="/articles/topic/{$topic['id']}">{$topic['title']}</a></li>
    }
    $ret .= <<<DBLoadTopicsEnd

</ul>

DBLoadTopicsEnd;
    return $ret;
}

function DBLoadBooks()
{
    $yq = "SELECT DISTINCT year FROM books WHERE `published`=1 ORDER BY `year` ";
    $yr = mysql_query($yq);
    $all_books = array();
    while ($ya = mysql_fetch_assoc($yr)) {
        $all_books[ $ya['year'] ] = array();
    }
    foreach ($all_books as $key => $value)
    {
        $bq = "SELECT id, title FROM books WHERE `year`=$key AND `published`=1 ORDER BY `title`";
        $br = mysql_query($bq);
        while ($ba = mysql_fetch_assoc($br)) {
            $all_books[$key][ $ba['id'] ] = $ba['title'];
        }

    }
    $ret = <<<LB_Start
    <ul>
LB_Start;
    foreach ($all_books as $key => $year_books)
    {
        $ret.= <<<LB_Item_Start
    <li>
        <div>$key</div>
        <ul>
LB_Item_Start;
        foreach ($year_books as $id => $book)
        {
            $ret .= <<<LB_Item_Data
            <li><a href="?fetch=articles&with=book&id=$id">$book</a></li>
LB_Item_Data;
        }
$ret.= <<<LB_Item_End
        </ul>
    </li>
LB_Item_End;
    }

    $ret .= <<<LB_End
    </ul>
LB_End;
    return $ret;
}

function DBLoadAuthorInformation($id) // @todo: this
{
    $ret = "Базовая информация об авторе с айди = ".$id;
    return $ret;
}

function DBLoadAuthorPublications($id) //@todo: this
{
    $ret = "Публикации автора с айди = ".$id;
    return $ret;
}

?>
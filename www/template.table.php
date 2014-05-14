<?php
require_once('template.php');
$tpl_path = 'template.table';

class Template extends __Template {

    /* выводит рубрики: принимает язык вывода */
    public function getTopics()
    {
        $data = LoadTopics($this->site_language);
        $ret = '';

        $ret .= <<<FE_PrintTopics_Start
FE_PrintTopics_Start;

        foreach ($data as $id => $title )
        {
            $ret .= <<<FE_PrintTopics_Each
<li><a href="?fetch=articles&with=topic&id={$id}">{$title}</a></li>
FE_PrintTopics_Each;
        }

        $ret .= <<<FE_PrintTopics_End
FE_PrintTopics_End;
        return $ret;
    }

    /* выводит сборники */
    public function getBooks()
    {
        $all_books = LoadBooks();

        $ret = '';
        $ret .= <<<FE_PrintBooks_Start
FE_PrintBooks_Start;

        foreach ($all_books as $key => $year_books)
        {
            $ret .= <<<FE_PrintBooks_ItemStart
<h3 class="books-list-year">{$key}</h3>
<ul>
FE_PrintBooks_ItemStart;

            foreach ($year_books as $id => $book)
            {
                $ret .= <<<FE_PrintBooks_ItemEach
<li class="books-list-eachbook">
    <a href="?fetch=articles&with=book&id={$id}"> {$book['title']}</a>&nbsp;&nbsp;&nbsp;({$book['count']})
</li>

FE_PrintBooks_ItemEach;
            }
            $ret .= <<<FE_PrintBooks_ItemEnd
</ul>
FE_PrintBooks_ItemEnd;
        }
        $ret .= <<<FE_PrintBooks_End
FE_PrintBooks_End;
        ;
        return $ret;
    }

    /* Выводит заголовок сборника на страницу /articles/topic/xx */
    /* для табличного шаблона это "критерии поиска на разных языках*/
    public function getTopicTitle($id)
    {
        $ret = '';
        switch ($this->site_language) {
            case 'en': { $ret = 'Search criteria'; break; }
            case 'ru': { $ret = 'Критерии поиска'; break; }
            case 'uk': { $ret = 'Критерiï пошуку'; break; }
        } // end switch
        return $ret;
    }



}
?>
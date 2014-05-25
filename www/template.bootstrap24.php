<?php
require_once('template.php');
$tpl_path = 'template.bootstrap24';

class Template extends __Template
{
    function getTopics()
    {
        $data = LoadTopics($this->site_language);

        $ret = '';
        foreach ($data as $id => $title )
        {
            $ret .= <<<FE_PrintTopics_Each
<a href="?fetch=articles&with=topic&id={$id}" class="list-group-item">{$title}</a>
FE_PrintTopics_Each;
        }

        return $ret;
    }

    public function getBooks()
    {
        $ret = '';
        $first_in = 'in';
        $all_books = LoadBooks();

        foreach ($all_books as $key => $year_books)
        {
            $ret .= <<<FE_PrintBooksBS_YearStart
                    <div class="panel panel-default">
                        <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" data-target="#collapse{$key}">
                            <h4 class="panel-title">
                                <a class="accordion-toggle">{$key}</a>
                            </h4>
                        </div>
                        <div id="collapse{$key}" class="panel-collapse collapse {$first_in} books">
                            <div class="panel-body">
                                <ul class="etks-books-list">
FE_PrintBooksBS_YearStart;

            foreach ($year_books as $id => $book) {
                $ret .= <<<FE_PrintBooksBS_EachBook
                                    <li><a href="?fetch=articles&with=book&id={$id}"> {$book['title']} </a>({$book['count']})</li>
FE_PrintBooksBS_EachBook;
            }

            $ret .= <<<FE_PrintBooksBS_End
                                </ul>
                            </div>
                        </div>
                    </div>
FE_PrintBooksBS_End;
            $first_in = '';
        }
        return $ret;
    }

    public function getTopicTitle($id)
    {
        $topic = LoadTopicInfo($id, $this->site_language);
        return $topic;
    }



}


?>
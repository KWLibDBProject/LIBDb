<?php
require_once('template.php');
$tpl_path = 'template.bootstrap24';

/**
 * Шаблон bootstrap24 под 24-х колоночную сетку. Наследуется от __Template
 */
class Template extends __Template
{

    /**
     * @return string
     */
    function getTopicsPlain()
    {
        $all_topics = LoadTopics($this->site_language);

        $ret = '';
        foreach ($all_topics as $id => $title )
        {
            $ret .= <<<FE_PrintTopics_Each
<a href="?fetch=articles&with=topic&id={$id}" class="list-group-item">{$title}</a>
FE_PrintTopics_Each;
        }

        return $ret;
    }

    /**
     * @return string
     */
    public function getTopicsTree()
    {
        $all_topics = LoadTopicsTree($this->site_language);

        $ret = '';
        $last_group = '';
        $optgroup_found = 0;

        foreach ($all_topics['data'] as $id => $row) {
            if ($row['type'] == 'group') {

                // add optiongroup
                if ($last_group != $row['value']) {
                    $last_group = $row['value'];
                    if ($optgroup_found) $ret .= '</div></div>';
                    $optgroup_found++;

                    $is_group_expanded = ($optgroup_found == 1) ? ' in ' : '';

                    $ret .= <<<getTT_Group
                    <div class="panel panel-default">
                        <div class="panel-heading" data-toggle="collapse" data-parent="#taccordion" data-target="#topics_{$row['value']}">
                            <h4 class="panel-title">
                                <a class="accordion-toggle">{$row['text']}</a>
                            </h4>
                        </div>
                        <div id="topics_{$row['value']}" class="panel-collapse collapse {$is_group_expanded} list-group etks-topics-list">
getTT_Group;
                }
            }

            if ($row['type'] == 'option') {
                $id = $row['value'];
                $title = $row['text'];

                $ret .= <<<FE_PrintTopics_Each
<a href="?fetch=articles&with=topic&id={$id}" class="list-group-item">{$title}</a>
FE_PrintTopics_Each;

            }

        }

        if ($optgroup_found) $ret .= '</div></div>';

        return $ret;
    }


    /**
     * @return string
     */
    public function getBooks()
    {
        $ret = '';
        $all_books = LoadBooks();

        // этот шаблон надо подключать в основном шаблоне, передавая в 'all_books' результат LoadBooks()
        // ВАЖНО: по аналогии можно написать и TOPICS+TOPIC GROUPS

        $template_dir = '$/template.bootstrap24/_websun_templates';
        $template_file = "frontpage_books_section.html";

        $template_data = array(
            'all_books' =>  $all_books
        );

        $render_result = \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);

        $first_in = 'in';
        /*foreach ($all_books as $key => $year_books)
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

            foreach ($year_books as $id => $book)
            {

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
        }*/
        // return $ret;

        /*printr($all_books);
        printr($render_result);
        die;*/

        return $render_result;
    }

    /**
     * @param $id
     * @return string
     */
    public function getTopicTitle($id)
    {
        $topic = LoadTopicInfo($id, $this->site_language);
        return $topic;
    }



}

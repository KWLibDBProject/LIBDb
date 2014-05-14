<?php
require_once('frontend.php');

class Template
{
    public $template_path = '';
    public $site_language = '';
    public $page_prefix = '';

    public function __construct($path, $site_language)
    {
        $this->template_path = $path;
        $this->site_language = $site_language;
        switch ($site_language) {
            case 'en': { $this->page_prefix = 'Pp. '; break; }
            case 'ru': { $this->page_prefix = 'C. '; break; }
            case 'uk': { $this->page_prefix = 'C. '; break; }
        } // end switch
    }

    /* отображение статической страницы: alias */
    public function getStaticPage($alias)
    {
        $ret = LoadStaticPage($alias, $this->site_language);
        $return = '';
        switch ($ret['state']) {
            case '200': {
                $return = $ret['content'];
                break;
            }
            case '404': {
                $html404 = new kwt($this->template_path.'/page404.html');
                $return = $html404->get();
                break;
            }
        } // switch
        return $return;
    } // end GetStaticPage

    /* оформляет массив баннеров в LI-список (VIEW!) */
    public function getBanners()
    {
        $data = LoadBanners();
        $return = '';
        if (count($data)) {
            foreach ($data as $id=>$row) {
                $return .= <<<EACH_BANNER
                    <li class="banner-item">
                        <a href="{$row['data_url_href']}" target="_blank" class="banner-item-href">
                            <img src="{$row['data_url_image']}" alt="{$row['data_alt']}">
                        </a>
                    </li>
EACH_BANNER;
            }
        } else $return = null;
        return $return;
    } // GetBanners

    /* возвращает длинную строку с новостями --результат подставляется в override-переменную новостного блока справа (под сборниками) */
    public function getLastNews($count)
    {
        $return = '';
        $data = LoadLastNews($this->site_language, $count);

        if (count($data)>0) {
            foreach ($data as $i => $row)
            {
                $date_add = ConvertDateByLang($row['date_add'], $this->site_language);
                $return .= <<<PrintLastNews
                        <li>
                            <strong>{$date_add} </strong>
                            <br>
                            <a href="?fetch=news&with=the&id={$row['id']}">{$row['title']}</a>
                        </li>
PrintLastNews;
            }
        }
        return $return;
    } // GetLastNews

    /* возвращает строку с меню */
    public function getMenu()
    {
        $main_menu = new kwt($this->template_path."/_menu/menu.{$this->site_language}.html");
        return $main_menu->get();
    }

    /* сообщение "нет статей" для разных языков */
    private function messageNoArticles()
    {
        $r = '';
        switch ($this->site_language) {
            case 'en' : {
                $r = '<br><strong>No articles found within this search criteria!</strong>'; break;
            }
            case 'ru' : {
                $r = '<br><strong>По заданным критериям поиска статей не найдено!</strong>'; break;
            }
            case 'uk' : {
                $r = '<br><strong>За заданими критеріями пошуку статей не знайдено!</strong>'; break;
            }
        }
        return $r;
    }

    /* список статей, инфо о каждой статье делается на основе /_internal/item_in_articles_list.html */
    public function getArticlesList($request, $with_email = 'no')
    {
        $articles = LoadArticles_ByQuery($request, $this->site_language);
        $return = '';
        if (count($articles) > 0)
        {
            // печатаем хедер таблицы
            $return .= '<table class="articles-list-by-query" border="1" width="100%">';

            // в цикле загружаем шаблон и передаем в него строки таблицы
            // и результат функции "печать списка авторов на основе $articles['authors'] "

            foreach ($articles as $an_article_id => $an_article) {
                $authors_list = $this->getAuthors_InArticlesList($an_article['authors']);

                $t_a = new kwt($this->template_path.'/_internal/item_in_articles_list.html', '<!--{', '}-->');
                $t_a->override(array(
                    'book_title'        => $an_article['book_title'],
                    'lal_e_bi'          => $this->page_prefix,
                    'article_pages'     => $an_article['article_pages'],
                    'pdfid'             => $an_article['pdfid'],
                    'article_id'        => $an_article['id'],
                    'article_title'     => $an_article['article_title'],
                    'authors_list'      => $authors_list
                ));
                $return .= $t_a->get();
                unset($t_a);
            } // foreach
            // печатаем футер таблицы
            $return .= '</table>';
        } else {
            $return .= $this->messageNoArticles();
        }
        return $return;
    }

    /* список авторов в полной информации о статье! Передаем массив с авторами (который можно загрузить по-разному) */
    public function getAuthors_InArticle($authors, $with_email = '')
    {
        $ret = '';
        // $authors = LoadAuthors_ByArticle($id, $this->site_language);
        foreach ($authors as $author_id => $an_author)
        {
            // Иванов И.И., др.тех.наук
            if (($with_email != '') && ($an_author['author_email'] != '')) {
                $an_author['author_email'] = ' ('.$an_author['author_email'].')';
            } else { $an_author['author_email'] = ''; }

            // выводит каждый элемент по формату шаблона
            $t_a = new kwt($this->template_path.'/_internal/item_in_authors_in_article_info.html', '<!--{', '}-->');
            $t_a->override( array(
                'author_id' => $an_author['author_id'],
                'site_lang' => $this->site_language,
                'author_name' => $an_author['author_name'],
                'author_title' => $an_author['author_title'],
                'author_email' => $an_author['author_email']
            ) );
            $ret .= $t_a->get();
            unset($t_a);
        }
        return $ret;
    }

    /* список авторов в статье (для поля "авторы" в табличном представлении статей, вызывается в getArticlesList )*/
    public function getAuthors_InArticlesList($authors, $with_email = '')
    {
        $ret = '';
        foreach ($authors as $an_author)
        {
            // Иванов И.И., др.тех.наук
            if ($with_email != '') {
                $an_author['author_email'] = ' ('.$an_author['author_email'].')';
            } else { $an_author['author_email'] = ''; }

            // выводит каждый элемент по формату шаблона
            $t_a = new kwt($this->template_path.'/_internal/item_in_authors_in_article_info.html', '<!--{', '}-->');
            $t_a->override( array(
                'author_id' => $an_author['author_id'],
                'site_lang' => $this->site_language,
                'author_name' => $an_author['author_name'],
                'author_title' => $an_author['author_title'],
                'author_email' => $an_author['author_email']
            ) );
            $ret .= $t_a->get();
            unset($t_a);
        }
        return $ret;
    }

    /* список статей (публикаций) у указанного автора (для /author/info ) */
    public function getArticles_ByAuthor($id)
    {
        $ret = '';
        $articles = LoadArticles_ByAuthor($id, $this->site_language);

        if (count($articles) > 0) {
            // Начало строки статей у автора
            $ret .= <<<FE_PrintArticles_ByAuthor_Start
<table class="articles_by-author-table">
FE_PrintArticles_ByAuthor_Start;

            // Каждая строка "статья у автора" - из шаблона
            foreach ($articles as $aid => $article) {
                $t_a = new kwt($this->template_path.'/_internal/item_in_articles_by_author_list.html', '<!--{', '}-->');
                $t_a->override( array(
                    'btitle'    => $article['btitle'],
                    'pdfid'     => $article['pdfid'],
                    'aid'       => $article['aid'],
                    'atitle'    => $article['atitle'],
                    'bdate'     => $article['bdate']
                ) );
                $ret .= $t_a->get();
                unset($t_a);
            }

            // Конец строки статей у автора
            $ret .= <<<FE_PrintArticles_ByAuthor_End
</table>
FE_PrintArticles_ByAuthor_End;
        } else {
            $ret .= ''; // нет статей у автора (Messages::NoArticles_ByAuthor)
        }

        return $ret;
    } // getArticles_ByAuthor

    /* сообщение "нет авторов" для разных языков */
    private function messageNoPlainAuthors()
    {
        $r = '';
        switch ($this->site_language) {
            case 'en' : {
                $r = '<br><strong>Authors not found!</strong>'; break;
            }
            case 'ru' : {
                $r = '<br><strong>Авторы не найдены</strong>'; break;
            }
            case 'uk' : {
                $r = '<br><strong>Автори не знайдені</strong>'; break;
            }
        }
        return $r;
    }

    /* список авторов в виде plain/list ( /authors/all для поисковых систем и не только)*/
    public function getAuthors_PlainList($letter)
    {
        $return = '';
        $authors = LoadAuthors_ByLetter($letter, $this->site_language, 'no');

        // начало
        $return .= <<<PrintAuthorsSelectedByLetter_Start
<ul class="authors-list">
PrintAuthorsSelectedByLetter_Start;

        if (sizeof($authors) > 0)
        {
            foreach ($authors as $i => $an_author)
            {
                $t_a = new kwt($this->template_path.'/_internal/item_in_authors_plain_list.html', '<!--{', '}-->');
                $t_a->override( array(
                    'id'    => $an_author['id'],
                    'name'  => $an_author['name'],
                    'title' => (!empty($an_author['title'])) ? ", ".$an_author['title'] : " ",
                    'email' => $an_author['email']
                ) );
                $return .= $t_a->get();
                unset($t_a);
            }
        } else {
            $return .= $this->messageNoPlainAuthors();
        }
        // конец
        $return .= <<<PrintAuthorsSelectedByLetter_End
</ul>
PrintAuthorsSelectedByLetter_End;

        return $return;
    } // getAuthors_PlainList

    // печать нужных авторов ($authors) в расширенной форме для /authors/estuff
    // функция НЕ оборачивает элементы списка в UL, поэтому её вывод надо вставлять во внутрь списка в шаблоне
    function getAuthors_EStaffList($selfhood)
    {
        $authors = LoadAuthors_ByLetter('0', $this->site_language, 'yes', $selfhood);

        $return = '';
        $return .= <<<fe_printauthors_estuff_start
fe_printauthors_estuff_start;

        if ( sizeof($authors) > 0 ) {
            foreach ($authors as $i => $an_author ) {
                $name = $an_author['name'];
                // первое слово в имени обернуть в <span class="authors-estufflist-firstword">
                $name = preg_replace('/^([^\s]+)/','<span class="authors-estufflist-firstword">\1</span>', $name);

                $title = $an_author['title'];
                $title = (trim($title) != '') ? "<br><div class=\"smaller\">{$title}</div>" : "";

                $workplace = $an_author['workplace'];
                $workplace = ($title != '') ? "<div class=\"smaller\">{$workplace}</div>" : "";

                $email = ($an_author['email'] != '') ? "<strong>E-Mail: </strong>{$an_author['email']}" : '';

                $return .= <<<fe_printauthors_estuff_each
            <li><a class="authors-estufflist-name" href="/?fetch=authors&with=info&id={$an_author['id']}">{$name}</a>{$title}{$workplace}{$email}</li>
fe_printauthors_estuff_each;
            }
        }

        $return .= <<<fe_printauthors_estuff_end
fe_printauthors_estuff_end;

        return $return;
    }

    /* список статей в виде plain/list (для поисковых систем) */
    /* похоже по логике на getArticlesList, но другой формат вывода */
    public function getArticles_PlainList($request)
    {
        $articles = LoadArticles_ByQuery($request, $this->site_language);
        $return = '';
        if (count($articles)>0)
        {
            $return .= <<<PAL_S_Start
<ul class="articles-list-full">
PAL_S_Start;
            foreach ($articles as $an_article)
            {
                // превращаем массив из нескольких авторов в строку, разделитель ;
                // возможно в иных шаблонах потребуется иное представление, то есть нам нужно будет переписать функцию
                $authors = array();
                foreach ($an_article['authors'] as $an_author)
                {
                    $authors[] = $an_author['author_name'];
                }
                $authors_string = implode("; ", $authors);

                // выводит каждый элемент по формату шаблона
                $t_a = new kwt($this->template_path.'/_internal/item_in_articles_plain_list.html', '<!--{', '}-->');
                $t_a->override( array(
                    'id'                => $an_article['id'],
                    'article_title'     => $an_article['article_title'],
                    'authors_string'    => $authors_string,
                    'book_year'         => $an_article['book_year'],
                    'book_title'        => $an_article['book_title']
                ) );
                $return .= $t_a->get();
                unset($t_a);
            }
            $return .= <<<PAL_S_End
</ul>
PAL_S_End;
        }
        return $return;
    }



}
?>
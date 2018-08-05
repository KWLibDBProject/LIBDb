<?php
require_once('core/core.kwt.php');
require_once('frontend.php');

/**
Класс-предок для всех шаблонов.

Расширяется классами-шаблонами, описываемыми в template.{name}.php

Класс __Template описывает базовые методы вывода данных в шаблон(ы). В классах-наследниках
это поведение можно переопределить. Фактически это VIEW в методологии MVC.

Эти классы запрашивают функции из frontend.php - там должны быть функции работы с базой -
только получение результатов, никаких отображений.
 */
class __Template
{
    public $template_path = '';
    public $site_language = '';
    public $page_prefix = '';

    /**
     * @param $path
     * @param $site_language
     */
    public function __construct($path, $site_language)
    {
        $this->template_path = $path;
        $this->site_language = $site_language;

        // определим префикс страницы по языку
        switch ($site_language) {
            case 'en': { $this->page_prefix = 'Pp. '; break; }
            case 'ru': { $this->page_prefix = 'C. '; break; }
            case 'ua': { $this->page_prefix = 'C. '; break; }
        } // end switch
    }

    /**
     * отображение статической страницы: alias
     * @param $alias
     * @return mixed|string
     */
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

    /**
     * оформляет массив баннеров в LI-список (VIEW!)
     * @return null|string
     * @todo: EXPORT
     */
    public function getBanners()
    {
        $template_dir = '$/template.bootstrap24/_websun_templates';
        $template_file = "frontpage_banners_section.html";

        $template_data = array(
            'all_banners' =>  LoadBanners()
        );

        // ? перенести в основной шаблон как подключение файла с передачей ему параметров

        $render_result = \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);

        return $render_result;
    } // GetBanners

    /**
     * возвращает длинную строку с новостями --результат подставляется в override-переменную
     * новостного блока (справа под сборниками)
     * @param $count
     * @return string
     *
     * @todo: EXPORT
     */
    public function getLastNews($count)
    {
        $template_dir = '$/template.bootstrap24/_websun_templates';
        $template_file = "frontpage_news_section.html";

        $template_data = array(
            'last_news_list' =>  LoadLastNews($this->site_language, $count)
        );

        $render_result = \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);

        return $render_result;

    } // GetLastNews

    /**
     * возвращает строку с меню
     * @return mixed
     *
     * @todo: USELESS
     */
    public function getMenu()
    {
        $main_menu = new kwt($this->template_path."/_menu/menu.{$this->site_language}.html");
        return $main_menu->get();
    }

    /**
     * сообщение "нет статей" для разных языков
     * @return string
     */
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
            case 'ua' : {
                $r = '<br><strong>За заданими критеріями пошуку статей не знайдено!</strong>'; break;
            }
        }
        return $r;
    }

    /**
     * список статей,
     * инфо о каждой статье делается на основе /_internal/item_in_articles_list.html
     *
     * @param $request
     * @param string $with_email
     * @return string
     */
    public function getArticlesList($request, $with_email = 'no')
    {
        global $mysqli_link;
        $articles = LoadArticles_ByQuery($request, $this->site_language);
        $return = '';
        if (count($articles) > 0)
        {
            // хедер таблицы
            $return .= '<table class="articles-list">';

            // в цикле загружаем шаблон и передаем в него строки таблицы
            // и результат функции "печать списка авторов на основе $articles['authors'] "

            foreach ($articles as $an_article_id => $an_article) {
                $authors_list = $this->getAuthors_InArticlesList($an_article['authors']);

                $t_a = new kwt($this->template_path.'/_internal/row_in_articles_list.html', '<!--{', '}-->');
                $t_a->override(array(
                    'book_title'        => $an_article['book_title'],
                    'lal_e_bi'          => $this->page_prefix,
                    'article_pages'     => $an_article['article_pages'],
                    'pdfid'             => $an_article['pdfid'],
                    'article_id'        => $an_article['id'],
                    'article_title'     => $an_article['article_title'],
                    'authors_list'      => $authors_list,
                    'book_year'         => $an_article['book_year'],
                    'pdf_filename'      => $an_article['pdf_filename']
                ));
                $return .= $t_a->get();
                unset($t_a);
            } // foreach

            // футер таблицы
            $return .= '</table>';
        } else {
            $return .= $this->messageNoArticles();
        }
        return $return;
    }

    /**
     * список авторов в полной информации о статье!
     * Передаем массив с авторами (который можно загрузить по-разному)
     *
     * @param $authors
     * @param string $with_email
     * @return string
     */
    public function getAuthors_InArticle($authors, $with_email = '')
    {
        $ret = '';
        foreach ($authors as $author_id => $an_author)
        {
            // Иванов И.И., др.тех.наук
            if (($with_email != '') && ($an_author['author_email'] != '')) {
                $an_author['author_email'] = ' ('.$an_author['author_email'].')';
            } else { $an_author['author_email'] = ''; }

            // выводит каждый элемент по формату шаблона
            $t_a = new kwt($this->template_path.'/_internal/an_author_in_article_info.html', '<!--{', '}-->');
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

    /**
     * список авторов в статье
     * (для поля "авторы" в табличном представлении статей, вызывается в getArticlesList )
     * @param $authors
     * @param string $with_email
     * @return string
     */
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
            $t_a = new kwt($this->template_path.'/_internal/an_author_in_articles_list.html', '<!--{', '}-->');
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

    /**
     * сообщение "нет авторов" для разных языков
     * @return string
     * @todo: USELESS
     */
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
            case 'ua' : {
                $r = '<br><strong>Автори не знайдені</strong>'; break;
            }
        }
        return $r;
    }

    /**
     * список авторов в виде plain/list ( /authors/all для поисковых систем и не только)
     * @param $letter
     * @return array
     * @todo: USELESS
     */
    public function getAuthors_PlainList($letter)
    {
        return LoadAuthors_ByLetter($letter, $this->site_language, 'no');
    }

    /**
     * печать нужных авторов ($authors) в расширенной форме для /authors/estuff
     * функция НЕ оборачивает элементы списка в UL, поэтому её вывод надо вставлять
     * внутрь списка в шаблоне
     *
     * @param $estaff_role
     * @return array
     *
     * @todo: EXPORT
     */
    function getAuthors_EStaffList($estaff_role)
    {
        $authors = LoadAuthors_ByLetter('0', $this->site_language, 'yes', $estaff_role);

        // Первое слово имени выделяем стилем
        $authors = array_map(function ($v){
            $v['name'] = preg_replace('/^([^\s]+)/','<span class="estaff-name-firstword">\1</span>', $v['name']);
            return $v;
        }, $authors);

        return $authors;
    }

    /**
     * список статей в виде plain/list (для поисковых систем)
     * похоже по логике на getArticlesList, но другой формат вывода
     *
     * @param $request
     * @return array
     *
     * @todo: EXPORT METHOD
     */
    public function getArticles_PlainList($request)
    {
        $articles = LoadArticles_ByQuery($request, $this->site_language);

        /*
         * @HINT
        Теперь склеим ФИО в строку
        Если мы не будем использовать склейку в массиве - то нужен итератор по фамилиям в шаблоне. Шаблон будет сложнее.

        Можно двум форычами:

        foreach ($articles as $i => &$an_article) {
            $authors = [];
            foreach ( $an_article['authors'] as $an_author) {
                $authors[] = $an_author['author_name'];
            }
            $an_article['authors'] = implode(', ', $authors);
        }

        но мы используем "модный" array_map
        */

        // переберем все статьи
        $articles = array_map(function ($v_article){

            // итерируем массив авторов, возвращая только элемент с ФИО у каждого элемента
            $authors = array_map(function ($v_author){
                return $v_author['author_name'];
            }, $v_article['authors']);

            // склеиваем в строчку массив ФИО и присваиваем элементу с ключом `authors` массива статей это значение

            $v_article['authors'] = implode(', ', $authors);

            // возвращаем статью из замыкания
            return $v_article;

        }, $articles);
        // возможно, стоит возвращать authors_string

        // printr($articles);

        return $articles;
    }

    /**
     * Прототип, переопределяется в конкретном шаблоне
     * @return null
     */
    function getTopics()
    {
        return null;
    }

    /**
     * Прототип, переопределяется в конкретном шаблоне
     * @return null
     */
    function getBooks()
    {
        return null;
    }


}
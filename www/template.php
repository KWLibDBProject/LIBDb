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
     * оформляет массив баннеров в LI-список (VIEW!)
     * @return null|string
     * @todo: EXPORT to TEMPLATE or INDEX
     */
    public function getBanners()
    {
        $template_dir = '$/template.bootstrap24/_main_subtemplates';
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
     * @todo: EXPORT to TEMPLATE or INDEX
     */
    public function getLastNews($count)
    {
        $template_dir = '$/template.bootstrap24/_main_subtemplates';
        $template_file = "frontpage_news_section.html";

        $template_data = array(
            'last_news_list' =>  LoadLastNews($this->site_language, $count)
        );

        $render_result = \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);

        return $render_result;

    } // GetLastNews

    /**
     * список статей,
     * инфо о каждой статье делается на основе /_internal/item_in_articles_list.html
     *
     * @param $request
     * @param string $with_email
     * @return array
     *
     * @todo: EXPORT METHOD
     */
    public function getArticlesList($request, $with_email = 'no')
    {
        // useless: an_author_in_articles_list.html
        // useless: row_in_articles_list.html

        global $mysqli_link;
        $articles = LoadArticles_ByQuery($request, $this->site_language);

        foreach ($articles as $an_article_id => &$an_article) {
            $an_article['authors_list'] = $an_article['authors'];
            $an_article['page_prefix'] = $this->page_prefix;
        }

        return $articles;
    }

    /**
     * печать нужных авторов ($authors) в расширенной форме для /authors/estuff
     * функция НЕ оборачивает элементы списка в UL, поэтому её вывод надо вставлять
     * внутрь списка в шаблоне
     *
     * @param $estaff_role
     * @return array
     *
     * @todo: EXPORT METHOD
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

        возможно, стоит возвращать authors_string, а заменять authors
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


        // printr($articles);

        return $articles;
    }

}
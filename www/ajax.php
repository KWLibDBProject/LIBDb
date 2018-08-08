<?php
// Здесь собраны функции-ответы на вывод различных данных, передаваемых аяксом.
// в основном это ответы на разные селекторы
define('__ROOT__', __DIR__);
require_once (__ROOT__ . '/core/__required.php');
require_once 'frontend.php';

$main_theme_name    = $CONFIG['frontend_template_name']; // 'template.bootstrap24'
$main_theme_dir     = $CONFIG['frontend_template_name']; // так же, но может измениться (имя папки с темой в корне, но только имя папки!!!)

$actor = isset($_GET['actor']) ? $_GET['actor'] : ''; // безопасный результат - проверка в switch
$lang = isset($_GET['lang']) ? GetRequestLanguage($_GET['lang']) : 'en';

$return = '';

switch ($actor) {
    case 'get_letters_as_optionlist' : {
        /* загрузить первые буквы авторов и отдать JSON-объект для построения селекта */
        $data = LoadFirstLettersForSelector($lang);
        $return = json_encode($data);
        break;
    }
    case 'get_books_as_optionlist_extended' : {

        // BETTER: request to /core/core.books/books.action.getoptionlist.php

        $i = 1;
        $withoutid = isset($_GET['withoutid']) ? intval($_GET['withoutid']) : 1;
        $q = "SELECT * FROM books WHERE published = 1 ORDER BY SUBSTRING(title, 6, 2)";  //@todo: магическая подстановка
        $r = mysqli_query($mysqli_link, $q) or die($q);
        $n = @mysqli_num_rows($r) ;

        if ($n > 0)
        {
            $data['error'] = 0;
            while ($row = mysqli_fetch_assoc($r))
            {
                $ov_id = ($withoutid == 1) ? '' : "[{$row['id']}]" ;
                $option_value = "{$ov_id} {$row['title']}";

                $data['data'][ $i ] = array(
                    'type'      => 'option',
                    'value'     => $row['id'],
                    'text'      => $option_value // returnBooksOptionString($row, $lang, $withoutid)
                );
                $i++;
            }
        } else {
            $data['data'][1] = "Добавьте книги (сборники) в базу!!!"; //@todo: __lang( message )
            $data['error'] = 1;
        }
        $return = json_encode($data);
        break;

    }
    case 'get_topics_as_optgroup_list' : {
        // /articles/book

        // BETTER : request to /core/core.topics/topicgroups.action.getoptionlist.php

        /* загрузить категории и отдать JSON-объект для построения селекта c группировкой */
        $withoutid = isset($_GET['withoutid']) ? intval($_GET['withoutid']) : 1;

        $data = LoadTopicsTree($lang, $withoutid);
        if ($data['data'][1]['value'] != -1 ) {
            $data['state'] = 'ok';
            $data['error'] = 0;
        }

        $return = json_encode($data);
        break;
    }

    case 'load_authors_selected_by_letter': {
        // called js from /authors/list

        $authors_list = LoadAuthors_ByLetter($_GET['letter'], $lang, 'no');

        $template_dir = "$/{$main_theme_dir}/authors/all/";
        $template_file_name = "authors__all.{$lang}";
        $inner_html_data = [
            'all_authors_list' => $authors_list
        ];

        $return = \Websun\websun::websun_parse_template_path($inner_html_data, "{$template_file_name}.html", $template_dir);

        break;
    }

    case 'load_articles_by_query' : {
        // Поиск статей - расширенный (/articles/extended/)
        // called by:
        //      articles/extended.*.js
        //      articles/topic
        //      articles/book

        $template_dir = "$/{$main_theme_dir}/_main_ajax_templates/";
        $template_file_name = "ajax.articles__extended.{$lang}.html"; // delete row_in_articles_list.html

        $inner_html_data = [
            'articles_list' =>  getArticlesList($_GET, $lang),
            'with_email'    =>  'no',
            'site_lang'     =>  $lang
        ];

        $return = \Websun\websun::websun_parse_template_path($inner_html_data, $template_file_name, $template_dir);

        break;
    }

    case 'load_articles_expert_search': {
        //@TODO: unused
        // Поиск статей - экспертный ( в keywords может быть склеенная плюсом строчка )
        // completly equal load_articles_by_query
        $return = getArticlesList($_GET, $lang);
        break;
    }

    default: {
        $return = 'Unknown request method!';
        break;
    }

} // switch


if (isAjaxCall()) {
    print($return);
} else {
    print_r($return);
}
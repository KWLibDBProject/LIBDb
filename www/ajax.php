<?php
// Здесь собраны функции-ответы на вывод различных данных, передаваемых аяксом.
// в основном это ответы на разные селекторы
require_once('core/core.php');
require_once('core/core.db.php');
require_once('frontend.php');
require_once('template.bootstrap24.php');

$actor = isset($_GET['actor']) ? $_GET['actor'] : '';
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';

$return = '';
$link = ConnectDB();

$engine = new Template($tpl_path, $lang);

switch ($actor) {
    case 'get_letters_as_optionlist' : {
        /* загрузить первые буквы авторов и отдать JSON-объект для построения селекта */
        $data = LoadFirstLettersForSelector($lang);
        $return = json_encode($data);
        break;
    }
    case 'get_books_as_optionlist' : {
        /* загрузить сборники и отдать JSON-объект для построения селекта */
        $withoutid = isset($_GET['withoutid']) ? $_GET['withoutid'] : 1;
        $q = "SELECT * FROM books WHERE deleted=0 AND published=1";
        $r = mysql_query($q) or die($q);
        $n = @mysql_num_rows($r) ;

        if ($n > 0)
        {
            $data['error'] = 0;
            while ($row = mysql_fetch_assoc($r))
            {
                $data['data'][ $row['id'] ] = returnBooksOptionString($row,$lang,$withoutid); // see core.php
            }
        } else {
            $data['data'][1] = "Добавьте книги (сборники) в базу!!!";
            $data['error'] = 1;
        }
        $return = json_encode($data);
        break;
    }
    case 'get_topics_as_optionlist' : {
        /* загрузить категории и отдать JSON-объект для построения селекта */
        $withoutid = isset($_GET['withoutid']) ? $_GET['withoutid'] : 1;
        $query = "SELECT * FROM topics WHERE deleted=0";
        $result = mysql_query($query) or die($query);
        $ref_numrows = @mysql_num_rows($result) ;

        if ($ref_numrows > 0)
        {
            $data['error'] = 0;
            while ($row = mysql_fetch_assoc($result))
            {
                $data['data'][ $row['id'] ] = returnTopicsOptionString($row,$lang,$withoutid); // see CORE.PHP
            }
        } else {
            $data['data'][1] = "Добавьте темы (топики) в базу!!!";
            $data['error'] = 1;
        }
        $return = json_encode($data);
        break;
    }

    case 'load_articles_by_query' : {
        // Поиск статей - расширенный (/articles/extended/)
        // called by:     f_articles+w_extended.en.js ->
        $return = $engine -> getArticlesList($_GET);
        break;
    }

    case 'load_authors_selected_by_letter': {
        $return = $engine -> getAuthors_PlainList($_GET['letter']);
        break;
    }
    case 'load_articles_expert_search': {
        // Поиск статей - экспертный ( в keywords может быть склеенная плюсом строчка )
        $return = $engine -> getArticlesList($_GET);
        break;
    }

} // switch


CloseDB($link);

if (isAjaxCall()) {
    print($return);
} else {
    print_r($return);
}
?>
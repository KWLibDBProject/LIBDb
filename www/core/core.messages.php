<?php

class TheMessenger {
    /* Messages section */
    private $MESSAGES = array(
    'errors' => array(
        'mysql_query_error' => array(
            'en' => 'Unable retrieve table content! ',
            'ru' => 'Невозможно получить содержимое справочника! '
        ),

    ),

    'topics' => array(
        'topics_not_found' => array(
            'en' => '',
            'ru' => 'Пока не добавили ни один тематический раздел'
        ),

    ),
    'articles' => array(),
    'authors' => array(),
    'books' => array(),
    'news' => array(),
    'users' => array(),
    );


    /*system section*/
    protected static $instance;
    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}
    public static function getIt() {
        if ( !isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /* data section */
    private $language='en';
    private $delimeter='/';

    public function config($language, $delimeter = '/')
    {
        $this->language = $language;
        $this->delimeter = $delimeter;
    }
    public function say($what, $message='') // path to message, addition message
    {
        $ma = explode($this->delimeter, $what);
        $primary = $ma[0];
        $secondary = $ma[1];
        return $this->MESSAGES[$primary][$secondary][$this->language].$message;
    }
}


?>

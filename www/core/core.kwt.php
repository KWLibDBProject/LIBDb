<?php
/*
KarelWintersky's Template simple engine ver 1.4
*/

class kwt
{
    private $file;
    private $tag_open = '{%';
    private $tag_close = '%}';
    private $overrides = array();
    private $content;

    private function get_include_contents($filename)
    {
        if (is_file($filename)) {
            ob_start();
            include $filename;
            return ob_get_clean();
        }
        return null; // was false
    }

    // функция-обработчик. заменяет переменные в файле согласно массиву overrides
    private function kwt_callback(&$buffer)
    {
        $buf = $buffer;
        foreach ($this->overrides as $key => $value)
        {
            $skey = $this->tag_open.$key.$this->tag_close;
            $buf = str_replace($skey, $value, $buf);
        }
        return $buf;
    }

    // constructor: создаем экземпляр класса.
    // загружаем шаблон из $file, $open & $close - строки, обрамляющие заменяемые переменные
    public function __construct($file, $open = '{%', $close = '%}')
    {
        $this->file = dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file;
        $this->tag_open = $open;
        $this->tag_close = $close;
        $this->content = $this->get_include_contents($this->file);
    }

    // создает (или дополняет) массив замещаемых переменных в шаблоне
    public function override($arr)
    {
        if (!empty($arr)) {
            foreach ($arr as $ki => $kv) {
                if (!array_key_exists(strtolower($ki), $this->overrides)) $this->overrides[strtolower($ki)] = $kv;
            }
        } else {
            $this->overrides = array_merge($this->overrides,$arr);
        }
    }

    // возвращает обработанный шаблон в переменную (для использования в шаблонах верхнего уровня)
    public function get()
    {
        $return = $this->kwt_callback($this->content);
        return $return;
    }

    // выводит шаблон в буфер вывода, то есть в stdout (эквивалент функции flush() )
    public function out()
    {
        print $this->kwt_callback($this->content);
    }

    // переопределяет параметры экранирования заменяемых переменных, принимает строки
    public function config($start,$end)
    {
        $this->tag_open = $start;
        $this->tag_close = $end;
    }

    /* функции-обертки */

    /* вывод в stdout */
    public function flush()
    {
        $this->out();
    }

    /* вывод в переменную */
    public function getcontent()
    {
        return $this->get();
    }

    /**
     *
     * проверяет значение ключа в массиве kwt::overrides[] и возвращает его значение
     * Заготовка-костыль нормального парсера условных операций в шаблоне, см. todo в конце
     * Пример применения, в шаблоне:
     * <?php if ( $this->key('estaff_honorary_editor') != '') { ?>
         <h2>Почесний редактор</h2>
         <ul class="no-marker">
           {%estaff_honorary_editor%}
         </ul>
       <?php } ?>
    */
    public function key($key)
    {
        return isset($key) ? $this->overrides[ $key ] : null;
    }

}
/*
@todo:
Хочется парсинг вида:

{% $('estaff_honorary_editor') != '' { %}
...
{% } %}

вместо

<?php if ( $this->overrides['estaff_honorary_editor'] != '' ) {  ?>
        {%estaff_honorary_editor%}
<?php } ?>

но это если и делать - то в функции get_include_contents()
нужно загрузить



*/



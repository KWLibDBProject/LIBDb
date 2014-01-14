<?php
// kw templates

/* использовать
ассоциативный массив для замены:
{ ... ключ => строка ... }
ищем строку <%key%> и тупо меняем на содержимое ключа

*/
/* @todo: инструкция по пользованию */
class KWT
{
    private $tag_open = '{%';
    private $tag_close = '%}';

    private $file;
    private $overrides = array();

    // Конструктор класса, задает файл с шаблоном
    public function __construct($file)
    {
        $this->file = dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file;
        ob_start(array(&$this,'callback'));
    }

    // вызов требуется, если мы хотим что-то вывести через обычный echo (прямо "в теле" скрипта)
    public function contentstart()
    {
        ob_start(array(&$this,'callback'));
    }

    // вызов требуется, когда мы заканчиваем выводить что-то выводить в теле скрипта
    // принимает имя поля вставки
    public function contentend($target,$clear=true)
    {
        if (!isset($clear)) $clear = true;
        $target = strtolower($target);
        $this->overrides["$target"] = ob_get_contents();
        if ($clear) ob_end_clean();
    }

    // вызываем, когда надо отдать все с сервера, обычно в конце
    public function out()
    {
        include($this->file);
        ob_end_flush();
    }

    // загружает ассоциативный массив с заменами в обработчик шаблона
    // использовать можно в любое время
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

    // функция-обработчик. выполняет всю работу
    public function callback($buffer)
    {
        $buf = $buffer;
        foreach ($this->overrides as $key => $value) {
            $skey = $this->tag_open.$key.$this->tag_close;
            $buf = str_replace($skey, $value, $buf);
        }
        return $buf;
    }

    // функция записывает весь вывод в переменную и её возвразает. Использовать для вложенных шаблонов так:
    // new, override(), потом
    // $t2 -> contentstart(); <--- вызывать обязательно, если мы делаем какие-то оверрайды во вложенном шаблоне !!!!
    // $message = $t2->apply(); и используем как хотим :)
    public function getcontent($clear=true)
    {
        if (!isset($clear)) $clear = true;
        include($this->file);
        $return = ob_get_contents();

        if ($clear) ob_end_clean();
        return $return;
    }
    // а еще нужна функция, которая тупо инклюдит файл в переменную (возможно, даже , тупо через read)

    // изменяет параметры ограничений переменных, принимает строки
    public function config($start,$end)
    {
        $this->tag_open = $start;
        $this->tag_close = $end;
    }




}
?>
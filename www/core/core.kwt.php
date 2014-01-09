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
    public function __construct($file)
    {
        $this->file = dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file;
        ob_start(array(&$this,'callback'));
    }
    public function contentstart()
    {
        ob_start(array(&$this,'callback'));
    }
    public function override($arr)
    {
        $this->overrides = array_merge($this->overrides,$arr);
    }
    public function contentend($target,$clear=true)
    {
        if (!isset($clear)) $clear = true;
        $target = strtolower($target);
        $this->overrides["$target"] = ob_get_contents();
        if ($clear) ob_end_clean();
    }
    public function out()
    {
        include($this->file);
        ob_end_flush();
    }
    public function callback($buffer)
    {
        // @todo: исправить регистрозависимость в замене!!!!!!!!!
        $buf = $buffer;
        foreach ($this->overrides as $key => $value) {
            $skey = $this->tag_open.$key.$this->tag_close;
            $buf = str_replace($skey,$value,$buf);
        }
        return $buf;
    }




}
?>
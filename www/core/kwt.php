<?php
// kw templates

/* использовать
ассоциативный массив для замены:
{ ... ключ => строка ... }
ищем строку <%key%> и тупо меняем на содержимое ключа

*/
class KWT
{
    private $file;
    private $overrides;
    public function __construct($file)
    {
        $this->file = dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file;
        ob_start(array(&$this,'callback'));
    }
    public function start()
    {
        ob_start(array(&$this,'callback'));
    }
    public function override($arr)
    {
        $this->overrides = array_merge($this->overrides,$arr);
    }
    public function content($target,$clear=true)
    {
        $this->overrides["$target"] = ob_get_contents();
        if ($clear) ob_end_clean();
    }
    public function end()
    {
        include($this->file);
        ob_end_flush();
    }
    public function callback($buffer)
    {
        $buf = $buffer;
        foreach ($this->overrides as $key => $value) {
            $skey = "{%".$key."%}";
            $buf = str_replace($skey,$value,$buf);
        }
        return $buf;
    }




}
?>
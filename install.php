<?php

include("vendor/autoload.php");

class install
{

    protected $smarty = NULL;
    protected $templates = "";
    protected $htaccess = "";
    protected $dir = '';

    public function __construct()
    {
        $this->dir = $_SERVER['DOCUMENT_ROOT'];
        $this->smarty = new Smarty();
        $this->template = dirname(__FILE__) . '/templates/';
        $file = substr(__FILE__, strlen($this->dir));
        $file = substr($file, 0, -11);
        $file = str_replace("\\", "/", $file);
        $this->htaccess = "\n# MDview \nRewriteEngine on\nRewriteCond %{REQUEST_URI} \.(md)$|.(MD)$\nRewriteRule . " . $file . "MDview.php\n# MDview ";
    }

    public function Instalar()
    {

        $this->smarty->assign("file_htaccess", $this->dir . '.htaccess');
        $this->smarty->assign("htaccess", $this->htaccess);
        if (!file_exists($this->dir . '.htaccess'))
        {
            $this->WriteFile();
            $html = $this->smarty->fetch($this->templates . 'instalacion.tpl');
        } elseif (!$this->is_instaled())
        {
            $this->EditFile();
            $html = $this->smarty->fetch($this->templates . 'instalacion.tpl');
        } else
        {
            $html = $this->smarty->fetch($this->templates . 'instalado.tpl');
        }

        $this->smarty->assign("dir_mdview", "");
        $this->smarty->assign("html", $html);
        $this->smarty->assign("filename", " Instalacion ");
        $this->smarty->display($this->templates . 'MD.tpl');
    }

    public function WriteFile()
    {
        $this->smarty->assign("accion", "Creado");
        file_put_contents($this->dir . '.htaccess', $this->htaccess);
    }

    public function EditFile()
    {
        $f = fopen($this->dir . '.htaccess', "a");
        fwrite($f, $this->htaccess);
        fclose($f);
        $this->smarty->assign("accion", "Editado");
    }

    public function is_instaled()
    {
        $f = file_get_contents($this->dir . '.htaccess');

        return preg_match("(" . preg_quote($this->htaccess) . ")", $f);
    }

}

(new install())->Instalar();

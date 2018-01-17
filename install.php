<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2018 Enyerber Franco

 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
include("vendor/autoload.php");

/**
 * Instala MDview en el servidor apache 
 * 
 * @autor ENYREBER FRANCO                                                    
 * @copyright (C) 2018, Enyerber Franco 
 */
class install
{

    /**
     * Motor de plantillas 
     * @var Smarty 
     */
    protected $smarty = NULL;

    /**
     * directorio de platillas 
     * @var string 
     */
    protected $templates = "";

    /**
     * texto para agregar en el archivo .htaccess
     * @var string 
     */
    protected $htaccess = "";

    /**
     * directorio donde se escribe el archivo .htaccess
     * @var type 
     */
    protected $dir = '';

    /**
     * 
     */
    public function __construct()
    {
        $this->dir = realpath($_SERVER['DOCUMENT_ROOT']);
        $this->smarty = new Smarty();
        $this->template = dirname(__FILE__) . '/templates/';
        $file = substr(__FILE__, strlen($this->dir));
        $file = substr($file, 0, -11);
        $file = str_replace("\\", "/", $file);
        $this->htaccess = "# MDview \nRewriteEngine on\nRewriteCond %{REQUEST_URI} \.(md)$|.(MD)$\nRewriteRule . " . $file . "MDview.php\n# MDview ";
    }

    /**
     * Ejecuta la istalacion 
     */
    public function Instalar()
    {

        $this->smarty->assign("file_htaccess", $this->dir . '/.htaccess');
        $this->smarty->assign("htaccess", $this->htaccess);
        if (!file_exists($this->dir . '/.htaccess'))
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

    /**
     * escribe el archivo .htaccess desde cero
     */
    public function WriteFile()
    {
        $this->smarty->assign("accion", "Creado");
        file_put_contents($this->dir . '/.htaccess', $this->htaccess);
    }

    /**
     * edita un archivo .htacces existente 
     */
    public function EditFile()
    {
        $ht = file_get_contents($this->dir . '/.htaccess');
        $f = fopen($this->dir . '.htaccess', "w+");
        fwrite($f, $this->htaccess . $ht);
        fclose($f);
        $this->smarty->assign("accion", "Editado");
    }

    /**
     * verifica si existe 
     * @return bool
     */
    public function is_instaled()
    {
	
        $f = file_get_contents($this->dir . '/.htaccess');

        return preg_match("(" . preg_quote($this->htaccess) . ")", $f);
    }

}

/**
 * EJECUCION 
 */
(new install())->Instalar();

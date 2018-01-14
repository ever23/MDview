<?php

include("vendor/autoload.php");
$root = $_SERVER['DOCUMENT_ROOT'];
$file = substr(__FILE__, strlen($root));
$file = substr($file, 0, -11);

$file = str_replace("\\", "/", $file);
$htaccess = "\nRewriteEngine on\nRewriteCond %{REQUEST_URI} \.(md)$|.(MD)$\nRewriteRule . " . $file . "MDview.php";
$smarty = new Smarty();
$template = dirname(__FILE__) . '/templates/';
$smarty->assign("file_htaccess", $_SERVER['DOCUMENT_ROOT'] . '.htaccess');
$smarty->assign("htaccess", $htaccess);
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '.htaccess'))
{

    $smarty->assign("accion", "Creado");
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '.htaccess', $htaccess);
} else
{
    $f = fopen($_SERVER['DOCUMENT_ROOT'] . '.htaccess', "a");
    fwrite($f, $htaccess);
    fclose($f);
    $smarty->assign("accion", "Editado");
}

$html = $smarty->fetch($template . 'instalacion.tpl');
$smarty->assign("dir_mdview", "");
$smarty->assign("html", $html);
$smarty->assign("filename", " Instalacion ");

$smarty->display($template . 'MD.tpl');

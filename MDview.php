<?php

include("vendor/autoload.php");
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class MDview
{

    protected $FileMD = '';
    protected $script = '';
    protected $parse;
    protected $filename = '';
    protected $url = '';

    public function __construct($root, $url, $sript)
    {
        $this->parse = new \cebe\markdown\MarkdownExtra();
        $this->parse->html5 = true;
        $this->parse->enableNewlines = true;
        $this->FileMD = realpath($_SERVER['DOCUMENT_ROOT']) . $url;
        $this->script = dirname($sript);
        $this->filename = (new SplFileInfo($url))->getFilename();
        $this->url = $url;
    }

    public function ParseTemplate($tpl)
    {
        $md = file_get_contents($this->FileMD);
        $html = $this->parse->parse($md);

        $smarty = new Smarty();
        $smarty->cache_lifetime = 120;
        $template = dirname(__FILE__) . '/templates/';
        $smarty->assign("dir_mdview", $this->script . '/');
        $smarty->assign("html", $html);
        $smarty->assign("filename", $this->filename);
        $smarty->assign("md", $this->url);
        return $smarty->fetch($template . $tpl);
    }

    public function ParsePDF()
    {
        $name = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REDIRECT_URL'];

        $DomPdfConfig = new \Dompdf\Options();
        $DomPdfConfig->setIsHtml5ParserEnabled(true);
        $DomPdfConfig->setIsRemoteEnabled(true);
        $DomPdfConfig->setIsJavascriptEnabled(true);
        $DomPdfConfig->setIsFontSubsettingEnabled(true);
        $DomPdfConfig->setDefaultMediaType("dompdf");
        $domPdf = new \Dompdf\Dompdf($DomPdfConfig);
        $domPdf->setPaper("letter", "legal");
        $domPdf->loadHtmlFile($name);

        $head = $domPdf->getDom()->getElementsByTagName('head');

        $node = $domPdf->getDom()->createElement("link");
        $node->setAttribute('rel', "stylesheet");
        $node->setAttribute('href', $this->script . "/css/mediaDompdf.css");
        $head->item(0)->appendChild($node);

        $domPdf->render();

        return $domPdf->output();
    }

}

$md = new MDview($_SERVER['DOCUMENT_ROOT'], $_SERVER['REDIRECT_URL'], $_SERVER["SCRIPT_NAME"]);

if (!isset($_GET['pdf']))
{
    echo $md->ParseTemplate("MD.tpl");
} else
{
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline;');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    echo $md->ParsePDF();
}


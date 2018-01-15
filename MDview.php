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
 * Convierte un archivo md en html o pdf
 * @autor ENYREBER FRANCO                                                    
 * @copyright (C) 2018, Enyerber Franco 
 */
class MDview
{

    /**
     * nombre completo del archivo .md
     * @var SplFileInfo 
     */
    protected $FileMD = '';

    /**
     * directorio de MDview
     * @var string 
     */
    protected $script = '';

    /**
     *
     * @var cebe\markdown\GithubMarkdown
     */
    protected $parse;

    /**
     * Url del archivo MD
     * @var SplFileInfo 
     */
    protected $url = '';

    /**
     * 
     * @param string $root directorio raiz del servidor 
     * @param string $url url del archivo md
     * @param string $sript nombre completo del archivo de ejecucion 
     */
    public function __construct($root, $url, $sript)
    {
        $this->parse = new \cebe\markdown\MarkdownExtra();
        $this->parse->html5 = false;
        $this->parse->enableNewlines = true;
        $this->FileMD = new SplFileInfo(realpath($root) . $url);
        $this->script = dirname($sript);
        // $this->url->getFilename() = (new SplFileInfo($url))->getFilename();
        $this->url = new SplFileInfo($url);
    }

    /**
     * verifica que el archivo tenga como extencion md y exista 
     * @return bool
     */
    public function VerifiqueFile()
    {
        return strtolower($this->url->getExtension() == 'md') && file_exists($this->FileMD);
    }

    /**
     * muestra el error 404 
     * @param string $tpl
     * @return string
     */
    public function error404($tpl)
    {
        $smarty = new Smarty();
        $smarty->cache_lifetime = 120;
        $template = dirname(__FILE__) . '/templates/';
        $smarty->assign("url", $this->url);
        $html = $smarty->fetch('404.tpl');
        $smarty->assign("dir_mdview", '');
        $smarty->assign("html", $html);
        $smarty->assign("filename", "");
        $smarty->assign("md", "");
        return $smarty->fetch($template . $tpl);
    }

    /**
     * carga un template smarty y le inserta el texto html generado del archivo .md
     * @param string $tpl nombre del archivo .tpl
     * @return string
     */
    public function ParseTemplate($tpl)
    {
        $md = file_get_contents($this->FileMD);
        $html = $this->parse->parse($md);

        $smarty = new Smarty();
        $smarty->cache_lifetime = 120;
        $template = dirname(__FILE__) . '/templates/';
        $smarty->assign("dir_mdview", $this->script . '/');
        $smarty->assign("html", $html);
        $smarty->assign("filename", $this->url->getFilename());
        $smarty->assign("md", $this->url);
        return $smarty->fetch($template . $tpl);
    }

    /**
     * carga un template smarty, le inserta el texto html generado del archivo .md
     * y lo combierte en pdf 
     * @param string $tpl nombre del archivo .tpl
     * @return string
     */
    public function ParsePDF($tpl)
    {
        $name = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $this->url;

        $DomPdfConfig = new \Dompdf\Options();
        $DomPdfConfig->setIsHtml5ParserEnabled(true);
        $DomPdfConfig->setIsRemoteEnabled(true);
        $DomPdfConfig->setIsJavascriptEnabled(true);
        $DomPdfConfig->setIsFontSubsettingEnabled(true);
        $DomPdfConfig->setDefaultMediaType("dompdf");
        $domPdf = new \Dompdf\Dompdf($DomPdfConfig);
        $domPdf->setPaper("letter", "legal");
        list($protocol, $baseHost, $basePath) = \Dompdf\Helpers::explode_url($name);
        $domPdf->setBaseHost($baseHost);
        $domPdf->setProtocol($protocol);
        $domPdf->setBasePath($basePath);
        $html = $this->ParseTemplate($tpl);
        $domPdf->loadHtml($html);

        $head = $domPdf->getDom()->getElementsByTagName('head');

        $node = $domPdf->getDom()->createElement("link");
        $node->setAttribute('rel', "stylesheet");
        $node->setAttribute('href', $this->script . "/css/mediaDompdf.css");
        $head->item(0)->appendChild($node);

        $domPdf->render();

        return $domPdf->output();
    }

}

/**
 * EJECUCION 
 */
$md = new MDview($_SERVER['DOCUMENT_ROOT'], $_SERVER['REDIRECT_URL'], $_SERVER["SCRIPT_NAME"]);

if (!$md->VerifiqueFile())
{
    http_response_code(404);
    echo $md->error404("MD.tpl");
    return;
}


if (!isset($_GET['pdf']))
{
    echo $md->ParseTemplate("MD.tpl");
} else
{
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline;');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    echo $md->ParsePDF("MD.tpl");
}


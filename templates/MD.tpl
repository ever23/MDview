
<html lang="en">
    <head>
        <title> {$filename}</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{$dir_mdview}css/bootstrap.min.css">
        <link rel="stylesheet" href="{$dir_mdview}css/style.css">
        <script src="{$dir_mdview}js/jquery.min.js"></script>
        <script src="{$dir_mdview}js/bootstrap.min.js"></script>

        <link  href='https://fonts.googleapis.com/css?family=Roboto:400,500,400italic,300italic,300,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>

    </head>
    <body>
        <div class="wrapper">
            <div class="main-wrapper">
                <div id="readme" class="readme boxed-group clearfix announce instapaper_body md">
                    <h3 class="no_pdf ">
                        <svg aria-hidden="true" class="octicon octicon-book" height="16" version="1.1" viewBox="0 0 16 16" width="16"><path fill-rule="evenodd" d="M3 5h4v1H3V5zm0 3h4V7H3v1zm0 2h4V9H3v1zm11-5h-4v1h4V5zm0 2h-4v1h4V7zm0 2h-4v1h4V9zm2-6v9c0 .55-.45 1-1 1H9.5l-1 1-1-1H2c-.55 0-1-.45-1-1V3c0-.55.45-1 1-1h5.5l1 1 1-1H15c.55 0 1 .45 1 1zm-8 .5L7.5 3H2v9h6V3.5zm7-.5H9.5l-.5.5V12h6V3z"></path></svg>
                        {$filename}

                        <a href="{$md}?pdf=true" class="btn btn-danger pull-right"><img src="{$dir_mdview}/css/pdf.gif"></a>
                    </h3>

                    <article class="markdown-body entry-content" itemprop="text">
                        {$html}</article></div>
            </div>
            <footer class="footer no_pdf">
                <div class="pull-left">&copy; <a href="https://github.com/ever23/">Enyerber Franco</a></div>
                <div class="pull-right">Powered  by <a href="https://github.com/ever23/MDview">MDview</a></div>
            </footer>
        </div>

    </body>
</html>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?= $code . ' - ' . getHttpResponseMessage($code) ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <style>
            @import url(http://fonts.googleapis.com/css?family=Montserrat:400,700);
            body {
                font-family: 'Montserrat', sans-serif;
                font-size: 14px;
                font-weight: 400;
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
                -webkit-font-smoothing: subpixel-antialiased;
            }
            html,body{
                height: 100%;
            }
            .content{
                margin: 10px;
                height: 100%;
            }
            .error-title h1{
                font-size: 1.2em;
            }
            .error-title{
                padding: 10px;
                border: lightgray thick solid;
                background-color: #007bb6;
                color: white;
                margin-bottom: 20px;
                border-radius: 10px;
            }
            .error-body{
                padding: 10px;
                border: lightgray thick solid;
                background-color: #000;
                color: #449d44;
                border-radius: 10px;
                min-height: 70%;
            }
            .search{
                margin-bottom: 20px;
                text-align: right;
                margin-right: 10px;
            }
            .search span{
                margin-right: 10px;
            }
            .search a{
                background-color: lightgray;
                padding: 10px;
                text-decoration: none;
                border-radius: 5px;
            }
            .search a.google:hover{
                background-color: #d34836;
                color: #fff;
            }
            .search a.so:hover{
                background-color: #F48024;
                color: #fafafb;
            }
            .search a.duckduckgo:hover{
                background-color: #de5833;
                /*color: #6ec051;*/
                color: #fff;
            }
            .search a.bing:hover{
                background-color: #004159;
                color: #fff;
            }
        </style>
    </head>
    <body>
        <div class="content">
            <div class="error-title">
                <h1><?= $message['title'] ?></h1>
            </div>
            <?php if (!empty($message['search'])): ?>
            <div class="search">
                    <?php $q = http_build_query(['q' => $message['search']]) ?>
                    <span>Search</span>
                    <a href="https://www.google.com/search?<?= $q ?>" target="_blank" class="google">Google</a>
                    <a href="https://stackoverflow.com/search?<?= $q ?>" target="_blank" class="so">Stack Overflow</a>
                    <a href="https://duckduckgo.com/?<?= $q ?>" target="_blank" class="duckduckgo">DuckDuckGo</a>
                    <a href="https://www.bing.com/search?<?= $q ?>" target="_blank" class="bing">Bing</a>
                </div>
            <?php endif; ?>
            <?php if (!empty($message['trace'])): ?>
            <div class="error-body">
                    <p class="trace"><?= nl2br($message['trace']) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>
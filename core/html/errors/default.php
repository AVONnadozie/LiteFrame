<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?= $code.' - '.getHttpResponseMessage($code) ?></title>
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
        </style>
    </head>
    <body>
        <div class="content">
            <div class="error-title">
                <h1><?= $message['title'] ?></h1>
            </div>
            <?php if (!empty($message['trace'])): ?>
                <div class="error-body">
                    <p class="trace"><?= nl2br($message['trace']) ?></p>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>
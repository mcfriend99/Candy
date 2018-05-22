<?php

if(!defined('CANDY')){
    header('Location: /');
}

?>
<html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Page not found - <?php echo t('site_name'); ?></title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .rel {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .container {
            text-align: center;
        }

        .title {
            font-size: 84px;
            color: #cade2d; /* Alternatively: #20a700 */
        }

        .tip-bottom {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="center">
            <h1 class="title">404</h1>
        </div>
        <div class="center">
            I don't think we ever had a page "<?=get_url_segment(0);?>" on this platform.
        </div>
    </div>
</body>
</html>
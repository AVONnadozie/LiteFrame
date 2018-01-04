<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Bootstrap -->
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
        <!-- Main Style -->
        <link rel="stylesheet" type="text/css" href="assets/css/main.css">
        <!-- Responsive Style -->
        <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
        <!--Icon Font-->
        <link rel="stylesheet" media="screen" href="assets/fonts/font-awesome/font-awesome.min.css" />
        <!-- Extras -->
        <link rel="stylesheet" type="text/css" href="assets/extras/animate.css">
        <!-- jQuery Load -->
        <script src="assets/js/jquery-min.js"></script>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->

        <!--[if lt IE 9]>
                <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
                <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
              <![endif]-->
        <title><?= $bag->getDefaultTitle() . ' - ' . config('app.name') ?></title>

    </head>

    <body>
        <!-- Nav Menu Section -->
        <div class="logo-menu">
            <nav class="navbar navbar-default">
                <div class="container">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header col-md-3">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="<?= url('/') ?>"><i class="fa fa-fa fa-cubes"></i> <?= config('app.name') ?></a>
                    </div>

                    <div class="collapse navbar-collapse" id="navbar">
                        <ul class="nav navbar-nav pull-right">
                            <li class="active"><a href="<?= url('/') ?>">Home</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <!-- Nav Menu Section End -->

        <!-- Hero Area Section -->

        <section id="hero-area">
            <div class="hero-inner">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="subtitle-big wow fadeInDown"><?= config('app.name') ?></h2>
                            <h1 class="title-big wow fadeInDown" data-wow-delay=".7s"><?= $bag->getDefaultTitle() ?></h1>
                            <h2 class="subtitle-big wow fadeInDown" data-wow-delay=".14s">
                                <?= $bag->getTitle() ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Hero Area Section End-->


        <section id="bottom">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <h3>Products</h3>
                        <ul>
                            <li><a href="#">Paypal</a>
                            </li>
                            <li><a href="#">BitCoin</a>
                            </li>
                            <li><a href="#">Skrill</a>
                            </li>
                            <li><a href="#">Alertpay</a>
                            </li>
                            <li><a href="#">Payoneer</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <h3>FAQs</h3>
                        <ul>
                            <li><a href="#">Why choose us?</a>
                            </li>
                            <li><a href="#">Where we are?</a>
                            </li>
                            <li><a href="#">Fees</a>
                            </li>
                            <li><a href="#">Guarantee</a>
                            </li>
                            <li><a href="#">Discount</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <h3>About</h3>
                        <ul>
                            <li><a href="#">Career</a>
                            </li>
                            <li><a href="#">Partners</a>
                            </li>
                            <li><a href="#">Team</a>
                            </li>
                            <li><a href="#">Clients</a>
                            </li>
                            <li><a href="#">Contact</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12">
                        <h3>Find us on</h3>
                        <a class="social" href="#" target="_blank"><i class="fa fa-facebook fa-2x"></i></a>
                        <a class="social" href="#" target="_blank"><i class="fa fa-twitter fa-2x"></i></a>
                        <a class="social" href="#" target="_blank"><i class="fa fa-google-plus fa-2x"></i></a>
                        <a class="social" href="#" target="_blank"><i class="fa fa-linkedin fa-2x"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bootstrap JS -->
        <script src="assets/js/bootstrap.js"></script>
        <!-- WOW JS plugin for animation -->
        <script src="assets/js/wow.js"></script>
        <!-- All JS plugin Triggers -->
        <script src="assets/js/main.js"></script>
    </body>
</html>
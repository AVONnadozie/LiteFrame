<!DOCTYPE html>
<html lang="en">

    <head>
        <?= includeView('layouts/head') ?>
        <title><?= config('app.name') ?></title>

    </head>

    <body>
        <?= includeView('layouts/nav') ?>

        <!-- Hero Area Section -->

        <section id="hero-area">
            <div class="hero-inner">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="subtitle-big wow fadeInDown"><?= config('app.name') ?></h2>
                            <h1 class="title-big wow fadeInDown" data-wow-delay=".7s"><?= $code ?></h1>
                            <h2 class="subtitle-big wow fadeInDown" data-wow-delay=".14s">
                                <?= $message ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?= includeView('layouts/footer') ?>
    </body>
</html>
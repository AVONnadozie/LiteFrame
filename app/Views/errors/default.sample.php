<!DOCTYPE html>
<html lang="en">
    <head>
        <?= includeView('layouts/head') ?>
        <title><?= $bag->getDefaultTitle() . ' - ' . config('app.name') ?></title>
    </head>
    <body>
        <section class="white-text" style="padding: 1em">
            <h1><?= $bag->getDefaultTitle() ?></h1>
            <h2><?= $bag->getTitle() ?></h2>
        </section>
    </body>
</html>
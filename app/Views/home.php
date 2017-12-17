<!DOCTYPE html>
<html lang="en">

    <head>
        <?= includeView('layouts/head') ?>
        <title><?= config('app.name') ?></title>
    </head>

    <body>
        <?= includeView('layouts/nav') ?>

        <!-- Hero Area Section -->

        <section id="hero-area" class="">
            <div class="hero-inner">
                <div class="container">
                    <div class="row">
                        <div class="col s12 l12 center">
                            <h3 class="herotext myanimated myfadeIn" style=""><?= config('app.name') ?></h3>
                            <p class="herotext2 myanimated2 myfadeInUp">The PHP framework built to be swift and strong.</p>
                            <a href="<?= url('docs') ?>" class = "btn-large metallic waves-effect waves-light lato ">Documentation</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Hero Area Section End-->

        <div class="bg-metallic">
            <div class="container">
                <div class="descbdr">
                    <div class="descbox">

                        <div class="desctop">
                            <p class="center deschead"> Light. As Air. </p>
                        </div>
                        <div class="divider"></div>

                        <div class="descitem">
                            <div class="row descpad">
                                <div  class="col s12 m5 l5 center textpad">
                                    <h4 class="deschead">Light.</h4>
                                    <p class="descsub"> A feather-weight, lightning-fast PHP framework. <br />
                                        Why write more code when Lite Frame can do it all with less? 
                                        Lite Frame is the ideal combination of fast and light.
                                    </p>
                                </div>
                                <div class=" col s12 m7 l7 picpad">
                                    <div class="desc-img imgfast " style="margin: auto">

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="descitem">
                            <div class="row descpad">
                                <div  class="col s12 m5 l5 center textpad">
                                    <h4 class="deschead">Clean.</h4>
                                    <p class="descsub"> All of the functionality with none of the clutter. <br />
                                        Lose zero features even though you use a lighter framework.
                                        Lite Frame is built with the philosophy "If it's not essential, it's not included"
                                    </p>
                                </div>
                                <div class=" col s12 m7 l7 picpad">
                                    <div class="desc-img imgclean " style="margin: auto">

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="descitem">
                            <div class="row descpad">
                                <div  class="col s12 m5 l5 center textpad">
                                    <h4 class="deschead">Easy to Use.</h4>
                                    <p class="descsub"> From file organization to installation and function calls,
                                        ease of use comes first.
                                        No need for the command line. Just create your files and go!
                                    </p>
                                </div>
                                <div class=" col s12 m7 l7 picpad">
                                    <div class="desc-img imgeasy " style="margin: auto">

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="descitem">
                            <div class="row descpad">
                                <div  class="col s12 m5 l5 center textpad">
                                    <h4 class="deschead">Docs for Dummies.</h4>
                                    <p class="descsub"> Documentation so simple, anyone will understand! <br />
                                        We avoid using too much technical jargon in our docs. 
                                        Understanding the documentation should not be reserved for advanced users.
                                    </p>
                                </div>
                                <div class=" col s12 m7 l7 picpad">
                                    <div class="desc-img imgdoc " style="margin: auto">

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="hide-on-med-and-up mb-200"></div>

                        <div class="bottompad"></div>

                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="center">
                <div class="mg70"></div>
                <h4> Get started with Lite Frame now.</h4>
                <h6>As a user, or a contributor. </h6>
                <div class="mg30"></div>
                <div class="row">
                    <a href="#" class = "btn-large metallic waves-effect waves-light lato ">Download</a>
                    <a href="<?= url('docs') ?>" class = "btn-large metallic waves-effect waves-light lato ">Documentation</a>
                    <a href="#" class = "btn-large metallic waves-effect waves-purple lato ">GitHub</a>
                </div>
            </div>
        </div>

        <?= includeView('layouts/footer') ?>
    </body>
</html>

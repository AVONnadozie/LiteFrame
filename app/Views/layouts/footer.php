
<section id="bottom">
        <div class="divider"></div>
    <div class="container">
        <div class="row center mg30">
            <div class="col s12">
                <a class="social" href="<?= config('social.twit', '#') ?>" target="_blank"><i class="fa fa-twitter metallic-text fa-2x"></i></a>
                <a class="social" href="<?= config('social.gplus', '#') ?>" target="_blank"><i class="fa fa-github metallic-text fa-2x"></i></a>
                <a class="social" href="<?= config('social.lin', '#') ?>" target="_blank"><i class="fa fa-linkedin metallic-text fa-2x"></i></a>
            </div>

            <div class="col-xs-12" style="padding-top: 10px">
                <p>Copyright &copy; <?= date('Y') ?> <?= config('app.name')?></p>
            </div>
        </div>
    </div>
</section>

<!-- Bootstrap JS -->
<script src="<?= asset('js/bootstrap.js') ?>"></script>
<!-- WOW JS plugin for animation -->
<script src="<?= asset('js/wow.js') ?>"></script>
<!-- All JS plugin Triggers -->
<script src="<?= asset('js/main.js') ?>"></script>
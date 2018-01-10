<!-- Nav Menu Section -->
<div class = "row" style = "width: 100%; margin-bottom: 0px; background-color: #74828e">
    <div class = "" style="margin-bottom:">
        <nav role = "navigation" class="">
            <div class = "nav-wrapper container">
                <a href = "#" class = "brand-logo white-text"><h4 class="logo">Lite frame</h4></a>
                <ul id = "nav-mobile" class = "right hide-on-med-and-down lato">
                    <li class=" <?= isRoute('home') ? 'active' : '' ?> ">
                        <a href = "<?= url('/') ?>">
                            <i style="display: inline" class="fa fa-home" aria-hidden = "true"></i>
                            Home
                        </a>
                    </li>
                    <li class="<?= isRoute('iammissing') ? 'active' : '' ?>">
                        <a href = "<?= url('docs') ?>">
                            <i style="display: inline" class="fa fa-book" aria-hidden = "true"></i>
                            Docs
                        </a>
                    </li>
                </ul>

                <a href="#" class="button-collapse right">
                    <i class="material-icons black-text">menu</i>
                </a>
            </div>
        </nav>
    </div>
</div>
<!-- Nav Menu Section End -->
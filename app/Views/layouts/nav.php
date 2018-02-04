<!-- Nav Menu Section -->
<div class = "row" style = "width: 100%; margin-bottom: 0px; background-color: #242422">
    <div class = "" style="margin-bottom:">
        <nav role = "navigation" class="z-depth-0">
            <div class = "nav-wrapper">
                <a href = "#" class = "brand-logo white-text"><h4 class="logo"></h4></a>
                
                <ul id = "nav-mobile" class = "right hide-on-med-and-down lato">
                    <li class="<?= isRoute('iammissing') ? 'active' : '' ?>">
                        <a href = "<?= url('docs') ?>">
                            Documentation
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
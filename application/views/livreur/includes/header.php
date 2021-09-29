<div id="header">
    <a class="header-link" href="<?= livreur_url(); ?>">
        <i class="fa fa-bars" data-toggle="tooltip" data-placement="right" title="<?= _l('nav_sidebar_toggle_tooltip'); ?>"></i>
    </a>
    <a id="logo" href="<?= livreur_url(); ?>">
        <?= '<img src="' . base_url() . 'uploads/company/' . get_option('companyalias') . '/logo.png" alt="' . get_option('companyname') . '" />'; ?>
    </a>
    <nav>
        <a class="small-logo" href="<?= livreur_url(); ?>">
            <span class="text-primary"><?= '<img src="' . base_url() . 'uploads/company/' . get_option('companyalias') . '/logo.png" alt="' . get_option('companyname') . '" />'; ?></span>
        </a>
        <div class="mobile-menu">
            <button type="button" class="navbar-toggle visible-sm visible-xs mobile-menu-toggle collapsed" data-toggle="collapse" data-target="#mobile-collapse" aria-expanded="false">
                <i class="fa fa-chevron-down"></i>
            </button>
            <div class="mobile-navbar collapse" id="mobile-collapse" aria-expanded="false" style="height: 0px;" role="navigation" >
                <ul class="nav navbar-nav">
                    <li><a href="<?= livreur_url('profile'); ?>"><?= _l('nav_my_profile'); ?></a></li>
                    <li><a href="<?= site_url(); ?>authentication/logout"><?= _l('nav_logout'); ?></a></li>
                </ul>
            </div>
        </div>
    </nav>
</div>
<div id="mobile-search" class="hide">
    <ul></ul>
</div>

<?php init_head_livreur(); ?>
<div id="wrapper">
    <h4 class="bloc-title-mobile default-background-color default-color">
        <span class="icon-return-mobile">
            <i class="fa fa-arrow-left mright5"></i><?= _l('return') ?>
        </span>
        <?= _l('list_colis') ?>
    </h4>
    <div class="content">
        <div class="row no-margin">
            <a href="<?= livreur_url('colis/delivred'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list "><?= _l('delivred'); ?></a>
            <a href="<?= livreur_url('colis/shipped'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list"><?= _l('shipped'); ?></a>
            <a href="<?= livreur_url('colis/returned'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list"><?= _l('returned'); ?></a>
            <a href="<?= livreur_url('colis/postponed'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list"><?= _l('postponed'); ?></a>
            <a href="<?= livreur_url('colis/unreachable'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list"><?= _l('unreachable'); ?></a>
            <a href="<?= livreur_url('colis/no_answer'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list"><?= _l('no_answer'); ?></a>
            <a href="<?= livreur_url('colis/refused'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list"><?= _l('refused'); ?></a>
            <a href="<?= livreur_url('colis/cancelled'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list"><?= _l('cancelled'); ?></a>
            <a href="<?= livreur_url('colis/wrong_number'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list"><?= _l('wrong_number'); ?></a>
        </div>
    </div>
</div>
<?php init_tail_livreur(); ?>
<script src="<?= site_url('assets/js/livreur/colis/menu.js?v=' . version_sources()); ?>"></script>
</body>
</html>

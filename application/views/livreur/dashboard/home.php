<?php init_head_livreur(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row no-margin">
            <a href="<?= livreur_url('colis'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list">
                <?= _l('colis'); ?>
            </a>
            <a href="<?= livreur_url('bons_livraison'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list">
                <?= _l('delivery_notes'); ?>
            </a>
            <a href="<?= livreur_url('etats_colis_livrer'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list">
                <?= _l('etats_colis_livrer'); ?>
            </a>
            <a href="<?= livreur_url('notifications'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list">
                <?= _l('notifications'); ?>
            </a>
        </div>
    </div>
</div>
<?php init_tail_livreur(); ?>
<script src="<?= site_url('assets/js/livreur/dashboard/home.js?v=' . version_sources()); ?>"></script>
</body>
</html>

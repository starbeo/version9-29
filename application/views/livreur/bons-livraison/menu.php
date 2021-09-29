<?php init_head_livreur(); ?>
<div id="wrapper">
    <h4 class="bloc-title-mobile default-background-color default-color">
        <span class="icon-return-mobile">
            <i class="fa fa-arrow-left mright5"></i><?= _l('return') ?>
        </span>
        <?= $title ?>
    </h4>
    <div class="content">
        <div class="row no-margin">
            <a href="<?= livreur_url('bons_livraison/output'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list "><?= _l('delivery_note_type_output'); ?></a>
            <a href="<?= livreur_url('bons_livraison/returned'); ?>" class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 default-background-color default-color bloc-list"><?= _l('delivery_note_type_returned'); ?></a>
        </div>
    </div>
</div>
<?php init_tail_livreur(); ?>
<script src="<?= site_url('assets/js/livreur/bons-livraison/menu.js?v=' . version_sources()); ?>"></script>
</body>
</html>

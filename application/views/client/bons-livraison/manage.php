<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/client/includes/alerts.php'); ?>
            <div id="filter-table" class="bloc-filter-table display-none">
                <div class="bloc-content-filter">
                    <i id="icon-remove-bloc-filter" class="fa fa-remove" title="<?= _l('close_menu') ?>"></i>
                    <h3 class="title-bloc-filter"><?= _l('filter') ?></h3>
                    <?= render_date_input('f-date-created-start', 'date_created_start', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-created-end', 'date_created_end', '', array(), array(), 'mbot10'); ?>
                    <div id="filter-submit" class="btn btn-success width100p mbot5" data-table="delivery-notes"><?= _l('filter'); ?></div>
                    <div id="filter-reset" class="btn btn-info width100p mbot5" data-table="delivery-notes"><?= _l('reset'); ?></i></div>
                    <div id="filter-close" class="btn btn-default width100p mbot5"><?= _l('close'); ?></i></div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="<?= client_url('bons_livraison/bon'); ?>" class="btn btn-info mright5 pull-left display-block"><?= _l('new_delivery_note'); ?></a>
                        <a href="javascript:void(0)" class="btn btn-default pull-right btn-statistique mright5"><?= _l('delivery_notes_summary'); ?></a>
                        <a href="javascript:void(0)" class="btn btn-default pull-right btn-filter mright5"><?= _l('filter'); ?></a>
                        <div id="statistique" class="row small-text-span mbot15 display-none">
                            <div class="clearfix"></div>
                            <hr />
                            <div class="col-md-12">
                                <h3 class="text-muted no-margin"><?= _l('delivery_notes_summary'); ?></h3>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center">
                                <h3 class="bold"><?= total_rows('tblbonlivraisoncustomer', array('id_expediteur' => get_expediteur_user_id())); ?></h3>
                                <a href="javascript:void(0)"><span class="text-muted bold"><?= _l('total'); ?></span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('name'),
                            _l('delivery_note_number_of_delivery_notes'),
                            _l('date_created'),
                            _l('options')
                            ), 'delivery-notes');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail_client(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/client/bons-livraison/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>

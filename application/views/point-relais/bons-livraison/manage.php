<?php init_head_point_relais(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/point-relais/includes/alerts.php'); ?>
            <div id="filter-table" class="bloc-filter-table display-none">
                <div class="bloc-content-filter">
                    <i id="icon-remove-bloc-filter" class="fa fa-remove" title="<?= _l('close_menu') ?>"></i>
                    <h3 class="title-bloc-filter"><?= _l('filter') ?></h3>
                    <?= render_select('f-point-relai', $points_relais, array('id', array('nom')), 'point_relai', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-created', 'date_created', '', array(), array(), 'mbot10'); ?>
                    <div id="filter-submit" class="btn btn-success width100p mbot5" data-table="delivery-notes"><?= _l('filter'); ?></div>
                    <div id="filter-reset" class="btn btn-info width100p mbot5" data-table="delivery-notes"><?= _l('reset'); ?></i></div>
                    <div id="filter-close" class="btn btn-default width100p mbot5"><?= _l('close'); ?></i></div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="<?= point_relais_url('bons_livraison/bon/false/' . $type); ?>" class="btn btn-info mright5 pull-left display-block"><?= _l('new_delivery_note'); ?></a>
                        <a href="#" class="btn btn-default pull-right btn-filter"><?= _l('filter'); ?></a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        $columns = array(
                            _l('id'),
                            _l('name'),
                            _l('type'),
                            _l('status'),
                            _l('delivery_note_number_of_delivery_notes'),
                            _l('point_relais'),
                            _l('date_created'),
                            _l('staff'),
                            _l('options')
                        );

                        render_datatable($columns, 'delivery-notes');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail_point_relais(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/point-relais/bons-livraison/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>

<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-info pull-left mright5" data-toggle="modal" data-target="#city_modal"><?= _l('new_city'); ?></a>
                        <?php if (get_option('shipping_cost_by_ville') == 1) { ?>
                        <a href="#" class="btn btn-success pull-left" data-toggle="modal" data-target="#shipping_cost_to_cities_modal"><?= _l('allocation_of_delivery_costs'); ?></a>
                        <?php } ?>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        $columns = array(
                            _l('name')
                        );
                        if (get_option('shipping_cost_by_ville') == 1) {
                            array_push($columns, _l('category_shipping_cost'), _l('shipping_cost'));
                        }
                        array_push($columns, _l('special_fee'), _l('delai') . ' (Heures)', _l('active'), _l('options'));
                        render_datatable($columns, 'villes');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal animated fadeIn" id="city_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?= _l('city_edit_heading'); ?></span>
                    <span class="add-title"><?= _l('city_add_heading'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/villes/ville', array('id' => 'form-city')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_input('name', 'name'); ?>
                        <?php if (get_option('shipping_cost_by_ville') == 1) { ?>
                            <?= render_select('category_shipping_cost', $categories_shipping_cost, array('id', array('name')), 'category_shipping_cost'); ?>
                        <?php } ?>
                        <?= render_input('frais_special', 'special_fee', '', 'number'); ?>
                        <?= render_input('delai', 'delai_infos', '', 'number'); ?>
                        <?= form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button id="submit-form-city" group="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
<div class="modal animated fadeIn" id="shipping_cost_to_cities_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span><?= _l('allocation_of_delivery_costs'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/villes/affectation', array('id' => 'form-affectation-shipping-cost-to-cities')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_select('cities[]', $cities, array('id', array('name')), 'als_cities', '', array('multiple' => true)); ?>
                        <?= render_select('shipping_cost', $categories_shipping_cost, array('id', array('name')), 'category_shipping_cost'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button id="submit-form-shipping-cost-to-cities" group="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/villes/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>

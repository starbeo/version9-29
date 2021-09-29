<?php init_head_point_relais(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/point-relais/includes/alerts.php'); ?>

            <?= form_open($this->uri->uri_string(), array('id' => 'bon-livraison-form')); ?>
            <div class="<?= $class1 ?>">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-12 no-padding">
                            <h4 class="bold"><?= _l('detail_delivery_note'); ?></h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php if (isset($bon_livraison)) { ?>
                            <?= form_hidden('bonlivraison_id', $bon_livraison->id); ?>
                            <?= form_hidden('bonlivraison_status', $bon_livraison->status); ?>
                            <?= form_hidden('ids[]', $bon_livraison->id); ?>
                            <?php $value = (isset($bon_livraison) ? $bon_livraison->nom : ''); ?>
                            <div class="form-group">
                                <label class="control-label"><?= _l('name'); ?></label>
                                <div class="input-group">
                                    <input type="text" disabled class="form-control" value="<?= $value; ?>">
                                    <div class="input-group-addon">
                                        <?= render_btn_copy('input-nom-bl', 'name_of_delivery_note', '', 'input'); ?>
                                    </div>
                                </div>
                            </div>
                            <?= render_input_hidden('input-nom-bl', 'nom', $value); ?>
                        <?php } ?>

                        <?php
                        $attrSelect = array();
                        if (isset($bon_livraison)) {
                            $attrSelect = array('disabled' => 'disabled');
                        }

                        ?>
                        <?php $selected = (isset($bon_livraison) ? $bon_livraison->point_relai_id : ''); ?>
                        <?= render_select('point_relai_id', $points_relais, array('id', array('nom')), 'point_relai', $selected, $attrSelect); ?>

                        <?php $selected = (isset($bon_livraison) ? $bon_livraison->type : $type); ?>
                        <?= render_select('type', $types, array('id', array('name')), 'type', $selected, $attrSelect); ?>

                        <?php if (!isset($bon_livraison)) { ?>
                            <button id="submit" class="btn btn-primary" type="submit" style="width: 100%;"><?= _l('submit'); ?></button>
                        <?php } else { ?>
                            <input id="barcode-douchette" type="text" class="form-control" placeholder="Code d'envoi douchette">
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?= form_close(); ?>

            <?php if (isset($bon_livraison)) { ?>
                <div class="<?= $class2 ?>">
                    <div class="panel_s">
                        <div class="panel-body">
                            <h4 class="bold"><?= _l('historical_delivery_note'); ?></h4>
                            <?php
                            render_datatable(array(
                                '',
                                _l('code_barre'),
                                _l('client'),
                                _l('city'),
                                _l('crbt'),
                                _l('colis_list_fresh'),
                                _l('colis_list_date_pickup'),
                                _l('colis_list_etat'),
                                _l('status')
                                ), 'historique-colis-bon-livraison');

                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="col-md-12">
                                <h4 class="bold"><?= _l('list_colis'); ?> :</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <?php init_colis_bon_livraison_table(); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php init_tail_point_relais(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/point-relais/bons-livraison/bon.js?v=' . version_sources()); ?>"></script>
</body>
</html>

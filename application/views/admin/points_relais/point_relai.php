<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>

            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <ul class="nav nav-tabs no-margin" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#general" aria-controls="general" role="tab" data-toggle="tab">
                                    <?= _l('general'); ?>
                                </a>
                            </li>
                            <?php if (isset($point_relai)) { ?>
                                <li role="presentation">
                                    <a href="#localization" aria-controls="localization" role="tab" data-toggle="tab">
                                        <?= _l('localization'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>

                    <div class="panel-body">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="general">
                                <?= form_open($this->uri->uri_string(), array('id' => 'point-relai-form')); ?>
                                <div class="col-md-4">
                                    <?php $value = (isset($point_relai) ? $point_relai->id : ''); ?>
                                    <input type="hidden" id="point_relai_id" value="<?= $value; ?>">

                                    <?php $selected = (isset($point_relai) ? $point_relai->societe_id : ''); ?>
                                    <?= render_select('societe_id', $societes, array('id', array('name')), 'societe', $selected); ?>
                                    
                                    <?php $value = (isset($point_relai) ? $point_relai->nom : ''); ?>
                                    <?= render_input('nom', 'name', $value); ?>

                                    <?php $selected = (isset($point_relai) ? $point_relai->ville : ''); ?>
                                    <?= render_select('ville', $cities, array('id', array('name')), 'city', $selected); ?>

                                    <?php $value = (isset($point_relai) ? $point_relai->adresse : ''); ?>
                                    <?= render_input('adresse', 'address', $value); ?>
                                </div>
                                <div class="col-md-4">
                                    <?php $value = (isset($point_relai) ? $point_relai->contact : ''); ?>
                                    <?= render_input('contact', 'contact', $value); ?>

                                    <?php $value = (isset($point_relai) ? $point_relai->email : '') ?>
                                    <?= render_input('email', 'email', $value, 'email'); ?>

                                    <?php $value = (isset($point_relai) ? $point_relai->telephone : ''); ?>
                                    <?= render_input('telephone', 'phone_number', $value, 'number'); ?>
                                </div>
                                <div class="col-md-4">
                                    <?php $selected = (isset($point_relai) ? $point_relai->banque_id : ''); ?>
                                    <?= render_select('banque_id', $banks, array('id', array('name')), 'name_of_bank', $selected); ?>

                                    <?php $value = (isset($point_relai) ? $point_relai->rib : ''); ?>
                                    <?= render_input('rib', 'rib', $value); ?>

                                    <?php $value = (isset($point_relai) ? $point_relai->latitude : ''); ?>
                                    <?= render_input('latitude', 'latitude', $value, 'number'); ?>

                                    <?php $value = (isset($point_relai) ? $point_relai->longitude : ''); ?>
                                    <?= render_input('longitude', 'longitude', $value, 'number'); ?>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-primary pull-right" type="submit"><?= _l('submit'); ?></button>
                                </div>
                                <?= form_close(); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="localization">
                                <?php if (isset($point_relai)) { ?>
                                    <div class="row">
                                        <?php if (!empty(get_option('google_api_key'))) { ?>
                                            <div class="col-md-12">
                                                <div id="map" class="bloc_map"></div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="col-md-12">
                                                <h3 class="text-center"><?= _l('setup_google_api_key_customer_map'); ?></h3>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/points-relais/point-relai.js?v=' . version_sources()); ?>"></script>
<?php if (!empty(get_option('google_api_key')) && isset($point_relai) && !is_null($point_relai->latitude) && !is_null($point_relai->longitude)) { ?>
    <script src="<?= site_url('assets/js/admin/points-relais/map.js?v=' . version_sources()); ?>"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= get_option('google_api_key'); ?>&callback=initMap"></script>
<?php } ?>
</body>
</html>

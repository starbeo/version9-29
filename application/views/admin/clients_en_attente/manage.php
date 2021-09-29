<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('societe'),
                            _l('personne_a_contacte'),
                            _l('email'),
                            _l('phone_number'),
                            _l('city'),
                            _l('date_created'),
                            _l('options')
                            ), 'clients-en-attente');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="client-en-attente" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?= _l('detail'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h4 class="bold"><?= _l('societe'); ?> : <span id="societe" class="fw100"></span></h4>
                            <h4 class="bold"><?= _l('personne_a_contacte'); ?> : <span id="contact" class="fw100"></span></h4>
                            <h4 class="bold"><?= _l('email'); ?> : <span id="email" class="fw100"></span></h4>
                            <h4 class="bold"><?= _l('phone_number'); ?> : <span id="telephone" class="fw100"></span></h4>
                            <h4 class="bold"><?= _l('address'); ?> : <span id="adresse" class="fw100"></span></h4>
                            <h4 class="bold"><?= _l('city'); ?> : <span id="ville" class="fw100"></span></h4>
                            <h4 class="bold"><?= _l('affiliation_code'); ?> : <span id="affiliation_code" class="fw100"></span></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <a id="convert-client-en-attente-to-client" href="javascript:void(0)" class="btn btn-primary"><?= _l('convert_to_client'); ?></a>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/clients-en-attente/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>

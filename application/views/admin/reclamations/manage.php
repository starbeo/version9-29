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
                            _l('reclamation_list_shipper'),
                            _l('reclamation_list_object'),
                            _l('reclamation_list_states'),
                            _l('reclamation_list_date_created'),
                            _l('reclamation_list_staff'),
                            _l('reclamation_list_date_traitement'),
                            _l('options')
                            ), 'reclamations');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reclamation" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?= form_open(admin_url('reclamations/reponse_reclamation')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?= _l('send_reponse_reclamation'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <h4><?= _l('reclamation_list_object'); ?> : </h4>
                            <h5 name="objet"></h5>
                            <h4><?= _l('reclamation_list_message'); ?> : </h4>
                            <h5 name="message"></h5>
                            <?= render_textarea('reponse', 'reclamation_list_answer', '', array(), array()); ?>
                            <input type="hidden" name="id">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/reclamations/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>

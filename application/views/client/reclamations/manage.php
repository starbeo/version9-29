<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/client/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="javascript:void(0)" class="btn btn-info pull-left mright5 mbot5" data-toggle="modal" data-target="#reclamation"><?= _l('new_reclamation'); ?></a>
                        <a href="javascript:void(0)" class="btn btn-default pull-right btn-statistique mright5"><?= _l('reclamations_summary'); ?></a>
                        <a href="javascript:void(0)" class="btn btn-default pull-right btn-filtre mright5"><?= _l('filter'); ?></a>
                        <div id="filtre-table" class="panel-body mtop40 display-none">
	                        <div class="col-md-4">
								<?= render_select('f-etat', $etats, array('id',array('name')), 'state'); ?>
							</div>
	                        <div class="col-md-4">
								<?= render_date_input('f-date-created', 'date_created'); ?>
							</div>
	                        <div class="col-md-4">
								<?= render_date_input('f-date-traitement', 'reclamation_list_date_traitement'); ?>
							</div>
	                        <div class="col-md-12 text-right">
                                <button id="filtre-submit" data-table="reclamations" class="btn btn-primary"><?= _l('filter'); ?></button>
                                <button id="filtre-reset" data-table="reclamations" class="btn btn-default"><?= _l('empty'); ?></i></button>
                            </div>
						</div>
                        <div class="visible-xs">
                            <div class="clearfix"></div>
                        </div>
                        <div id="statistique" class="row small-text-span mbot15 display-none">
                            <div class="clearfix"></div>
                            <hr />
                            <div class="col-md-12">
                                <h3 class="text-muted no-margin"><?= _l('reclamations_summary'); ?></h3>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <a href="<?= client_url('reclamations'); ?>">
                                    <h2 class="bold"><?= total_rows('tblreclamations', array('etat' => 0, 'relation_id' => get_expediteur_user_id())); ?></h2>
                                    <span class="bold text-danger mtop15 inline-block"><i class="fa fa-envelope-o"></i> <?= _l('als_reclamations_unprocessed'); ?></span>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-4 text-center border-right">
                                <a href="<?= client_url('reclamations'); ?>">
                                    <h2 class="bold"><?= total_rows('tblreclamations', array('etat' => 1, 'relation_id' => get_expediteur_user_id())); ?></h2>
                                    <span class="bold text-success mtop15 inline-block"><i class="fa fa-envelope-o"></i> <?= _l('als_reclamations_processed'); ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('reclamation_list_object'),
                            _l('reclamation_list_states'),
                            _l('reclamation_list_date_created'),
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
        <?= form_open(client_url('reclamations/reclamation')); ?>
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
                            <?= render_input('objet', 'reclamation_list_object'); ?>
                            <?= render_textarea('message', 'message'); ?>
                            <?= render_textarea('reponse', 'answer', '', array('disabled' => true), array('id' => 'bloc-field-answer'), 'display-none'); ?>
                            <input type="hidden" name="id">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button id="submit-form-reclamation" type="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
</div>
<?php init_tail_client(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/client/reclamations/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>

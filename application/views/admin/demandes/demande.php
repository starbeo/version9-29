<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-offset-3 col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="bold no-margin font-medium">
                            <?= $title; ?>
                        </h4>
                        <hr />
                        <?php if (isset($demande)) { ?>
                            <a href="<?= admin_url('demandes/demande'); ?>" class="btn btn-success pull-right mbot20 display-block"><?= _l('new_request'); ?></a>
                            <div class="clearfix"></div>
                        <?php } ?>
                        <?= form_open_multipart($this->uri->uri_string(), array('id' => 'demande-form')); ?>
                        <?php $selected = (isset($demande) ? $demande->type : ''); ?>
                        <?php $types = array(); ?>
                        <?= render_select('type', $types, array('id', array('name')), 'type', $selected); ?>

                        <?php $selected = (isset($demande) ? $demande->client_id : ''); ?>
                        <?= render_select('client_id', $clients, array('id', array('nom')), 'client', $selected); ?>
                        <?php $selected = (isset($demande) ? $demande->object : ''); ?>
                        <?php $objects = (isset($demande) ? $objets : array()); ?>
                        <?= render_select('object', $objects, array('id', array('name')), 'object', $selected); ?>
                        <?php $value = (isset($demande) ? $demande->departement_name : ''); ?>
                        <?php $displayDepartement = (isset($demande) ? '' : 'display-none'); ?>
                        <?= render_input('department', 'department', $value, 'text', array('disabled' => true), array('id' => 'bloc-input-department'), $displayDepartement); ?>
                        <?php $selected = (isset($demande) ? $demande->rel_id : ''); ?>
                        <?= form_hidden('hidden_rel_id', $selected); ?>
                        <?= render_select('rel_id',array(), array(), 'relation','' , array('multiple' => true), array('id' => 'relation'), 'display-none'); ?>
                        <?php $selected = (isset($demande) ? $demande->priorite : ''); ?>
                        <?php $priorities = array(); ?>
                        <?= render_select('priorite', $priorities, array('id', array('name')), 'priority', $selected); ?>
                        <?php $value = (isset($demande) ? $demande->message : ''); ?>
                        <?= render_textarea('message', 'message', $value); ?>
                        <?php if (!isset($demande) || is_null($demande->attached_piece) || !file_exists('uploads/demandes/' . $demande->id . '/' . $demande->attached_piece)) { ?>
                            <div class="form-group">
                                <label for="attached_piece" class="profile-image"><?= _l('attached_piece'); ?></label>
                                <input type="file" name="attached_piece" class="form-control">
                            </div>
                        <?php } else { ?>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-9">
                                        <label><?= _l('attached_piece'); ?></label>
                                        <p class="mright5">
                                            <i class="<?= get_mime_class($demande->attached_piece_type); ?>"></i> <a href="<?= site_url('download/file/demande/' . $demande->id); ?>" title="Télécharger pièce jointe"> <?= $demande->attached_piece; ?></a>
                                        </p>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <a href="<?= admin_url('demandes/remove_attached_piece_demande/' . $demande->id); ?>" title="Supprimer pièce jointe"><i class="fa fa-remove"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if (isset($demande) && $demande->status == 4 && !empty($demande->notes)) { ?>
                            <?= render_textarea('note', 'note', $demande->notes); ?>
                        <?php } else { ?>
                            <div class="text-right">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary pull-right mtop15"><?= _l('submit'); ?></button>
                                </div>
                            </div>
                        <?php } ?>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/demandes/demande.js?v=' . version_sources()); ?>"></script>
</body>
</html>


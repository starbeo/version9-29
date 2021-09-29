<?php $value = (isset($demande) ? $demande->id : ''); ?>
<button type="button" class="close" style="font-size: 32px !important;" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

<?= form_hidden('demande_id', $value); ?>
<div class="col-md-12 no-padding animated fadeIn">
    <div class="panel_s">
        <div class="panel-body padding-16">
            <ul class="nav nav-tabs no-margin" role="tablist">
                <li role="presentation" class="active">
                    <a href="#tab_demande" aria-controls="tab_demande" role="tab" data-toggle="tab">
                        <?= _l('request'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab_messages" aria-controls="tab_messages" role="tab" data-toggle="tab">
                        <?= _l('messages'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="panel_s">
        <div class="panel-body">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tab_demande">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="ptop10">
                                <span><?= format_type_demande($demande->type); ?></span>

                            </p>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="pull-right">
                                <?php if (has_permission('demandes', '', 'edit') && is_numeric($demande->addedfrom) && $demande->addedfrom == get_staff_user_id()) { ?>
                                    <a class="btn btn-default mright5" href="<?= admin_url('demandes/demande/' . $demande->id); ?>" data-toggle="tooltip" data-placement="bottom" title="<?= _l('edit_string'); ?>"><i class="fa fa-pencil-square-o"></i></a>
                                <?php } ?>
                                <?php if (has_permission('demandes', '', 'delete')) { ?>
                                    <a class="btn btn-danger mright5" href="<?= admin_url('demandes/delete/' . $demande->id); ?>" data-toggle="tooltip" data-placement="bottom" title="<?= _l('delete'); ?>"><i class="fa fa-remove"></i></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <hr />
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="bold no-margin">
                                <b><?= $demande->name; ?></b>
                            </h4>
                            <p class="ptop10">
                                <span><span class="bold text-muted"><?= _l('priority'); ?> :</span> <?= format_priorite_demande($demande->priorite); ?></span>
                            </p>
                            <p>
                                <span><span class="bold text-muted"><?= _l('status'); ?> :</span> <?= format_status_demande($demande->status); ?></span>
                            </p>
                        </div>
                        <div class="col-sm-6 text-right">
                            <h4 class="bold no-margin">
                                <a href="<?= admin_url('expediteurs/expediteur/' . $demande->client_id); ?>" target="_blank"><?= $demande->client->nom; ?></a>
                            </h4>
                            <p class="ptop10">
                                <span><span class="bold text-muted"><?= _l('date_created'); ?> :</span> <?= date(get_current_date_format(), strtotime($demande->datecreated)); ?></span>
                            </p>
                            <p>
                                <span><span class="bold text-muted"><?= _l('rating'); ?> :</span> <?= rating_demande($demande->rating); ?></span>
                            </p>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-6">
                            <p><span class="bold text-muted"><?= _l('departement'); ?> : </span><?= format_departement($demande->department); ?></p>
                            <p><span class="bold text-muted"><?= _l('object'); ?> : </span><b><?= $demande->departement_objet_name; ?></b></p>
                            <?php if (is_numeric($demande->departement_objet_bind) && $demande->departement_objet_bind == 1) { ?>
                                <p>
                                    <span class="bold text-muted"><?= _l('bind_to'); ?> : </span>
                                    <?php if ($demande->departement_objet_bind_to == 'colis') { ?>
                                        <?php if (is_null($demande->rel_id) && !is_null($demande->rels_id)) { ?>
                                            <?php $rels_id =  json_decode($demande->rels_id);  ?>
                                            <?php for($i = 0; $i<count($rels_id);$i++) { ?>
                                                <?php $barcode = get_colis($rels_id[$i], 'code_barre'); ?>
                                                <a href="<?= admin_url('colis/search/' . $barcode) ?>" target="_blank"><?= $barcode; ?></a>
                                            <?php } ?>
                                        <?php } else if (is_null($demande->rels_id) && !is_null($demande->rel_id)) { ?>
                                            <?php $barcode = get_colis($demande->rel_id, 'code_barre'); ?>
                                            <a href="<?= admin_url('colis/search/' . $barcode) ?>" target="_blank"><?= $barcode; ?></a>
                                        <?php } ?>
                                    <?php } else if ($demande->departement_objet_bind_to == 'factures') { ?>
                                        <a href="<?= admin_url('factures/aprecu_facture/' . $demande->rel_id) ?>" target="_blank"><?= get_facture($demande->rel_id, 'nom'); ?></a>
                                    <?php } ?>
                                </p>
                            <?php } ?>
                            <?php if (!empty($demande->message)) { ?>
                                <p class="bold text-muted"><?= _l('message'); ?> :</p>
                                <p><?= $demande->message; ?></p>
                            <?php } ?>
                            <?php if (!empty($demande->attached_piece)) { ?>
                                <h5 class="bold text-muted no-margin"><?= _l('attached_piece'); ?> :</h5>
                                <p class="ptop10">
                                    <i class="<?= get_mime_class($demande->attached_piece_type); ?>"></i> <a href="<?= site_url('download/file/demande/' . $demande->id); ?>"> <?= $demande->attached_piece; ?></a>
                                </p>
                            <?php } ?>
                            <?php if (!empty($demande->notes)) { ?>
                                <p class="bold text-muted"><?= _l('comment'); ?> :</p>
                                <p><?= $demande->notes; ?></p>
                            <?php } ?>
                        </div>
                        <?php if ($demande->status == 4 && empty($demande->notes)) { ?>
                            <div class="col-md-6">
                                <?php $value = (isset($demande) ? $demande->notes : ''); ?>
                                <?= render_textarea('note', 'comment', $value); ?>
                                <div class="form-group text-right">
                                    <button id="add-note-form" type="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_messages">
                    <div class="media">
                        <div class="media-left">
                            <?= staff_profile_image(get_staff_user_id(), array('staff-profile-image-small mright5', 'media-object'), 'small', array('data-toggle' => 'tooltip', 'data-title' => get_staff_full_name(), 'data-placement' => 'right')); ?>
                        </div>
                        <div class="media-body">
                            <?= render_textarea('message_discussion', 'message'); ?>
                            <div id="submit-message-discussion" class="btn btn-primary pull-right"><?= _l('send'); ?></div>
                        </div>
                    </div>
                    <hr />
                    <h3><?= _l('discussions'); ?></h3>
                    <ul id="bloc-discussions-demande"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        // Init discussion
        init_discussion();
    });
</script>




<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/client/includes/alerts.php'); ?>
            <div class="col-md-offset-2 col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="bold no-margin font-medium">
                            <?= $title; ?>
                        </h4>
                        <hr />
                        <?php if (isset($demande)) { ?>
                            <a href="<?= client_url('demandes/demande'); ?>" class="btn btn-success pull-right mbot20 display-block"><?= _l('new_request'); ?></a>
                            <div class="clearfix"></div>
                        <?php } ?>
                        <?= form_open_multipart($this->uri->uri_string(), array('id' => 'demande-form')); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?php $selected = (isset($demande) ? $demande->type : ''); ?>


 <?php $types = array(); ?>
                                <?= render_select('type', $types, array('id', array('name')), 'type', $selected); ?>



                                <?php $selected = (isset($demande) ? $demande->object : ''); ?>
                                <?php $objects = (isset($demande) ? $objets : array()); ?>
                                <?= render_select('object', $objects, array('id', array('name')), 'object', $selected); ?>
                                <?php $value = (isset($demande) ? $demande->departement_name : ''); ?>
                                <?php $displayDepartement = (isset($demande) ? '' : 'display-none'); ?>
                                <?= render_input('department', 'department', $value, 'text', array('disabled' => true), array('id' => 'bloc-input-department'), $displayDepartement); ?>
                                <?php $selected = (isset($demande) ? $demande->priorite : ''); ?>
                                <?php $priorities = array(); ?>
                                <?= render_select('priorite', $priorities, array('id', array('name')), 'priority', $selected); ?>
                                <?php $selected = (isset($demande) ? $demande->rel_id : ''); ?>
                                <?= form_hidden('hidden_rel_id', $selected); ?>
                                <?= render_select('rel_id',array(), array(), 'relation','' , array('multiple' => true), array('id' => 'relation'), 'display-none'); ?>


                            </div>
                            <div class="col-md-6">
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
                                                <a href="<?= client_url('demandes/remove_attached_piece_demande/' . $demande->id); ?>" title="Supprimer pièce jointe"><i class="fa fa-remove"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <?php if (isset($demande) && $demande->status == 4) { ?>
                            <?= render_textarea('note', 'note', $demande->notes); ?>
                            <p class="bold"><?= _l('your_note_for_this_request') ?> :</p>
                            <div class="div">
                                <input type="hidden" id="demande_id" value="<?= $demande->id ?>">
                                <?php $value = (isset($demande) ? $demande->rating : 0); ?>
                                <input type="hidden" name="rating" id="rating" value="<?= $value ?>">
                                <input type="hidden" id="rating_1_hidden" value="1">
                                <img class="image-rating" src="<?= site_url('assets/images/defaults/rating-star1.png'); ?>" onmouseover="changeRating(this.id);" id="rating_1">
                                <input type="hidden" id="rating_2_hidden" value="2">
                                <img class="image-rating" src="<?= site_url('assets/images/defaults/rating-star1.png'); ?>" onmouseover="changeRating(this.id);" id="rating_2">
                                <input type="hidden" id="rating_3_hidden" value="3">
                                <img class="image-rating" src="<?= site_url('assets/images/defaults/rating-star1.png'); ?>" onmouseover="changeRating(this.id);" id="rating_3">
                                <input type="hidden" id="rating_4_hidden" value="4">
                                <img class="image-rating" src="<?= site_url('assets/images/defaults/rating-star1.png'); ?>" onmouseover="changeRating(this.id);" id="rating_4">
                                <input type="hidden" id="rating_5_hidden" value="5">
                                <img class="image-rating" src="<?= site_url('assets/images/defaults/rating-star1.png'); ?>" onmouseover="changeRating(this.id);" id="rating_5">
                            </div>

                            <?php if ($demande->rating == 0) { ?>
                                <div class="col-md-12 text-right">
                                    <div class="form-group">
                                        <div id="send_note" class="btn btn-success"><?= _l('send_note'); ?></div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="text-right">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
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
<?php init_tail_client(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/client/demandes/demande.js?v=' . version_sources()); ?>"></script>
</body>
</html>



<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?= form_open_multipart($this->uri->uri_string(), array('id' => 'marketing-form')); ?>
                        <div class="col-md-5">
                            <h4 class="bold no-margin font-medium">
                                <?= $title; ?>
                            </h4>
                            <hr class="mtop30" />
                            <?php $value = (isset($marketing) ? $marketing->name : ''); ?>
                            <?= render_input('name', 'name', $value); ?>
                            <?php $selected = (isset($marketing) ? $marketing->type : ''); ?>
                            <?= render_select('type', $types, array('id', array('name')), 'type', $selected); ?>
                            <?php $value = (isset($marketing) ? $marketing->rel_id : ''); ?>
                            <input type="hidden" id="rel_id_hidden" value="<?= $value; ?>">
                            <?= render_select('rel_id', array(), array(), 'relation', '', array(), array('id' => 'bloc-select-rel-id'), 'display-none'); ?>
                            <div class="form-group">
                                <p class="bold"><?= _l('notification'); ?></p>
                                <?php $value = (isset($marketing) ? $marketing->notification_by : 'email'); ?>
                                <input type="hidden" id="notification_by" value="<?= $value; ?>">
                                <div class="form-check-inline input-radio-staff">
                                    <label class="form-check-label">
                                        <?php $checked = ($value == 'email' ? 'checked' : ''); ?>
                                        <input type="radio" class="form-check-input mright5" name="notification_by" value="email" <?= $checked; ?>><?= _l('email'); ?>
                                    </label>
                                </div>
                                <div class="form-check-inline input-radio-staff">
                                    <label class="form-check-label">
                                        <?php $checked = ((isset($marketing) && $value == 'sms') ? 'checked' : ''); ?>
                                        <input type="radio" class="form-check-input mright5" name="notification_by" value="sms" <?= $checked; ?>><?= _l('sms'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <?php if (isset($marketing)) { ?>
                                <a href="<?= admin_url('marketing/marketing'); ?>" class="btn btn-info pull-right display-block"><?= _l('new_marketing'); ?></a>
                                <a href="<?= admin_url('marketing/start/' . $marketing->id); ?>" class="btn btn-success pull-right display-block mright5"><?= _l('launch'); ?></a>
                                <div class="clearfix"></div>
                            <?php } ?>
                            <hr />
                            <!-- Notification by email -->   
                            <?php $className = ((isset($marketing) && $marketing->notification_by == 'email') || !isset($marketing) ? '' : 'display-none'); ?>
                            <div id="bloc-notification-by-email" class="<?= $className; ?>">
                                <?php $value = (isset($marketing) ? $marketing->subject : ''); ?>
                                <?= render_input('subject', 'subject', $value); ?>
                                
                                <p class="bold"><?= _l('choice_of_email_type'); ?></p>
                                <?php $value = (isset($marketing) ? $marketing->notification_by_email : 'text'); ?>
                                <input type="hidden" id="notification_by_email" value="<?= $value; ?>">
                                <div class="form-check-inline input-radio-staff">
                                    <label class="form-check-label">
                                        <?php $checked = ($value == 'text' ? 'checked' : ''); ?>
                                        <input type="radio" class="form-check-input mright5" name="notification_by_email" value="text" <?= $checked; ?>><?= _l('text'); ?>
                                    </label>
                                </div>
                                <div class="form-check-inline input-radio-staff">
                                    <label class="form-check-label">
                                        <?php $checked = ((isset($marketing) && $value == 'image') ? 'checked' : ''); ?>
                                        <input type="radio" class="form-check-input mright5" name="notification_by_email" value="image" <?= $checked; ?>><?= _l('image'); ?>
                                    </label>
                                </div>
                                
                                <?php $classNameNotificationEmail = ((isset($marketing) && $marketing->notification_by_email == 'text') || !isset($marketing) ? '' : 'display-none'); ?>
                                <?php $classNameNotificationImage = ((isset($marketing) && $marketing->notification_by_email == 'image') ? '' : 'display-none'); ?>
                                <div id="bloc-notification-by-email-text" class="<?= $classNameNotificationEmail; ?>">
                                    <p class="bold"><?= _l('email'); ?></p>
                                    <?php $value = (isset($marketing) ? $marketing->email : ''); ?>
                                    <?php $this->load->view('admin/editor/template', array('name' => 'email', 'contents' => $value)); ?>
                                </div>
                                <div id="bloc-notification-by-email-image" class="<?= $classNameNotificationImage; ?>">
                                    <?php if (!isset($marketing) || is_null($marketing->image) || !file_exists('uploads/marketing/' . $marketing->id . '/' . $marketing->image)) { ?>
                                        <?= render_input('image', 'image_extension_marketing', '', 'file', array('accept' => 'image/x-jpg,image/jpeg,image/png,image/gif')); ?>
                                    <?php } else { ?>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <label><?= _l('image'); ?></label>
                                                    <p class="mright5">
                                                        <i class="<?= get_mime_class('application/image'); ?>"></i> <a href="<?= site_url('download/file/marketing/' . $marketing->id); ?>" title="Télécharger l'image"> <?= $marketing->image; ?></a>
                                                    </p>
                                                </div>
                                                <div class="col-md-3 text-right">
                                                    <a href="<?= admin_url('marketing/remove_image_marketing/' . $marketing->id); ?>" title="Supprimer l'image"><i class="fa fa-trash text-danger fs20"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <!-- Notification by sms -->   
                            <?php $className = (isset($marketing) && $marketing->notification_by == 'sms' ? '' : 'display-none'); ?>
                            <div id="bloc-notification-by-sms" class="<?= $className; ?>">
                                <?php $value = (isset($marketing) ? $marketing->sms : ''); ?>
                                <?= render_textarea('sms', 'sms', $value, array('rows' => 7, 'maxlength' => 150)); ?>
                                <p><?= _l('note_message_sms'); ?></p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary pull-right"><?= _l('submit'); ?></button>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/marketing/marketing.js?v=' . version_sources()); ?>"></script>
</body>
</html>

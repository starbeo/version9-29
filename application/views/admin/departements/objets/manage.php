<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-info pull-left mright5 mbot5" data-toggle="modal" data-target="#objet_departement_modal"><?= _l('new_objet'); ?></a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('type'),
                            _l('departement'),
                            _l('name'),
                            _l('staff'),
                            _l('date_created'),
                            _l('actions')
                            ), 'objets');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal animated fadeIn" id="objet_departement_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?= _l('objet_edit'); ?></span>
                    <span class="add-title"><?= _l('objet_add'); ?></span>
                </h4>
            </div>
            <?= form_open(admin_url('departements/objet'), array('id' => 'from-objet')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= render_select('type', $types, array('id', array('name')), 'type'); ?>
                        <?= render_input('name', 'objet'); ?>
                        <?= render_yes_no('bind', 0, 'bind_to_a_module'); ?>
                    </div>
                    <div class="col-md-6">
                        <?= render_select('departement_id', $departements, array('id', array('name')), 'departement'); ?>
                        <?= render_select('visibility', $visibilities, array('id', array('name')), 'visibility'); ?>
                        <?= render_select('bind_to', $modules, array('id', array('name')), 'bind_to', '', array(), array('id' => 'bloc-select-bind-to'), 'display-none'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h4><?= _l('people_to_notify') ?></h4>
                        <ul class="nav nav-tabs no-margin" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#staff" aria-controls="staff" role="tab" data-toggle="tab">
                                    <?= _l('staff'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#livreur" aria-controls="livreur" role="tab" data-toggle="tab">
                                    <?= _l('delivery_men'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#client" aria-controls="client" role="tab" data-toggle="tab">
                                    <?= _l('client'); ?>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="staff">
                                <!-- Notification simple -->   
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="send_notification_staff" id="send_notification_staff">
                                    <label for="send_notification_staff"><?= _l('send_notification'); ?></label>
                                </div>
                                <!-- Notification by email -->   
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="send_email_staff" id="send_email_staff">
                                    <label for="send_email_staff"><?= _l('send_an_email'); ?></label>
                                </div>
                                <div id="bloc-notification-by-email-staff" class="display-none">
                                    <?= render_input('subject_email_staff', 'subject'); ?>
                                    <?= render_textarea_avancer('email_staff', 'email_staff', 'email'); ?>
                                </div>
                                <!-- Notification by sms -->   
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="send_sms_staff" id="send_sms_staff">
                                    <label for="send_sms_staff"><?= _l('send_a_text_message'); ?></label>
                                </div>
                                <div id="bloc-notification-by-sms-staff" class="display-none">
                                    <?= render_textarea('sms_staff', 'sms', '', array('rows' => 5, 'maxlength' => 150)); ?>
                                    <p><?= _l('note_message_sms'); ?></p>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="livreur">
                                <!-- Notification simple -->   
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="send_notification_livreur" id="send_notification_livreur">
                                    <label for="send_notification_livreur"><?= _l('send_notification'); ?></label>
                                </div>
                                <!-- Notification by email -->   
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="send_email_livreur" id="send_email_livreur">
                                    <label for="send_email_livreur"><?= _l('send_an_email'); ?></label>
                                </div>
                                <div id="bloc-notification-by-email-livreur" class="display-none">
                                    <?= render_input('subject_email_livreur', 'subject'); ?>
                                    <?= render_textarea_avancer('email_livreur', 'email_livreur', 'email'); ?>
                                </div>
                                <!-- Notification by sms -->   
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="send_sms_livreur" id="send_sms_livreur">
                                    <label for="send_sms_livreur"><?= _l('send_a_text_message'); ?></label>
                                </div>
                                <div id="bloc-notification-by-sms-livreur" class="display-none">
                                    <?= render_textarea('sms_livreur', 'sms', '', array('rows' => 5, 'maxlength' => 150)); ?>
                                    <p><?= _l('note_message_sms'); ?></p>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="client">
                                <!-- Notification simple -->   
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="send_notification_client" id="send_notification_client">
                                    <label for="send_notification_client"><?= _l('send_notification'); ?></label>
                                </div>
                                <!-- Notification by email -->   
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="send_email_client" id="send_email_client">
                                    <label for="send_email_client"><?= _l('send_an_email'); ?></label>
                                </div>
                                <div id="bloc-notification-by-email-client" class="display-none">
                                    <?= render_input('subject_email_client', 'subject'); ?>
                                    <?= render_textarea_avancer('email_client', 'email_client', 'email'); ?>
                                </div>
                                <!-- Notification by sms -->   
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="send_sms_client" id="send_sms_client">
                                    <label for="send_sms_client"><?= _l('send_a_text_message'); ?></label>
                                </div>
                                <div id="bloc-notification-by-sms-client" class="display-none">
                                    <?= render_textarea('sms_client', 'sms', '', array('rows' => 5, 'maxlength' => 150)); ?>
                                    <p><?= _l('note_message_sms'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <hr />
                        <p class="bold text-info"><?= _l('template_merge_fields'); ?></p>
                        <a class="btn btn-default bold" href="#" onclick="slideToggle('.available_merge_fields_container');
                                return false;"><?= _l('available_merge_fields'); ?></a>
                        <div class="clearfix"></div>
                        <div class="row available_merge_fields_container hide">
                            <?php
                            $available_merge_fields;
                            foreach ($available_merge_fields as $field) {
                                foreach ($field as $key => $val) {
                                    if ($key == 'colis' || $key == 'clients' || $key == 'demande' || $key == 'staff' || $key == 'other') {
                                        echo '<div class="col-md-6 merge_fields_col">';
                                        echo '<h5 class="text-info bold">' . ucfirst($key) . '</h5>';
                                        foreach ($val as $_field) {
                                            echo '<p><span class="bold">' . _l($_field['name']) . '</span><span class="pull-right">' . $_field['key'] . '</span></p>';
                                        }
                                        echo '</div>';
                                    }
                                }
                            }

                            ?>
                        </div>
                    </div>
                </div>
                <?= form_hidden('id'); ?>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button id="submit" group="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/departements/objets/manage.js'); ?>"></script>
</body>
</html>

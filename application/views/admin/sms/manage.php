<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-info pull-left mright5" data-toggle="modal" data-target="#sms_modal" data-action="edit"><?= _l('new_sms'); ?></a>
                        <!--a href="#" class="btn btn-default pull-left" data-toggle="modal" data-target="#test_send_sms_modal"><?= _l('test_send_sms'); ?></a-->
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('title'),
                            _l('status'),
                            _l('state'),
                            _l('active'),
                            _l('date_created'),
                            _l('staff'),
                            _l('options'),
                            ), 'sms');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal animated fadeIn" id="sms_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?= _l('sms_edit'); ?></span>
                    <span class="add-title"><?= _l('sms_add'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/sms/sms', array('id' => 'submit-form-sms')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= render_select('status_id', $statuses, array('id', array('name')), 'status'); ?>
                    </div>
                    <div class="col-md-6">
                        <?= render_input('title', 'title'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= render_textarea('message', 'message', '', array('rows' => 6, 'maxlength' => 450)); ?>
                        <p><?= _l('note_message_sms'); ?></p>
                        <?= form_hidden('id'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <p class="bold"><?= _l('send_sms_automatically'); ?></p>
                        <div class="form-check-inline input-radio-staff">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input mr10" id="automatic_sending_yes" name="automatic_sending" value="1" ><?= _l('yes'); ?>
                            </label>
                        </div>
                        <div class="form-check-inline input-radio-staff">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input mr10" id="automatic_sending_no" name="automatic_sending" value="0" ><?= _l('no'); ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <p class="bold"><?= _l('template_merge_fields'); ?></p>
                        <a class="btn btn-default bold" href="#" onclick="slideToggle('.available_merge_fields_container');
                                return false;"><?= _l('available_merge_fields'); ?></a>
                        <div class="clearfix"></div>
                        <div class="row available_merge_fields_container hide">
                            <?php
                            $available_merge_fields;
                            foreach ($available_merge_fields as $field) {
                                foreach ($field as $key => $val) {
                                    if ($key == 'colis' || $key == 'clients') {
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
            </div>
            <div id="sms-footer-form" class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button id="submit" group="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<div class="modal animated fadeIn" id="test_send_sms_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span><?= _l('test_send_sms'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/sms/test', array('id' => 'form-test-send-sms')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_input('phone_number_test', 'phone_number', '', 'number', array('size' => 10)); ?>
                        <?= render_textarea('message_test', 'message'); ?>
                    </div>
                </div>
            </div>
            <div id="sms-footer-form" class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button id="submit-form-test-send-sms" group="submit" class="btn btn-primary"><?= _l('send_an_sms'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/sms/manage.js'); ?>"></script>
</body>
</html>
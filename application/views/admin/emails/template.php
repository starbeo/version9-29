<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?= form_open($this->uri->uri_string(), array('id' => 'email-template-form')); ?>
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?= $title; ?>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <?= render_input('name', 'name', _l($template->name), 'text', array('disabled' => 'disabled')); ?>
                                <?= render_input('subject', 'subject', $template->subject); ?>
                                <?= render_input('fromname', 'fromname', get_option('companyname'), 'text', array('disabled' => 'disabled')); ?>
                                <?= render_input('fromemail', 'fromemail', get_option('smtp_email'), 'text', array('disabled' => 'disabled')); ?>
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="plaintext" <?php
                                    if ($template->plaintext == 1) {
                                        echo 'checked';
                                    }

                                    ?>>
                                    <label><?= _l('send_as_plain_text'); ?></label>
                                </div>
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="disabled" <?php
                                    if ($template->active == 0) {
                                        echo 'checked';
                                    }

                                    ?>>
                                    <label data-toggle="tooltip" title="Disable this email from being sent"><?= _l('disabled'); ?></label>
                                </div>
                                <button type="submit" class="btn btn-primary pull-right"><?= _l('submit'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?= _l('email_message'); ?>
                    </div>
                    <div class="panel-body">
                        <p class="bold"><?= _l('description'); ?></p>
                        <?php $this->load->view('admin/editor/template', array('name' => 'message', 'contents' => $template->message)); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel_s">
                            <div class="panel-heading">
                                <?= _l('template_merge_fields'); ?>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <a class="btn btn-default bold" href="#" onclick="slideToggle('.available_merge_fields_container');
                                                return false;"><?= _l('available_merge_fields'); ?></a>
                                        <div class="clearfix"></div>
                                        <div class="row available_merge_fields_container hide">
                                            <?php
                                            $available_merge_fields;
                                            foreach ($available_merge_fields as $field) {
                                                foreach ($field as $key => $val) {
                                                    echo '<div class="col-md-6 merge_fields_col">';
                                                    echo '<h5 class="bold text-info">' . ucfirst($key) . ' :</h5>';
                                                    foreach ($val as $_field) {
                                                        foreach ($_field['available'] as $_available) {
                                                            if ($_available == $template->type) {
                                                                echo '<p><b>' . _l($_field['name']) . '</b><span class="pull-right">' . $_field['key'] . '</span></p>';
                                                            }
                                                        }
                                                    }
                                                    echo '</div>';
                                                }
                                            }

                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/emails/template.js'); ?>"></script>
</body>
</html>

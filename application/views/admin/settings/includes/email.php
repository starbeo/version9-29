<div class="row">
    <div class="col-md-3">
        <h4><?= _l('smtp_settings'); ?></h4>
        <p class="text-muted"><?= _l('settings_smtp'); ?></p>
        <hr />
        <?= render_input('smtp_email', 'smtp_email', get_option('smtp_email'), 'text', array('disabled' => 'disabled')); ?>
        <hr />
        <h4><?= _l('send_test_email_heading'); ?></h4>
        <p class="text-muted"><?= _l('send_test_email_subheading'); ?></p>
        <hr />
        <?= render_input('test_email', 'send_test_email_string'); ?>
        <div class="form-group">
            <button type="button" class="btn btn-info test_email"><?= _l('test'); ?></button>
        </div>
    </div>
    <div class="col-md-5">
        <?= render_textarea_avancer('email_signature', 'settings[email_signature]', 'email_signature', get_option('email_signature')); ?>
    </div>
    <div class="col-md-4">
        <p class="bold"><?= _l('available_merge_fields'); ?></p>
        <div class="row available_merge_fields_container">
            <?php
            $available_merge_fields;
            foreach ($available_merge_fields as $field) {
                foreach ($field as $key => $val) {
                    if ($key == 'other') {
                        echo '<div class="col-md-12">';
                        foreach ($val as $key1 => $_field) {
                            if($_field['name'] != 'email_signature') {
                                echo '<p><span class="bold">' . _l($_field['name']) . '</span>' . render_btn_copy('row-tag-' . $key . '-' . $key1, 'tag', 'pull-right mleft5') . '<span id="row-tag-' . $key . '-' . $key1.'" class="pull-right">' . $_field['key'] . '</span></p>';
                            }
                        }
                        echo '</div>';
                    }
                }
            }

            ?>
        </div>
    </div>
</div>

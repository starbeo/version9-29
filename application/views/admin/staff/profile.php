<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?= $title; ?>
                    </div>
                    <div class="panel-body">
                        <?= form_open_multipart($this->uri->uri_string(), array('id' => 'staff_profile_table')); ?>
                        <?php if ($_staff->profile_image == NULL) { ?>
                            <div class="form-group">
                                <label for="profile_image" class="profile-image"><?= _l('staff_edit_profile_image'); ?></label>
                                <input type="file" name="profile_image" class="form-control" id="profile_image">
                            </div>
                        <?php } ?>
                        <?php if ($_staff->profile_image != NULL) { ?>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-9">
                                        <?= staff_profile_image($_staff->staffid, array('img', 'img-responsive', 'staff-profile-image-thumb'), 'thumb'); ?>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <a href="<?= admin_url('staff/remove_staff_profile_image'); ?>"><i class="fa fa-remove"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <label for="firstname" class="control-label"><?= _l('staff_add_edit_firstname'); ?></label>
                            <input type="text" class="form-control" name="firstname" value="<?php
                            if (isset($member)) {
                                echo $member->firstname;
                            }

                            ?>">
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="control-label"><?= _l('staff_add_edit_lastname'); ?></label>
                            <input type="text" class="form-control" name="lastname" value="<?php
                            if (isset($member)) {
                                echo $member->lastname;
                            }

                            ?>">
                        </div>
                        <div class="form-group">
                            <label for="email" class="control-label"><?= _l('staff_add_edit_email'); ?></label>
                            <input type="email" class="form-control" disabled="true" value="<?= $member->email; ?>">
                        </div>
                        <div class="form-group">
                            <label for="phonenumber" class="control-label"><?= _l('staff_add_edit_phonenumber'); ?></label>
                            <input type="text" class="form-control" name="phonenumber" value="<?php
                            if (isset($member)) {
                                echo $member->phonenumber;
                            }

                            ?>">
                        </div>
                        <div class="form-group">
                            <label for="default_language" class="control-label"><?= _l('localization_default_language'); ?></label>
                            <select name="default_language" id="default_language" class="form-control selectpicker">
                                <option value=""><?= _l('system_default_string'); ?></option>
                                <?php
                                foreach (list_folders(APPPATH . 'language') as $language) {
                                    $selected = '';
                                    if (isset($member)) {
                                        if ($member->default_language == $language) {
                                            $selected = 'selected';
                                        }
                                    }

                                    ?>
                                    <option value="<?= $language; ?>" <?= $selected; ?>><?= ucfirst($language); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary pull-right"><?= _l('submit'); ?></button>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?= _l('staff_edit_profile_change_your_password'); ?>
                    </div>
                    <div class="panel-body">
                        <?= form_open('admin/staff/change_password_profile', array('id' => 'staff_password_change_form')); ?>
                        <div class="form-group">
                            <label for="oldpassword" class="control-label"><?= _l('staff_edit_profile_change_old_password'); ?></label>
                            <input type="password" class="form-control" name="oldpassword" id="oldpassword">
                        </div>
                        <div class="form-group">
                            <label for="newpassword" class="control-label"><?= _l('staff_edit_profile_change_new_password'); ?></label>
                            <input type="password" class="form-control" id="newpassword" name="newpassword">
                        </div>
                        <div class="form-group">
                            <label for="newpasswordr" class="control-label"><?= _l('staff_edit_profile_change_repet_new_password'); ?></label>
                            <input type="password" class="form-control" id="newpasswordr" name="newpasswordr">
                        </div>
                        <button type="submit" class="btn btn-primary pull-right"><?= _l('submit'); ?></button>
                        <?= form_close(); ?>
                    </div>
                    <?php if (isset($member->last_password_change) && !is_null($member->last_password_change)) { ?>
                        <div class="panel-footer">
                            <?= _l('staff_add_edit_password_last_changed'); ?>: <?= time_ago($member->last_password_change); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/staff/profile.js?v=' . version_sources()); ?>"></script>
</body>
</html>

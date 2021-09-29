<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?= _l('staff_profile_string'); ?>
                    </div>
                    <div class="panel-body">
                        <div class="button-group mtop10 pull-right">
                            <?php if (has_permission('staff', '', 'view')) { ?>
                                <a href="<?= admin_url('staff/member/' . $staff_p->staffid); ?>" class="btn btn-default btn-xs"><i class="fa fa-pencil-square"></i></a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <?php if (is_admin($staff_p->staffid)) { ?>
                            <p class="pull-right text-info"><?= _l('staff_admin_profile'); ?></p>
                        <?php } ?>
                        <?= staff_profile_image($staff_p->staffid, array('staff-profile-image-thumb'), 'thumb'); ?>
                        <div class="profile mtop20 display-inline-block">
                            <h4><?= $staff_p->firstname . ' ' . $staff_p->lastname; ?></h4>
                            <small class="display-block"><i class="fa fa-envelope"></i> <?= $staff_p->email; ?></small>
                            <?php if ($staff_p->phonenumber != '') { ?>
                                <small><i class="fa fa-phone-square"></i> <?= $staff_p->phonenumber; ?></small>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($staff_p->staffid == get_staff_user_id()) { ?>
                <div class="col-md-6">
                    <div class="panel_s">
                        <div class="panel-heading">
                            <?= _l('staff_profile_notifications'); ?>
                        </div>
                        <div class="panel-body">
                            <?= form_hidden('total_pages', $total_pages); ?>
                            <div id="notifications"></div>
                            <a href="#" class="btn btn-primary loader"><?= _l('load_more'); ?></a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/staff/myprofile.js?v=' . version_sources()); ?>"></script>
</body>
</html>

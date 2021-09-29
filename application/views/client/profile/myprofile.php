<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/client/includes/alerts.php'); ?>
            <div class="col-md-4">
                <div class="panel_s">
                    <div class="panel-heading">
                        <?= _l('profile'); ?>
                    </div>
                    <div class="panel-body">
                        <div class="button-group mtop10 pull-right">
                            <a href="<?= client_url('profile/edit'); ?>" class="btn btn-default btn-xs"><i class="fa fa-pencil-square"></i></a>
                        </div>
                        <div class="clearfix"></div>
                        <?= client_logo($client->id, array('staff-profile-image-thumb'), 'thumb'); ?>
                        <div class="profile mtop20 display-inline-block">
                            <h4><?= $client->nom; ?></h4>
                            <?php if (!empty($client->email)) { ?>
                                <small class="display-block"><i class="fa fa-envelope"></i> <?= $client->email; ?></small>
                            <?php } ?>
                            <?php if (!empty($client->phonenumber)) { ?>
                                <small><i class="fa fa-phone-square"></i> <?= $staff_p->phonenumber; ?></small>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <?php if (isset($commerciale) || isset($account_manager) || isset($livreur)) { ?>
                    <div class="panel-heading">
                        <?= _l('agent'); ?>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <?php if (isset($commerciale)) { ?>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h3><?= _l('commercial'); ?></h4>
                                            <?= staff_profile_image($commerciale->staffid, array('staff-profile-image-thumb'), 'thumb'); ?>
                                    </div>
                                    <div class="profile mtop20 display-inline-block">
                                        <h5 class="bold"><?= ucwords($commerciale->firstname) . ' ' . ucwords($commerciale->lastname); ?></h4>
                                            <?php if (!empty($commerciale->email)) { ?>
                                                <small class="display-block"><i class="fa fa-envelope"></i> <?= $commerciale->email; ?></small>
                                            <?php } ?>
                                            <?php if (!empty($commerciale->phonenumber)) { ?>
                                                <small><i class="fa fa-phone-square"></i> <?= $commerciale->phonenumber; ?></small>
                                            <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (isset($account_manager)) { ?>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h3><?= _l('account_manager'); ?></h4>
                                            <?= staff_profile_image($account_manager->staffid, array('staff-profile-image-thumb'), 'thumb'); ?>
                                    </div>
                                    <div class="profile mtop20 display-inline-block">
                                        <h5 class="bold"><?= ucwords($account_manager->firstname) . ' ' . ucwords($account_manager->lastname); ?></h4>
                                            <?php if (!empty($account_manager->email)) { ?>
                                                <small class="display-block"><i class="fa fa-envelope"></i> <?= $account_manager->email; ?></small>
                                            <?php } ?>
                                            <?php if (!empty($account_manager->phonenumber)) { ?>
                                                <small><i class="fa fa-phone-square"></i> <?= $account_manager->phonenumber; ?></small>
                                            <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (isset($livreur)) { ?>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h3><?= _l('delivery_men'); ?></h4>
                                            <?= staff_profile_image($livreur->staffid, array('staff-profile-image-thumb'), 'thumb'); ?>
                                    </div>
                                    <div class="profile mtop20 display-inline-block">
                                        <h5 class="bold"><?= ucwords($livreur->firstname) . ' ' . ucwords($livreur->lastname); ?></h4>
                                            <?php if (!empty($livreur->email)) { ?>
                                                <small class="display-block"><i class="fa fa-envelope"></i> <?= $livreur->email; ?></small>
                                            <?php } ?>
                                            <?php if (!empty($livreur->phonenumber)) { ?>
                                                <small><i class="fa fa-phone-square"></i> <?= $livreur->phonenumber; ?></small>
                                            <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php if ($client->id == get_expediteur_user_id()) { ?>
                <div class="col-md-8">
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
<?php init_tail_client(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/client/profile/myprofile.js?v=' . version_sources()); ?>"></script>
</body>
</html>

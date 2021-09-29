<?php init_head_point_relais(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            include_once(APPPATH . 'views/point-relais/includes/alerts.php');
            //Affichage du bloc statistique
            include_once(APPPATH . 'views/point-relais/dashboard/statistique-home.php');

            ?>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body home-activity">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#home_tab_activity" aria-controls="home_tab_activity" role="tab" data-toggle="tab">
                                    <?= _l('home_latest_activity'); ?>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content home-activity-wrap">
                            <div role="tabpanel" class="tab-pane active" id="home_tab_activity">
                                <a href="<?= point_relais_url('utilities/activities_log'); ?>" class="btn btn-info btn-sm"><?= _l('home_widget_view_all'); ?></a>
                                <div class="clearfix"></div>
                                <hr />
                                <ul class="latest-activity">
                                    <?php foreach ($activities as $log) { ?>
                                        <div class="media">
                                            <?php if (is_numeric($log['staffid'])) { ?>
                                                <div class="media-left">
                                                    <a href="<?= point_relais_url('profile/' . $log["staffid"]); ?>">
                                                        <?= staff_profile_image($log['staffid'], array('staff-profile-image-small', 'media-object'), 'small', array('data-toggle' => 'tooltip', 'data-title' => get_staff_full_name($log['staffid']), 'data-placement' => 'right')); ?>&nbsp;
                                                    </a>
                                                </div>
                                            <?php } ?>
                                            <div class="media-body text-purple">
                                                <?= $log['description']; ?><small class="text-muted display-block"><?= date('d/m/Y H:i:s', strtotime($log['date'])); ?></small>
                                                <hr />
                                            </div>
                                        </div>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail_point_relais(); ?>
<script src="<?= site_url('assets/js/point-relais/dashboard/home.js?v=' . version_sources()); ?>"></script>
</body>
</html>

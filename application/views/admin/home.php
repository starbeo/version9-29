<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            
            <?php if (is_admin()) { ?>
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="row home-summary">
                                <div class="col-md-2 col-xs-6 text-center border-right">
                                    <a href="<?= admin_url('utilities/connected_customer'); ?>">
                                        <h2 class="bold no-margin"><?= total_rows('tblnumberofauthentication', 'date_created LIKE "' . date("Y-m-d", strtotime(date("Y-m-d"))) . '%"'); ?></h2>
                                        <span class="bold text-success mtop15 inline-block"><i class="fa fa-check"></i> <?= _l('number_of_authentication'); ?></span>
                                    </a>
                                </div>
                                <div class="col-md-2 col-xs-6 text-center border-right">
                                    <h2 class="bold no-margin"><?= total_rows('tblcolis', array('status_id' => 2, 'date_livraison' => date('Y-m-d'))); ?></h2>
                                    <span class="bold text-success mtop15 inline-block"><i class="fa fa-check"></i> <?= _l('dashboard_total_colis'); ?></span>
                                </div>
                                <div class="col-md-3 col-xs-6 text-center border-right">
                                    <h2 class="bold no-margin"><?= total_frais_colis(); ?></h2>
                                    <span class="bold text-success mtop15 inline-block"><i class="fa fa-check"></i> <?= _l('dashboard_frais_colis'); ?></span>
                                </div>
                                <div class="col-md-3 col-xs-6 text-center border-right">
                                    <h2 class="bold no-margin"><?= total_price_colis(); ?></h2>
                                    <span class="bold text-success mtop15 inline-block"><i class="fa fa-check"></i> <?= _l('dashboard_crbt_colis'); ?></span>
                                </div>
                                <div class="col-md-2 col-xs-6 text-center border-right">
                                    <h2 class="bold no-margin"><?= total_parrainage(); ?></h2>
                                    <span class="bold text-success mtop15 inline-block"><i class="fa fa-check"></i> <?= _l('dashboard_total_parrainage'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            
            <?php
            //Affichage du bloc statistique
            include_once(APPPATH . 'views/admin/includes/statistique-home.php');
            ?>

            <?php
            $col = 12;
            $class = '-small';
            if (is_admin()) {
                $col = 5;
                $class = '';
                ?>
                <div class="col-md-7">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <?= render_select('client_id', $clients, array('id', array('nom')), 'als_expediteur'); ?>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="report-time">
                                        <label class="form-label"><?= _l('report_period'); ?></label>
                                        <select class="selectpicker" name="months-report" data-width="100%">
                                            <option value=""><?= _l('report_sales_months_all_time'); ?></option>
                                            <option value="6"><?= _l('report_sales_months_six_months'); ?></option>
                                            <option value="12"><?= _l('report_sales_months_twelve_months'); ?></option>
                                            <option value="custom" selected><?= _l('report_sales_months_custom'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div id="date-range" class="form-group animated">
                                    <div class="col-md-6">
                                        <label for="report-from" class="control-label"><?= _l('report_sales_from_date'); ?></label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" id="report-from" name="report-from" value="<?= date("d/m/Y", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))); ?>">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="report-to" class="control-label"><?= _l('report_sales_to_date'); ?></label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" id="report-to" name="report-to" value="<?= date("d/m/Y", mktime(0, 0, 0, date("m") + 1, 0, date("Y"))); ?>">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div id="wait-chart" class="col-md-12 text-center">
                                    <img class="width50 mtop10" src="<?= site_url('assets/images/wait.gif'); ?>" />
                                    <h6 class="bold">Patientez pendant le chargement du contenu.</h6>
                                </div>
                                <canvas id="chart" class="animated fadeIn"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <?= render_select('livreur_id', $livreurs, array('staffid', array('firstname', 'lastname')), 'delivery_man'); ?>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" id="report-time-1">
                                        <label class="form-label"><?= _l('report_period'); ?></label>
                                        <select class="selectpicker" name="months-report-1" data-width="100%">
                                            <option value="this_day"><?= _l('report_colis_this_day'); ?></option>
                                            <option value="yesterday"><?= _l('report_colis_yesterday'); ?></option>
                                            <option value="this_week"><?= _l('report_colis_this_week'); ?></option>
                                            <option value="last_week"><?= _l('report_colis_last_week'); ?></option>
                                            <option value="6"><?= _l('report_sales_months_six_months'); ?></option>
                                            <option value="12"><?= _l('report_sales_months_twelve_months'); ?></option>
                                            <option value="custom"><?= _l('report_sales_months_custom'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div id="date-range-1" class="form-group hide animated">
                                    <div class="col-md-6">
                                        <label for="report-from-1" class="control-label"><?= _l('report_sales_from_date'); ?></label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" id="report-from-1" name="report-from-1">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="report-to-1" class="control-label"><?= _l('report_sales_to_date'); ?></label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control datepicker" disabled="disabled" id="report-to-1" name="report-to-1">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar calendar-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div id="wait-chart-1" class="col-md-12 text-center">
                                    <img class="width50 mtop10" src="<?= site_url('assets/images/wait.gif'); ?>" />
                                    <h6 class="bold">Patientez pendant le chargement du contenu.</h6>
                                </div>
                                <canvas id="chart-1" class="animated fadeIn"></canvas>
                            </div>

                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="col-md-<?= $col; ?>">
                <div class="panel_s">
                    <div class="panel-body home-activity<?= $class; ?>">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#home_tab_task" aria-controls="home_tab_task" role="tab" data-toggle="tab">
                                    <?= _l('home_my_tasks'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#home_tab_activity" aria-controls="home_tab_activity" role="tab" data-toggle="tab">
                                    <?= _l('home_latest_activity'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#home_tab_appels" aria-controls="home_tab_appels" role="tab" data-toggle="tab">
                                    <?= _l('historiques_des_appels'); ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#home_tab_latest_activity_sms" aria-controls="home_tab_latest_activity_sms" role="tab" data-toggle="tab">
                                    <?= _l('latest_activity_sms'); ?>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content home-activity-wrap<?= $class; ?>">
                            <div role="tabpanel" class="tab-pane active" id="home_tab_task">
                                <a href="<?= admin_url('supports'); ?>" class="btn btn-info btn-sm"><?= _l('home_widget_view_all'); ?></a>
                                <div class="clearfix"></div>
                                <hr />
                                <ul class="latest-activity">
                                    <?php
                                    foreach ($taches as $tache) {
                                        if ($tache['finished'] == 1) {
                                            $finished = '<i class="fa fa-check task-icon task-finished-icon"></i>';
                                        } else {
                                            $finished = '<i class="fa fa-check task-icon task-unfinished-icon"></i>';
                                        }
                                    ?>
                                        <div class="widget-task">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?= $finished; ?>
                                                    <a class="bold" href="<?= admin_url('supports/support/' . $tache['id']); ?>"><?= $tache['name']; ?></a>
                                                    <div class="clearfix mtop10"></div>
                                                    <?= substr(strip_tags($tache['description']), 0, 150) . '...' ?>
                                                </div>
                                                <div class="col-md-12 mtop10">
                                                    <span class="label <?php
                                                    if ($tache['nbr_checklists'] == 0) {
                                                        echo 'label-default-light';
                                                    } else {
                                                        echo 'label-success';
                                                    }

                                                    ?> pull-left mright5">
                                                        <i class="fa fa-th-list"></i> <?= $tache['nbr_checklists']; ?>
                                                    </span>
                                                    <span class="label label-default-light pull-left mright5">
                                                        <i class="fa fa-paperclip"></i> <?= $tache['nbr_attachements']; ?>
                                                    </span>
                                                    <span class="label label-default-light pull-left">
                                                        <i class="fa fa-comments"></i> <?= $tache['nbr_comments']; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <hr />
                                        </div>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="home_tab_activity">
                                <a href="<?= admin_url('utilities/activity_log'); ?>" class="btn btn-info btn-sm"><?= _l('home_widget_view_all'); ?></a>
                                <div class="clearfix"></div>
                                <hr />
                                <ul class="latest-activity">
                                    <?php foreach ($activities as $log) { ?>
                                        <div class="media">
                                            <?php if ($log['staffid'] != 0) { ?>
                                                <div class="media-left">
                                                    <a href="<?= admin_url('profile/' . $log["staffid"]); ?>">
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
                            <div role="tabpanel" class="tab-pane" id="home_tab_appels">
                                <a href="<?= admin_url('appels/livreurs'); ?>" class="btn btn-info btn-sm"><?= _l('home_widget_view_all'); ?></a>
                                <div class="clearfix"></div>
                                <hr />
                                <ul class="latest-activity">
                                    <?php foreach ($appels as $appel) { ?>
                                        <div class="media">
                                            <?php if ($appel['livreur_id'] != 0) { ?>
                                                <div class="media-left">
                                                    <a href="<?= admin_url('profile/' . $appel['livreur_id']); ?>">
                                                        <?= staff_profile_image($appel['livreur_id'], array('staff-profile-image-small', 'media-object'), 'small', array('data-toggle' => 'tooltip', 'data-title' => get_staff_full_name($appel['livreur_id']), 'data-placement' => 'right')); ?>&nbsp;
                                                    </a>
                                                </div>
                                            <?php } ?>
                                            <div class="media-body text-purple">
                                                Code d'envoi : <a href="<?= admin_url('colis/search/' . $appel['code_barre']); ?>" target="_blank"><b><?= $appel['code_barre']; ?></b></a>
                                                <br>
                                                Client : <a href="<?= admin_url('expediteurs/expediteur/' . $appel['client_id']); ?>" target="_blank"><b><?= ucfirst($appel['nom']); ?></b></a>
                                                <small class="text-muted display-block"><?= date('d/m/Y H:i:s', strtotime($appel['date_created'])); ?></small>
                                                <hr />
                                            </div>
                                        </div>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="home_tab_latest_activity_sms">
                                <a href="<?= admin_url('utilities/activities_log_sms'); ?>" class="btn btn-info btn-sm"><?= _l('home_widget_view_all'); ?></a>
                                <div class="clearfix"></div>
                                <hr />
                                <ul class="latest-activity">
                                    <?php foreach ($activities_log_sms as $activity) { ?>
                                        <div class="media">
                                            <div class="media-left">
                                                <i class="fa fa-envelope-o"></i>
                                            </div>
                                            <div class="media-body">
                                                <p class="text-muted no-margin"><?= date(get_current_date_time_format(), strtotime($activity['date'])); ?></p>
                                                <p class="no-margin"><span class="text-purple"><?= _l('code_barre') ?> :</span> <a href="<?= admin_url('colis/search/' . $activity['code_barre']); ?>" target="_blank"><b><?= $activity['code_barre']; ?></b></a></p>
                                                <p class="no-margin"><span class="text-purple"><?= _l('status') ?> :</span> <?= format_status_colis($activity['status_id']) ?></p>
                                                <p class="no-margin"><span class="text-purple"><?= _l('sms') ?> :</span> <?= $activity['sms'] ?></p>
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

            <?php if (is_admin()) { ?>
            <?php if(!empty(get_option('google_api_key'))){ ?>
                <div class="col-md-12">
                    <div id="map" class="bloc_map"></div>
                </div>
            <?php } else { ?>
                <div class="col-md-12">
                    <h3 class="text-center"><?= _l('setup_google_api_key_customer_map'); ?></h3>
                </div>
            <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?= site_url('assets/js/admin/home/home.js?v=' . version_sources()); ?>"></script>
<script src="<?= site_url('assets/js/admin/home/colis-reports.js?v=' . version_sources()); ?>"></script>
<script src="<?= site_url('assets/js/admin/home/fresh-crbt-reports.js?v=' . version_sources()); ?>"></script>
<?php if(!empty(get_option('google_api_key')) && is_admin()){ ?>
    <script src="<?= site_url('assets/js/admin/staff/map.js?v=' . version_sources()); ?>"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= get_option('google_api_key'); ?>&callback=initMap"></script>
<?php } ?>
</body>
</html>

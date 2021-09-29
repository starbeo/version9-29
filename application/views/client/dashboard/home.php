<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            include_once(APPPATH . 'views/client/includes/alerts.php');
            //Affichage du bloc statistique
            include_once(APPPATH . 'views/client/dashboard/statistique-home.php');

            ?>

            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <?= form_hidden('client_id', get_expediteur_user_id()); ?>
                                <div class="form-group" id="report-time">
                                    <label class="form-label"><?= _l('report_period'); ?></label>
                                    <select class="selectpicker" name="months-report" data-width="100%">
                                        <option value="" selected><?= _l('report_sales_months_all_time'); ?></option>
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
                                        <input type="text" class="form-control datepicker" id="report-from" name="report-from" value="<?= date("d/m/Y", mktime(0, 0, 0, date("m") - 2, 1, date("Y"))); ?>">
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
            </div>

            <?php if(count($sliders) > 0) { ?>
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div id="myCarousel" class="carousel slide" data-ride="carousel">
                            <!-- Indicators -->
                            <ol class="carousel-indicators">
                                <?php foreach ($sliders as $key => $slider) { ?>
                                <li data-target="#myCarousel" data-slide-to="<?= $key ?>" class="<?= ($key == 0 ? 'active' : '') ?>"></li>
                                <?php } ?>
                            </ol>
                            <!-- Wrapper for slides -->
                            <div class="carousel-inner">
                                <?php foreach ($sliders as $key => $slider) { ?>
                                    <?php $active = ($key == 0 ? 'active' : ''); ?>
                                    <div class="item <?= $active ?>">
                                        <img class="img-slider" src="<?= site_url('uploads/sliders/' . $slider['id'] . '/' . $slider['file']); ?>" alt="<?= $slider['name'] ?>">
                                    </div>
                                <?php } ?>
                            </div>
                            <!-- Left and right controls -->
                            <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
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
                            <li role="presentation">
                                <a href="#home_tab_latest_activity_sms" aria-controls="home_tab_latest_activity_sms" role="tab" data-toggle="tab">
                                    <?= _l('latest_activity_sms'); ?>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content home-activity-wrap">
                            <div role="tabpanel" class="tab-pane active" id="home_tab_activity">
                                <a href="<?= client_url('utilities/activities_log'); ?>" class="btn btn-info btn-sm"><?= _l('home_widget_view_all'); ?></a>
                                <div class="clearfix"></div>
                                <hr />
                                <ul class="latest-activity">
                                    <?php foreach ($activities as $log) { ?>
                                        <div class="media">
                                            <?php if (is_numeric($log['clientid'])) { ?>
                                                <div class="media-left">
                                                    <a href="<?= client_url('profile/' . $log["clientid"]); ?>">
                                                        <?= client_logo($log['clientid'], array('staff-profile-image-small', 'media-object'), 'small', array('data-toggle' => 'tooltip', 'data-title' => get_client_full_name(), 'data-placement' => 'right')); ?>&nbsp;
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
                            <div role="tabpanel" class="tab-pane" id="home_tab_latest_activity_sms">
                                <a href="<?= client_url('utilities/activities_log_sms'); ?>" class="btn btn-info btn-sm"><?= _l('home_widget_view_all'); ?></a>
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
                                                <p class="no-margin"><span class="text-purple"><?= _l('code_barre') ?> :</span> <a href="<?= client_url('colis/search/' . $activity['code_barre']); ?>" target="_blank"><b><?= $activity['code_barre']; ?></b></a></p>
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
        </div>
    </div>
</div>

<?php if($mhd == '0') { ?>
<?php include_once(APPPATH . 'views/client/dashboard/changepass.php'); } ?>
<?= $mhd; ?>

<?php init_tail_client(); ?>
<script src="<?= site_url('assets/js/client/dashboard/home.js?v=' . version_sources()); ?>"></script>
<script src="<?= site_url('assets/js/client/dashboard/colis-reports.js?v=' . version_sources()); ?>"></script>
</body>
</html>

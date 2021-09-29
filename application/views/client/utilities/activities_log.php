<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/client/includes/alerts.php'); ?>
            <div id="filter-table" class="bloc-filter-table display-none">
                <div class="bloc-content-filter">
                    <i id="icon-remove-bloc-filter" class="fa fa-remove" title="<?= _l('close_menu') ?>"></i>
                    <h3 class="title-bloc-filter"><?= _l('filter') ?></h3>
                    <?= render_date_input('f-date-created-start', 'date_created_start', '', array(), array(), 'mbot10'); ?>
                    <?= render_date_input('f-date-created-end', 'date_created_end', '', array(), array(), 'mbot10'); ?>
                    <div id="filter-submit" class="btn btn-success width100p mbot5" data-table="activities-log"><?= _l('filter'); ?></div>
                    <div id="filter-reset" class="btn btn-info width100p mbot5" data-table="activities-log"><?= _l('reset'); ?></i></div>
                    <div id="filter-close" class="btn btn-default width100p mbot5"><?= _l('close'); ?></i></div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="javascript:void(0)" class="btn btn-default pull-right btn-filter mright5"><?= _l('filter'); ?></a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php
                        render_datatable(array(
                            _l('description'),
                            _l('date_created')
                            ), 'activities-log');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail_client(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/client/utilities/activities_log.js?v=' . version_sources()); ?>"></script>
</body>
</html>

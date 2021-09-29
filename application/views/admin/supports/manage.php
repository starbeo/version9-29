<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12" id="small-table">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if(has_permission('supports', '', 'create')){ ?>
                        <a href="<?= admin_url('supports/support'); ?>" class="btn btn-info pull-left"><?= _l('new_support'); ?></a>
                        <?php } ?>
                        <a href="#" class="btn btn-default pull-right mleft10" onclick="toggle_small_view('.table-supports','#support'); return false;" data-toggle="tooltip" title="<?= _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-bars"></i></a>
                        <div class="btn-group pull-right" data-toggle="tooltip" title="Sort By">
                            <button type="button" class="btn btn-default mleft10 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-sort"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" onclick="supports_sort_by('finished'); return false;">
                                        <?= _l('support_sort_finished'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" onclick="supports_sort_by('priority'); return false;"><?= _l('support_sort_priority'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" onclick="supports_sort_by('startdate'); return false;">
                                        <?= _l('support_sort_startdate'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" onclick="supports_sort_by('dateadded'); return false;">
                                        <?= _l('support_sort_dateadded'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" onclick="supports_sort_by('duedate'); return false;">
                                        <?= _l('support_sort_duedate'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="btn-group pull-right" data-toggle="tooltip" title="View Supports">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-list"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" onclick="dt_supports_custom_view(this, '','.table-supports'); return false;">
                                        <?= _l('support_list_all'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" onclick="dt_supports_custom_view(this, 'finished','.table-supports'); return false;">
                                        <?= _l('support_list_finished'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" onclick="dt_supports_custom_view(this, 'unfinished','.table-supports'); return false;">
                                        <?= _l('support_list_unfinished'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" onclick="dt_supports_custom_view(this, 'not_assigned','.table-supports'); return false;">
                                        <?= _l('support_list_not_assigned'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" onclick="dt_supports_custom_view(this, 'due_date_passed','.table-supports'); return false;">
                                        <?= _l('support_list_duedate_passed'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
            </div>
            <div class="panel_s animated fadeIn">
                <div class="panel-body">
                    <?= form_hidden('supports_sort_by'); ?>
                    <?= form_hidden('finished'); ?>
                    <?= form_hidden('unfinished'); ?>
                    <?= form_hidden('not_assigned'); ?>
                    <?= form_hidden('due_date_passed'); ?>

                    <div class="clearfix"></div>
                    <?php
                    $table_data = array(
                        _l('id'),
                        _l('support_dt_name'),
                        _l('support_dt_members_assignes'),
                        _l('support_dt_priority'),
                        _l('support_dt_datestart'),
                        _l('support_dt_duedate'),
                        _l('support_dt_finished')
                    );

                    render_datatable($table_data,'supports'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div id="support" class="animated"></div>
        </div>
    </div>
</div>
</div>
<script>var hidden_columns = [0];</script>
<?php init_tail(); ?>
</body>
<script src="<?= site_url('assets/js/admin/supports/supports.js?v=' . version_sources()); ?>"></script>
</html>

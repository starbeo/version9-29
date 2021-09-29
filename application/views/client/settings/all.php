<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <?= form_hidden('tab_hash', $tab_hash); ?>
        <?= form_open_multipart($this->uri->uri_string(), array('id' => 'settings-form')); ?>
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <ul class="nav nav-tabs no-margin" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#general_settings_tab" aria-controls="general_settings_tab" role="tab" data-toggle="tab">
                                    <?= _l('general'); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="general_settings_tab">
                                <?php include_once(APPPATH . 'views/client/settings/includes/general.php'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-left">
                <button type="submit" class="btn btn-primary"><?= _l('settings_save'); ?></button>
            </div>
        </div>
        <?= form_close(); ?>
    </div>
</div>
<?php init_tail_client(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/client/settings/all.js?v=' . version_sources()); ?>"></script>
</body>
</html>

<?php init_head(); ?>
<div id="wrapper">
    <div class="content email-templates">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="bold"><?= _l('clients'); ?></h4>
                                <table class="table table-hover table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?= _l('list_of_modeles'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($clients as $template) { ?>
                                            <tr>
                                                <td class="<?php
                                                if ($template['active'] == 0) {
                                                    echo 'text-throught';
                                                }

                                                ?>">
                                                    <i class="fa fa-file-text-o"></i> <a href="<?= admin_url('emails/email_template/' . $template['emailtemplateid']); ?>"><?= _l($template['name']); ?></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        
                                    </tbody>
                                </table>
                                
                                <h4 class="bold"><?= _l('invoices'); ?></h4>
                                <table class="table table-hover table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?= _l('list_of_modeles'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($invoices as $template) { ?>
                                            <tr>
                                                <td class="<?php
                                                if ($template['active'] == 0) {
                                                    echo 'text-throught';
                                                }

                                                ?>">
                                                    <i class="fa fa-file-text-o"></i> <a href="<?= admin_url('emails/email_template/' . $template['emailtemplateid']); ?>"><?= _l($template['name']); ?></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
</body>
</html>

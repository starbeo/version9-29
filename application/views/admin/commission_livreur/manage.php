<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12" id="small-table">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (has_permission('invoices', '', 'create')) { ?>
                            <a href="<?= admin_url('commission_livreur/facture/false/2'); ?>" class="btn btn-info mright5 pull-left display-block">
                               Nouveau Facture livreur</a>
                        <?php } ?>

               
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <!-- if invoiceid found in url -->
                        <?= form_hidden('invoiceid', isset($invoiceid) ? $invoiceid : ''); ?>
                        <?php
                        render_datatable(array(
                           '',
                            '',
                            _l('name'),
                            _l('delivery_man'),
                            _l('total_nbr_livre'),
                            _l('total_frais_et'),
                            _l('total_nbr_refuse'),
                            _l('total_refuse_et'),
                            _l('status'),
                            _l('date_created'),
                            _l('staff'),
                            _l('total'),
                            _l('options')
                            ), 'factures12');

                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div id="facture"></div>
                <?= loader_waiting_ajax('35%', '45%'); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal animated fadeIn" id="commentaire_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?= _l('invoice_comment_edit_heading'); ?></span>
                    <span class="add-title"><?= _l('invoice_comment_add_heading'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/factures/commentaire', array('id' => 'comment')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_textarea('commentaire', 'comment'); ?>
                        <?= form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button id="submit" group="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<div class="modal animated fadeIn" id="add_line_additionnal_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span><?= _l('add_additionnal_line_invoice'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/factures/add_additionnal_line', array('id' => 'add-additionnal-line-form')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= render_input('description_line', 'description'); ?>
                        <?= render_input('total_line', 'total', '', 'number', array('min' => 0)); ?>
                        <?= form_hidden('id'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button group="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>



<script>
    var hidden_columns = [3, 4, 5, 11];
</script>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/commission_livreur/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>


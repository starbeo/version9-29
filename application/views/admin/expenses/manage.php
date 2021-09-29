<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-6" id="small-table">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php
                        //Insertion de l'ID de l'entreprise
                        $id_E = $this->session->userdata('staff_user_id_entreprise');

                        ?>
                        <?php if (has_permission('expenses', '', 'create')) { ?>
                            <a href="<?= admin_url('expenses/expense'); ?>" class="btn btn-info"><?= _l('new_expense'); ?></a>
                        <?php } ?>
                        <a href="#" class="btn btn-default pull-right" onclick="toggle_small_view('.table-expenses', '#expense');
                                return false;" data-toggle="tooltip" title="<?= _l('invoices_toggle_table_tooltip'); ?>"><i class="fa fa-bars"></i></a>

                    </div>
                </div>
                <?= form_hidden('custom_view'); ?>
                <div class="panel_s animated fadeIn">
                    <div class="panel-body">

                        <div class="clearfix"></div>
                        <!-- if expenseid found in url -->
                        <?= form_hidden('expenseid', $expenseid); ?>
                        <?php
                        $table_data = array(
                            _l('expense_dt_table_heading_note'),
                            _l('expense_dt_table_heading_category'),
                            _l('expense_dt_table_heading_amount'),
                            _l('expense_dt_table_heading_date'),
                            _l('expense_dt_table_heading_delivery_man'),
                            _l('expense_dt_table_heading_payment_mode'),
                        );

                        render_datatable($table_data, 'expenses');

                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div id="expense"></div>
            </div>
        </div>
    </div>
</div>
<script>var hidden_columns = [3, 4, 5];</script>
<?php init_tail(); ?>
<script>
    $(document).ready(function () {
        ExpensesServerParams = {
            'custom_view': '[name="custom_view"]'
        };
        initDataTable('.table-expenses', window.location.href, 'Dépenses', 'undefined', 'undefined', ExpensesServerParams, [2, 'DESC']);

        $.each(hidden_columns, function (i, val) {
            var column_estimates = $('.table-expenses').DataTable().column(val);
            column_estimates.visible(false);
        });
        Dropzone.autoDiscover = false;
        init_expense();
    });
    function init_expense(id) {
        var _expenseid = $('body').find('input[name="expenseid"]').val();
        // Check if expense id passed from url
        if (_expenseid !== '') {
            id = _expenseid;
        } else {
            if (typeof (id) === 'undefined' || id === '') {
                return;
            }
        }

        $('body').find('input[name="expenseid"]').val('');
        if (!$('body').hasClass('small-table')) {
            toggle_small_view('.tbl-expenses', '#expense');
        }
        $('#expense').load(admin_url + 'expenses/get_expense_data_ajax/' + id);

        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            $('html, body').animate({
                scrollTop: $('#expense').offset().top + 150
            }, 600);
        }

    }
</script>
</body>
</html>



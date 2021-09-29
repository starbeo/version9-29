<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th><?= _l('payments_table_number_heading'); ?></th>
                <th><?= _l('payments_table_mode_heading'); ?></th>
                <th><?= _l('payments_table_date_heading'); ?></th>
                <th><?= _l('payments_table_amount_heading'); ?></th>
                <th><?= _l('options'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($invoice->payments as $payment) { ?>
                <tr class="payment">
                    <td><?= $payment['paymentid']; ?>
                    </td>
                    <td>
                        <?php
                        $mode_string = '';
                        $mode_string .= $payment['name'];
                        if ($payment['transactionid']) {
                            $mode_string .= '<br />' . _l('payments_table_transaction_id', $payment['transactionid']);
                        }
                        echo $mode_string;

                        ?>
                    </td>
                    <td><?= date(get_current_date_format(), strtotime($payment['date'])); ?></td>
                    <td><?= $payment['amount']; ?></td>
                    <td>
                        <a href="<?= admin_url('payments/payment/' . $payment['paymentid']); ?>" class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
                        <a href="<?= admin_url('factures_internes/delete_payment/' . $payment['paymentid'] . '/' . $payment['factureinterneid']); ?>" class="btn btn-danger btn-icon"><i class="fa fa-remove"></i></a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>



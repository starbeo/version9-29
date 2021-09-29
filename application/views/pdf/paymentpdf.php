<?php

$pdf->SetFontSize(15);
$pdf->Ln(5);
$pdf->Cell(0, 0, ucwords(_l('payment_receipt')), 0, 1, 'C', 0, '', 0);
$pdf->SetFontSize(10);
$pdf->Ln(15);
$pdf->Cell(0, 0, _l('payment_date') . ' ' . date(get_current_date_format(), strtotime($payment->date)), 0, 1, 'L', 0, '', 0);
$pdf->Ln(2);
$pdf->writeHTMLCell(80, '', '', '', '<hr/>', 0, 1, false, true, 'L', true);
$pdf->Cell(0, 0, _l('payment_view_mode') . ' ' . $payment->name, 0, 1, 'L', 0, '', 0);
if(!empty($payment->transactionid)) {
    $pdf->Ln(2);
    $pdf->writeHTMLCell(80, '', '', '', '<hr/>', 0, 1, false, true, 'L', true);
    $pdf->Cell(0, 0, _l('payment_transaction_id') . ' ' . $payment->transactionid, 0, 1, 'L', 0, '', 0);
}
$pdf->Ln(2);
$pdf->writeHTMLCell(80, '', '', '', '<hr />', 0, 1, false, true, 'L', true);
$pdf->SetFillColor(37, 155, 36);
$pdf->SetTextColor(255);
$pdf->SetFontSize(12);
$pdf->Ln(3);
$pdf->Cell(80, 10, _l('payment_total_amount'), 0, 1, 'C', '1');
$pdf->SetFontSize(11);
$pdf->Cell(80, 10, format_money($payment->amount), 0, 1, 'C', '1');
$pdf->Ln(5);
// The Table
$pdf->Ln(5);
$pdf->SetTextColor(0);
$pdf->SetFont('freesans', 'B', 14);
$pdf->Cell(0, 0, _l('payment_for_string'), 0, 1, 'L', 0, '', 0);
$pdf->SetFont('freesans', '', 10);
$pdf->Ln(5);
// Header
$tblhtml = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="0">
<tr height="30" style="color:#fff;" bgcolor="#3A4656">
    <th width="25%;">' . _l('payment_table_invoice_number') . '</th>
    <th width="25%;">' . _l('payment_table_invoice_date') . '</th>
    <th width="25%;">' . _l('payment_table_invoice_amount_total') . '</th>
    <th width="25%;">' . _l('payment_table_payment_amount_total') . '</th>
</tr>';
$tblhtml .= '<tbody>';
$tblhtml .= '<tr>';
$tblhtml .= '<td>' . $payment->invoice_data->nom . '</td>';
$tblhtml .= '<td>' . date(get_current_date_format(), strtotime($payment->invoice_data->date_created)) . '</td>';
$tblhtml .= '<td>' . format_money($payment->invoice_data->total) . '</td>';
$tblhtml .= '<td>' . format_money($payment->amount) . '</td>';
$tblhtml .= '</tr>';
$tblhtml .= '</tbody>';
$tblhtml .= '</table>';
$pdf->writeHTML($tblhtml, true, false, false, false, '');

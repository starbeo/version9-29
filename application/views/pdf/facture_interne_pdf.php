<?php

$data  = '';

$data .= '<div style="border: 3px solid #000; height: auto;">';

$data .= '<div>
            <div style="float: left; width: 45%; height: 10%; padding: 15px 10px 5px 10px;">
                <img src="'.logo_pdf_url().'" style="width: 70%; height: 85px;">
            </div>
            <div style="float: right; width: 49%; height: 10%; line-height: 18px; padding-left: 10px; font-weight: bold;">
                <p>Date du : '.date('d/m/Y', strtotime($facture_interne->date_created)).'</p>
                <p>Nombre de factures : '.count($facture_interne->items).'</p>
            </div>
          </div>
         ';

$data .= '<div style="font-weight: bold; font-size: 25px; text-align: center;">
            Factures Internes Détaillées
          </div>
         ';

$data .= '<div style="font-size: 20px; text-align: center;">
            Facture Interne N&deg; : '.$invoice_number.'
          </div>';

$data .= '<div style="font-weight: bold; font-size: 18px; text-align: left; padding-left: 5px;">
            Listes Factures :
          </div>
         ';

$data .= "  <table border=1 style='width: 100%; margin: 10px 5px; 20px 5px;'>
                <thead>
                    <tr>
                        <td align=center width='7%'>N&deg;</td>
                        <td align=center width='15%'>Facture</td>
                        <td align=center width='15%'>"._l('facture_interne_client')."</td>
                        <td align=center width='15%'>Total Brut</td>
                        <td align=center width='16%'>Total Frais</td>
                        <td align=center width='16%'>Total Refusé</td>
                        <td align=center width='16%'>Total Parrainage</td>
                        <td align=center width='16%'>Total Remise</td>
                        <td align=center width='16%'>Total NET</td>
                    </tr>
                </thead>
                <tbody>";
$cpt = 1;  
foreach ($facture_interne->items as $item) {
    $data .= "      <tr>
                        <td align=center>".$cpt."</td>
                        <td align=center>".$item['nom']."</td>
                        <td align=center>".$item['client']."</td>
                        <td align=center>".number_format($item['total_crbt'], 2, ',', ' ')."</td>
                        <td align=center>".number_format($item['total_frais'], 2, ',', ' ')."</td>
                        <td align=center>".number_format($item['total_refuse'], 2, ',', ' ')."</td>
                        <td align=center>".number_format($item['total_parrainage'], 2, ',', ' ')."</td>
                        <td align=center>".number_format($item['total_remise'], 2, ',', ' ')."</td>
                        <td align=center>".number_format($item['total_net'], 2, ',', ' ')."</td>
                    </tr>"; 
    $cpt++;
}
$data .= "      </tbody>
            </table>
         ";

$data .= "  <table border=1 style='width: 40%; margin: 0px 5px 20px auto;'>
                
                <tr>
                    <td align='center'>Total Brut</td>
                    <td align='right' style='padding-right:10px'>". number_format($facture_interne->total, 2, ',', ' ') ."</td>
                </tr>
                <tr>
                    <td align='center'>Total Frais</td>
                    <td align='right' style='padding-right:10px'>". number_format($facture_interne->total_frais, 2, ',', ' ') ."</td>
                </tr>
                <tr>
                    <td align='center'>Total Refusé</td>
                    <td align='right' style='padding-right:10px'>". number_format($facture_interne->total_refuse, 2, ',', ' ') ."</td>
                </tr>
                <tr>
                    <td align='center'>Total Parrainage</td>
                    <td align='right' style='padding-right:10px'>". number_format($facture_interne->total_parrainage, 2, ',', ' ') ."</td>
                </tr>
                <tr>
                    <td align='center'>Total Remise</td>
                    <td align='right' style='padding-right:10px'>". number_format($facture_interne->total_remise, 2, ',', ' ') ."</td>
                </tr>
                <tr>
                    <td align='center'>Total Net</td>
                    <td align='right' style='padding-right:10px'>". number_format($facture_interne->total_net, 2, ',', ' ') ."</td>
                </tr>
                <tr>
                    <td align='center'>Règlement Total</td>
                    <td align='right' style='padding-right:10px'>". number_format(sum_from_table('tblfactureinternepaymentrecords', array('where'=>array('factureinterneid'=>$facture_interne->id), 'field'=>'amount')), 2, ',', ' ') ."</td>
                </tr>
                <tr>
                    <td align='center'>"._l('facture_interne_rest')."</td>
                    <td align='right' style='padding-right:10px'>". number_format($facture_interne->rest, 2, ',', ' ') ."</td>
                </tr>";

if(!is_null($facture_interne->motif) && !empty($facture_interne->motif)){
$data .= "      <tr>
                    <td colspan=2 style='padding-left: 10px; font-size:13px;'><b>Commentaire : </b>".$facture_interne->motif."</td>
                </tr>
         ";
}
$data .= "  </table>";

if(count($facture_interne->payments) > 0) {
$data .= '<div style="font-weight: bold; font-size: 18px; text-align: left;  margin: 10px 5px;">
            Listes Règlements :
          </div>
         ';

$data .= "  <table border=1 style='width: 100%; margin: 0px 5px; 0px 5px;'>
                <thead>
                    <tr>
                        <td align=center width='7%'>"._l('payments_table_number_heading')."</td>
                        <td align=center width='7%'>"._l('record_payment_leave_note')."</td>
                        <td align=center width='15%'>"._l('payments_table_mode_heading')."</td>
                        <td align=center width='15%'>"._l('payments_table_date_heading')."</td>
                        <td align=right  style='padding-right:10px' width='15%'>"._l('payments_table_amount_heading')."</td>
                    </tr>
                </thead>
                <tbody>";
$cpt   = 1;   
$total = 0;  
foreach ($facture_interne->payments as $payment) {
    $date_created = '';
    if(!is_null($item['date_created'])){
        $date_created = date('d/m/Y', strtotime($item['date_created']));
    }

    $mode_string  = '';
    $mode_string .= $payment['name'];
    if($payment['transactionid']){
        $mode_string .= '<br />'._l('payments_table_transaction_id',$payment['transactionid']);
    }

    $total = $total + $payment['amount'];

    $data .= "      <tr>
                        <td align=center>".$cpt."</td>
                        <td align=center>".$payment['note']."</td>
                        <td align=center>".$mode_string."</td>
                        <td align=center>".date(get_current_date_format() , strtotime($payment['date']))."</td>
                        <td align=right style='padding-right:10px'>".$payment['amount']."</td>
                    </tr>"; 
    $cpt++;
}
$data .= "      </tbody>
            </table>
         ";

$data .= "  <table border=1 style='width: 40%; margin: 20px 5px 10px auto;'>
                <tr>
                    <td align='center'>"._l('facture_interne_total_received')."</td>
                    <td align='right' style='padding-right:10px'>". number_format($total, 2, ',', ' ') ."</td>
                </tr>
                <tr>
                    <td align='center'>Total Net</td>
                    <td align='right' style='padding-right:10px'>". number_format($facture_interne->total_net, 2, ',', ' ') ."</td>
                </tr>
                 <tr>
                    <td align='center'>Total Frais</td>
                    <td align='right' style='padding-right:10px'>". number_format($facture_interne->total_frais, 2, ',', ' ') ."</td>
                </tr>
            </table>
         ";
}

$data .= '</div>';


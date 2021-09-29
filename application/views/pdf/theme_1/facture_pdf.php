<?php
$data .= '<div style="border: 3px solid #000; height: auto;">';

$data .= '<div style="min-height: 100px; height: auto;">
            <div style="float: left; width: 45%; height: 10%; padding: 15px 10px 5px 10px;">
                <img src="' . logo_pdf_url() . '" style="width: 70%; height: 85px;">
            </div>
            <div style="float: right; width: 49%; height: 10%; line-height: 10px; padding-left: 10px; font-weight: bold;">
                <p>Client : <u>' . $invoice->nom_expediteur . '</u></p>
                <p>Nom de la banque : ' . ucwords($invoice->client->marque) . '</p>
                <p>RIB : ' . $invoice->client->rib . '</p>
                <p>Période du : ' . date('d/m/Y', strtotime($invoice->date_created)) . '</p>
                <p>Statut : ' . $invoice->name . '</p>
            </div>
          </div>
         ';

$data .= '<div style="font-weight: bold; font-size: 30px; text-align: center;">
            Relevé Détaillé
          </div>
         ';

$data .= '<div style="font-size: 20px; text-align: center;">
            Relevé N&deg; : ' . $invoice_number . '
          </div>';


$data .= "<h3 style='margin-left:10px;'>Nous avons l'honneur de vous remettre ci-dessous les détailles de votre livraison</h3>";

$data .= "  <table border=1 style='width: 100%; margin: 0px 10px; 0px 0px;'>
                <thead>
                <tr>

                    <td align=center width='7%'>N&deg;</td>
                    <td align=center width='15%'>Code d'envoi</td>
<td align=center width='10%'>numéro commande </td>
                    <td align=center width='16%'>Date ramassage</td>";
if ($invoice->type == 2) {
    $data .= "<td align=center width='17%'>Date de livraison</td>";
}
$data .= "          <td align=center width='11%'>Statut</td>
                    <td align=center width='12%'>Crbt</td>
                    <td align=center width='14%'>Ville</td>
                    <td align=center width='8%'>Frais</td>";
if ($invoice->type == 3) {
    $data .= "<td align=center width='17%'>Motif</td>";
}
$data .= "      </tr>
                </thead>
                <tbody>";

$total_brut = 0;
$frais = 0;
$total_net = 0;
foreach ($invoice->items as $key => $item) {
    $date_ramassage = '';
    if (!is_null($item['date_ramassage'])) {
        $date_ramassage = date('d/m/Y', strtotime($item['date_ramassage']));
    }
    $date_livraison = '';
    if (!is_null($item['date_livraison'])) {
        $date_livraison = date('d/m/Y', strtotime($item['date_livraison']));
    }

    $crbt = 0;
    $status = $item['status_id'];
    if ($status == 2 || $status == 3) {
        $crbt = $item['crbt'];
        $status = $item['status'];
    } else {
        $status = $item['status_reel_name'];
    }

    $item_frais = 0;
    if ($invoice->type == 2) {
        $item_frais = $item['frais'];
    } else if ($invoice->type == 3) {
        $item_frais = $invoice->frais;
    }

    $data .= "      <tr>
                        <td align=center>" . ($key + 1) . "</td>
                        <td align=center>" . $item['code_barre'] . "</td>
        <td align=center>" . $item['num_commande']. "</td>
                      
  <td align=center>" . $date_ramassage . "</td>";
    if ($invoice->type == 2) {
        $data .= " <td align=center>" . $date_livraison . "</td>";
    }
    $data .= "          <td align=center>" . $status . "</td>
                        <td align=right style='padding-right: 5px;'>" . number_format($crbt, 2, ',', ' ') . "</td>
                        <td align=center>" . $item['ville_name'] . "</td>
                        <td align=center>" . $item_frais . "</td>";
    if ($invoice->type == 3) {
        $data .= " <td align=center>" . $item['motif'] . "</td>";
    }
    $data .= "      </tr>";
    $total_brut += $crbt;
    $frais += $item_frais;
}
$data .= "      </tbody>
            </table>
         ";

$discount = 0;
$discount_txt = '';
if ($invoice->remise_type == 'fixed_amount') {
    $discount = $invoice->remise;
} else if ($invoice->remise_type == 'percentage') {
    $discount = ($invoice->total_refuse * ($invoice->remise / 100));
    $discount_txt = ' de ' . $invoice->remise . '%';
}

$total_net = $total_brut - $frais + $discount;
if ($invoice->type == 2) {
    $data .= "  <table border=1 style='width: 40%; margin: 20px 10px 10px auto;'>
                <tr>
                    <td align='left'>" . _l('total_brut') . "</td>
                    <td align='right' style='padding-right:10px'>" . number_format($invoice->total_crbt, 2, ',', ' ') . " Dhs</td>
                </tr>
                <tr>
                    <td align='left'>" . _l('total_frais') . " (-)</td>
                    <td align='right' style='padding-right:10px'>" . number_format($invoice->total_frais, 2, ',', ' ') . " Dhs</td>
                </tr>";
    if ($invoice->total_refuse > 0) {
    $data .= "  <tr>
                    <td align='left'>" . _l('total_refuse') . " (-)</td>
                    <td align='right' style='padding-right:10px'>" . number_format($invoice->total_refuse, 2, ',', ' ') . " Dhs</td>
                </tr>";
    }
    if ($invoice->total_parrainage > 0) {
    $data .= "  <tr>
                    <td align='left'>" . _l('total_parrainage') . " (+)</td>
                    <td align='right' style='padding-right:10px'>" . number_format($invoice->total_parrainage, 2, ',', ' ') . " Dhs</td>
                </tr>";
    }
    if (!is_null($invoice->description_line) && get_option('show_add_line_additionnal_in_invoice') == 1) {
        $data .= "  <tr>
                        <td align='left'>" . ucwords($invoice->description_line) . "</td>
                        <td align='right' style='padding-right:10px'>" . number_format($invoice->total_line, 2, ',', ' ') . " Dhs</td>
                    </tr>";
    }
    if ($discount > 0 && get_option('show_discount_in_invoice_pdf') == 1) {
        $data .= "  <tr>
                        <td align='left'>" . _l('discount_invoice') . $discount_txt . " (+)</td>
                        <td align='right' style='padding-right:10px'>" . number_format($discount, 2, ',', ' ') . " Dhs</td>
                    </tr>";
    }
    $data .= "  <tr>
                    <td align='left'>" . _l('total_net') . "</td>
                    <td align='right' style='padding-right:10px'>" . number_format($invoice->total_net, 2, ',', ' ') . " Dhs</td>
                </tr>
                <tr>
                    <td align='center' colspan=2 style='font-size:20px;'><b>Sauf Erreur ou Omission</b></td>
                </tr>
            </table>
         ";
} else {
    if($frais > 0) {
        $data .= "  <table border=1 style='width: 40%; margin: 20px 10px 10px auto;'>
                        <tr>
                            <td align='left'>Total Frais de Retour</td>
                            <td align='right' style='padding-right:10px'>" . number_format($frais, 2, ',', ' ') . " Dhs</td>
                        </tr>
                        <tr>
                            <td align='center' colspan=2 style='font-size:20px;'><b>Sauf Erreur ou Omission</b></td>
                        </tr>
                    </table>
             ";
    }
}


$data .= "<h3 style='margin-left:10px;'>Merci Pour votre confiance</h3>";

if (!is_null($invoice->commentaire)) {
    $data .= '<div style="border: 2px solid #000; font-size: 17px; text-align: left; margin: 0px auto 0px auto; width: 95%; min-height: 20px; height: auto; line-height: 23px; padding: 10px 20px 10px 20px;">
            <span style="font-size: 15px;">' . _l('comment') . ' : ' . $invoice->commentaire . '
          </div>
         ';
}

$data .= '</div>';

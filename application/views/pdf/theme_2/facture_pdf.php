<?php
$data .= '<div style="height: auto;">';

$data .= '<div style="min-height: 90px; height: auto;">
            <div style="float: left; width: 45%;">
                <img src="' . logo_pdf_url() . '" style="width: 90%; height: 85px;">
            </div>
            <div style="float: right; width: 49%; line-height: 5px; padding: 10px 0 0 10px;">
                <h1 style="font-size: 40px; text-align: right;">Relevé Détaillé</h1>
            </div>
          </div>
         ';

$data .= '<div style="min-height: 115px; margin: 20px 0 10px 0; height: auto;">
            <div style="float: left; width: 45%; border: 1px solid #000; border-radius: 15px; padding: 10px; line-height: 5px;">
                <table>
                    <tbody>
                        <tr>
                            <td style="font-weight: bold;">Client</td>
                            <td>:</td>
                            <td>' . strtoupper($invoice->nom_expediteur) . '</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Banque</td>
                            <td>:</td>
                            <td>' . ucwords($invoice->client->marque) . '</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">RIB</td>
                            <td>:</td>
                            <td>' . $invoice->client->rib . '</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="float: right; width: 45%; border: 1px solid #000; border-radius: 15px; padding: 10px; line-height: 5px;">
                <table>
                    <tbody>
                        <tr>
                            <td style="font-weight: bold;">Numéro</td>
                            <td>:</td>
                            <td>' . $invoice_number . '</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Date</td>
                            <td>:</td>
                            <td>' . date(get_current_date_format(), strtotime($invoice->date_created)) . '</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Statut</td>
                            <td>:</td>
                            <td>' . $invoice->name . '</td>
                        </tr>
                    </tbody>
                </table>
            </div>
          </div>
         ';

$data .= "  <table border=1 style='border-collapse:collapse; width: 100%;'>
                <thead>
                    <tr>
                        <th align=center width='5%'>N&deg;</th>
                        <th align=center width='18%'>Code d'envoi</th>
                        <th align=center width='16%'>Date ramassage</th>";
if ($invoice->type == 2) {
    $data .= "          <th align=center width='17%'>Date de livraison</th>";
}
$data .= "              <th align=center width='17%'>Ville</th>
                        <th align=center width='9%'>Statut</th>
                        <th align=center width='10%'>Crbt</th>
                        <th align=center width='8%'>Frais</th>";
if ($invoice->type == 3) {
    $data .= "          <th align=center width='17%'>Motif</th>";
}
$data .= "          </tr>
                </thead>
                <tbody>";

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
                        <td align=center>" . $date_ramassage . "</td>";
    if ($invoice->type == 2) {
        $data .= "      <td align=center>" . $date_livraison . "</td>";
    }
    $data .= "          <td align=center>" . ucwords(strtolower($item['ville_name'])) . "</td>
                        <td align=center>" . $status . "</td>
                        <td align=right style='padding-right: 5px;'>" . number_format($crbt, 2, ',', ' ') . "</td>
                        <td align=center>" . number_format($item_frais, 2, ',', ' ') . "</td>";
    if ($invoice->type == 3) {
        $data .= "      <td align=center>" . $item['motif'] . "</td>";
    }
    $data .= "      </tr>";
}
$data .= "      </tbody>
            </table>";

$discount = 0;
$discount_txt = '';
if ($invoice->remise_type == 'fixed_amount') {
    $discount = $invoice->remise;
} else if ($invoice->remise_type == 'percentage') {
    $discount = ($invoice->total_refuse * ($invoice->remise / 100));
    $discount_txt = ' de ' . $invoice->remise . '%';
}

if ($invoice->type == 2) {
    $data .= "  <table border=1 style='border-collapse:collapse; width: 30%; margin: 10px 0 0 auto;'>
                    <tbody>
                        <tr>
                            <td align='center' style='font-weight: bold;'>MONTANT TOTAL EN DHS</td>
                        </tr>
                    </tbody>
                </table>
                       
                <table style='border: 1px solid #000; width: 30%; margin: 0 0 0 auto;'>
                    <tbody>
                        <tr>
                            <td align='left'>" . _l('total_brut') . "</td>
                            <td>:</td>
                            <td align='right' style='padding-right:10px'>" . number_format($invoice->total_crbt, 2, ',', ' ') . "</td>
                        </tr>
                        <tr>
                            <td align='left'>" . _l('total_frais') . " (-)</td>
                            <td>:</td>
                            <td align='right' style='padding-right:10px'>" . number_format($invoice->total_frais, 2, ',', ' ') . "</td>
                        </tr>";
    if ($invoice->total_refuse > 0) {
    $data .= "          <tr>
                            <td align='left'>" . _l('total_refuse') . " (-)</td>
                            <td>:</td>
                            <td align='right' style='padding-right:10px'>" . number_format($invoice->total_refuse, 2, ',', ' ') . "</td>
                        </tr>";
    }
    if ($invoice->total_parrainage > 0) {
    $data .= "                    <tr>
                            <td align='left'>" . _l('total_parrainage') . " (+)</td>
                            <td>:</td>
                            <td align='right' style='padding-right:10px'>" . number_format($invoice->total_parrainage, 2, ',', ' ') . "</td>
                        </tr>";
    }
    if (!is_null($invoice->description_line) && get_option('show_add_line_additionnal_in_invoice') == 1) {
        $data .= "      <tr>
                            <td align='left'>" . ucwords($invoice->description_line) . "</td>
                            <td>:</td>
                            <td align='right' style='padding-right:10px'>" . number_format($invoice->total_line, 2, ',', ' ') . "</td>
                        </tr>";
    }
    if ($discount > 0 && get_option('show_discount_in_invoice_pdf') == 1) {
        $data .= "      <tr>
                            <td align='left'>" . _l('discount_invoice') . $discount_txt . " (+)</td>
                            <td>:</td>
                            <td align='right' style='padding-right:10px'>" . number_format($discount, 2, ',', ' ') . "</td>
                        </tr>";
    }
    $data .= "      </tbody>
                </table>";
    $data .= "  <table style='border: 1px solid #000; width: 30%; margin: 0 0 0 auto;'>
                    <tbody>
                        <tr>
                            <td align='left' style='font-weight: bold;'>" . _l('total_net') . "</td>
                            <td align='right' style='font-weight: bold; padding-right:10px;'>" . number_format($invoice->total_net, 2, ',', ' ') . "</td>
                        </tr>
                    </tbody>
                </table>";
} else {
    if($frais > 0) {
        $data .= "  <table border=1 style='border-collapse:collapse; width: 30%; margin: 10px 0 0 auto;'>
                        <tbody>
                            <tr>
                                <td align='center' style='font-weight: bold;'>MONTANT TOTAL EN DHS</td>
                            </tr>
                        </tbody>
                    </table>

                    <table style='border: 1px solid #000; width: 30%; margin: 0 0 0 auto;'>
                        <tbody>
                            <tr>
                                <td align='left'>Total Frais de Retour</td>
                                <td>:</td>
                                <td align='right' style='padding-right:10px'>" . number_format($frais, 2, ',', ' ') . "</td>
                            </tr>
                        </tbody>
                    </table>";  
    }
}

$data .= "<h3 style='text-align: center;'>Merci Pour votre confiance, Sauf Erreur ou Omission.</h3>";

if (!is_null($invoice->commentaire)) {
    $data .= '<div style="border: 2px solid #000; font-size: 17px; text-align: left; margin: 0px auto 0px auto; width: 95%; min-height: 20px; height: auto; line-height: 23px; padding: 10px 20px 10px 20px;">
            <span style="font-weight: bold;">' . _l('comment') . ' : </span>' . $invoice->commentaire . '
          </div>
         ';
}

$data .= '</div>';

<?php
$data .= '<div style="height: auto;">';

$data .= '<div style="min-height: 90px; height: auto;">
            <div style="float: left; width: 45%;">
                <img src="' . logo_pdf_url() . '" style="width: 90%; height: 85px;">
            </div>
            <div style="float: right; width: 49%; line-height: 5px; padding: 10px 0 0 10px;">
                <h1 style="font-size: 40px; text-align: right;">Bon Livraison</h1>
            </div>
          </div>
         ';

$data .= '<div style="min-height: 115px; margin: 20px 0 10px 0; height: auto;">
            <div style="float: left; width: 45%; border: 1px solid #000; border-radius: 15px; padding: 10px; line-height: 5px;">
                <table>
                    <tbody>';
if($bon_livraison->type_livraison == 'a_domicile') {
$data .= '              <tr>
                            <td style="font-weight: bold;">Livreur</td>
                            <td>:</td>
                            <td>' . strtoupper($bon_livraison->livreur->firstname . ' ' . $bon_livraison->livreur->lastname) . '</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Téléphone</td>
                            <td>:</td>
                            <td>' . $bon_livraison->livreur->phonenumber . '</td>
                        </tr>';
} else {
$data .= '              <tr>
                            <td style="font-weight: bold;">Société</td>
                            <td>:</td>
                            <td>' . strtoupper($bon_livraison->point_relai->societe) . '</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Point Relai</td>
                            <td>:</td>
                            <td>' . strtoupper($bon_livraison->point_relai->nom) . '</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Adresse</td>
                            <td>:</td>
                            <td>' . $bon_livraison->point_relai->adresse . '</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Ville</td>
                            <td>:</td>
                            <td>' . $bon_livraison->point_relai->name_ville . '</td>
                        </tr>';    
}
$data .= '              <tr>
                            <td style="font-weight: bold;">Nombre de colis</td>
                            <td>:</td>
                            <td>' . count($bon_livraison->items) . '</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Signature</td>
                            <td>:</td>
                            <td></td>
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
                            <td>' . $bonLivraisonName . '</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Date</td>
                            <td>:</td>
                            <td>' . date(get_current_date_format(), strtotime($bon_livraison->date_created)) . '</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Statut</td>
                            <td>:</td>
                            <td>' . $bon_livraison->type . '</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Signature Responsable</td>
                            <td>:</td>
                            <td></td>
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
                        <th align=center width='10%'>Ouverture Colis</th>
         ";
if($bon_livraison->type_livraison == 'a_domicile') {
$data .= "              <th align=center width='20%'>Client</th>";
}
$data .= "              <th align=center width='20%'>Destinataire</th>
                        <th align=center width='16%'>Telephone</th>
                        <th align=center width='16%'>Ville</th>
                        <th align=center width='16%'>Quartier</th>
                        <th align=center width='12%'>CRBT</th>
                        <th align=center width='11%'>Statut</th>
                    </tr>
                </thead>
                <tbody>";

$total = 0;
foreach ($bon_livraison->items as $key => $item) {
    $date_ramassage = '';
    if (!is_null($item['date_ramassage'])) {
        $date_ramassage = date(get_current_date_format(), strtotime($item['date_ramassage']));
    }
    $ouverture = 'Non';
    if ($item['ouverture'] == 1) {
        $ouverture = 'Oui';
    }

    $data .= "      <tr>
                        <td align=center>" . ($key + 1) . "</td>
                        <td align=center>" . $item['code_barre'] . "</td>
                        <td align=center><b>" . $ouverture . "</b></td>
             ";
if($bon_livraison->type_livraison == 'a_domicile') {
    $data .= "          <td align=center>" . $item['nom'] . "</td>";
}
    $data .= "          <td align=center>" . $item['nom_complet'] . "</td>
                        <td align=center>" . $item['telephone'] . "</td>
                        <td align=center>" . $item['ville'] . "</td>
                        <td align=center>" . $item['quartier'] . "</td>
                        <td align=center style='padding-right: 5px;'>" . number_format($item['crbt'], 2, ',', ' ') . "</td>
                        <td align=center></td>
                    </tr>";
    $total += $item['crbt'];
}
$data .= "      </tbody>
            </table>";

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
                        <td align='left'>" . _l('total') . "</td>
                        <td>:</td>
                        <td align='right' style='padding-right:10px'>" . number_format($total, 2, ',', ' ') . "</td>
                    </tr>
                </tbody>
            </table>";

if (get_option('show_message_after_signature_bon_livraison') == 1) {
    $data .= "<h3 style='text-align: center;'>Les informations données par l\'expéditeur engagent sa responsabilité quant à la nature et la valeur réelle de la marchandise.</h3>";
}

$data .= '</div>';

<?php
$data .= '<div style="border: 3px solid #000; height: auto;">';

$data .= '<div style="min-height: 100px; height: auto;">
            <div style="float: left; width: 45%; height: 10%; padding: 15px 10px 5px 10px;">
                <img src="' . logo_pdf_url() . '" style="width: 70%; height: 85px;">
            </div>
            <div style="float: right; width: 49%; height: 10%; line-height: 10px; padding-left: 10px; font-weight: bold;">
                <p>Livreur : <u>' . $invoice->nom_expediteur . '</u></p>
                <p>Nom de la banque : CIH</p>
                <p>RIB : </p>
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
                  <td align=center width='10%'>Date </td>
                    <td align=center width='15%'>N° Etat colis livre</td>
                    <td align=center width='16%'>reste</td>";

$data .= "          <td align=center width='11%'>Nbr Colis Livre </td>
                    <td align=center width='12%'>Frais Livre </td>
                    <td align=center width='14%'>Nbr colis refuse</td>
                    <td align=center width='8%'>Refuse Frais</td>";

$data .= "      </tr>
                </thead>
                <tbody>";

$total_brut = 0;
$frais = 0;
$total_net = 0;
foreach ($invoice->items as $key => $item) {

    $date = date('d/m/Y', strtotime($item['date_created']));


    $crbt = 0;
    $status = $item['status'];


   // $item_frais = 0;
    //if ($invoice->type == 2) {
    //    $item_frais = $item['frais'];
   // } else if ($invoice->type == 3) {
 //       $item_frais = $invoice->frais;
  //  }

    $data .= "      <tr>
        <td align=center>" . ($key + 1) . "</td>
        <td align=center>" . $date. "</td>
                    
                        <td align=center>" . $item['nom'] . "</td>
                        <td align=center>" .number_format( $item["manque"] ) . "</td>";

    $data .= "         
                                 <td align=right style='padding-right: 5px;'>" . getcolisrefusepdf($item['id'], 2) . "</td>

          <td align=center>" . number_format( $item["commision"] ). "</td>
                        <td align=center>" .  getcolisrefusepdf($item['id'], 9) . "</td>
                        <td align=center>"  . $item['refuse_commision']  . "</td>";

    $data .= "      </tr>";

}
$data .= "      </tbody>
            </table>
         ";

$discount = 0;
$discount_txt = '';

    $data .= "  <table border=1 style='width: 40%; margin: 20px 10px 10px auto;'>
                <tr>
                    <td align='left'>" . _l('total_frais_et') . "</td>
                    <td align='right' style='padding-right:10px'>" . number_format($invoice->total_frais) . " Dhs</td>
                </tr>
                <tr>
                    <td align='left'>" . _l('total_nbr_livre') . " (-)</td>
                    <td align='right' style='padding-right:10px'>" . number_format($invoice->totalnbr_livre) . " </td>
                </tr>";

        $data .= "  <tr>
                    <td align='left'>" . _l('total_refuse_et') . " (-)</td>
                    <td align='right' style='padding-right:10px'>" . number_format($invoice->total_refuse) . " Dhs</td>
                </tr>";

        $data .= "  <tr>
                    <td align='left'>" . _l('total_nbr_refuse') . " (+)</td>
                    <td align='right' style='padding-right:10px'>" . number_format($invoice->totalnbr_refuse) . " </td>
                </tr>";

        


    $data .= "  <tr>
                    <td align='left'>" . _l('total') . "</td>
                    <td align='right' style='padding-right:10px'>" . number_format($invoice->total_manque, 2, ',', ' ') . " Dhs</td>
                </tr>
                <tr>
                    <td align='center' colspan=2 style='font-size:20px;'><b>Sauf Erreur ou Omission</b></td>
                </tr>
            </table>
         ";





$data .= "<h3 style='margin-left:10px;'>Merci Pour votre confiance</h3>";

if (!is_null($invoice->commentaire)) {
    $data .= '<div style="border: 2px solid #000; font-size: 17px; text-align: left; margin: 0px auto 0px auto; width: 95%; min-height: 20px; height: auto; line-height: 23px; padding: 10px 20px 10px 20px;">
            <span style="font-size: 15px;">' . _l('comment') . ' : ' . $invoice->commentaire . '
          </div>
         ';
}

$data .= '</div>';

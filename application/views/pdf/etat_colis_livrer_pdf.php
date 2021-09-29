<?php
$data  = '';
$data .= '<div style="border: 2px solid #000; height: auto;">';

$data .= '<div style="min-height: 100px; height: auto;">
            <div style="float: left; width: 45%; height: 2%; padding: 15px 10px 5px 10px;">
                <img src="'.logo_pdf_url().'" style="width: 70%; height: 60px;">
            </div>
            <div style="line-height: 10px;">';
if($etat->type_livraison == 'a_domicile') {
$data .=        '<p><span style="font-weight: bold;">Livreur :</span> <u>' . $etat->livreur->firstname . ' ' . $etat->livreur->lastname . '</u></p>';
} else {
$data .=        '<p><span style="font-weight: bold;">Agent point relais :</span> <u>' . $etat->point_relai->firstname . ' ' . $etat->point_relai->lastname . '</u></p>';
}

$data .=        '<p><span style="font-weight: bold;">Date :</span> '.date(get_current_date_format(), strtotime($etat->date_created)).'</p>
                 <p><span style="font-weight: bold;">Nombre colis :</span> '.count($etat->items).'</p>
            </div>
          </div>
         ';
         
$data .= '<div style="font-weight: bold; font-size: 15px; text-align: center;  ">
            Etat Colis livré N&deg; : '.$etat_number.'
          </div>
         ';

$data .= "  <table border=1 style='width: 100%; margin: 0px 5px; 0px 0px;'>
                <thead>
                    <tr>
                        <td align=center width='7%'>N&deg;</td>
                        <td align=center width='20%'>Code d'envoi</td>
                        <td align=center width='20%'>Client</td>
                        <td align=center width='16%'>crbt</td>
                        <td align=center width='11%'>Statut</td>
                        <td align=center width='16%'>"._l('colis_list_city')."</td>
                        <td align=center width='16%'>Status sys</td>
                    </tr>
                </thead>
                <tbody>";
$cpt   = 1;           
foreach ($etat->items as $item) {
$data .= "          <tr>
                        <td align=center>".$cpt."</td>
                        <td align=center>".$item['code_barre']."</td>
                        <td align=center>".$item['nom']."</td>
                        <td align=center>". number_format($item['crbt'], 2, ',', ' ') ."</td>
                        <td align=center>".$item['status']."</td>
                        <td align=center>".$item['ville']."</td>
                        <td align=center></td>
                    </tr>";   
    $cpt++;
}
$data .= "  
                </tbody>
            </table>
        ";

$data .= "  <table border=1 style='width: 40%; margin: 0px 5px 20px auto;'>
                <tr>
                    <td align='center'>Total Recu</td>
                    <td align='right' style='padding-right:10px'>". number_format($etat->total_received, 2, ',', ' ') ." Dhs</td>
                </tr>
                <tr>
                    <td align='center'>Total</td>
                    <td align='right' style='padding-right:10px'>". number_format($etat->total, 2, ',', ' ') ." Dhs</td>
                </tr>
                <tr>
                    <td align='center'>Reste</td>
                    <td align='right' style='padding-right:10px'>". number_format($etat->manque, 2, ',', ' ') ." Dhs</td>
                </tr>";
 
if(!is_null($etat->justif) && !empty($etat->justif)){
$data .= "      <tr>
                    <td colspan=2 style='font-size:13px;'><b>Justification : </b>".$etat->justif."</td>
                </tr>
         ";
}

$data .= "  </table>";

$data .= '</div>';


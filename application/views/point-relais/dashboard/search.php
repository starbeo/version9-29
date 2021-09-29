<ul class="dropdown-menu search-results animated fadeIn display-block" id="top_search_dropdown">
    <?php
    $total = 0;
    foreach ($result as $heading => $results) {
        if (count($results) > 0) {
            $total++;

            ?>
            <li role="separator" class="divider"></li>
            <li class="dropdown-header"><?= ucwords(str_replace('_', ' ', $heading)); ?></li>
        <?php } ?>
        <?php
        foreach ($results as $_result) {
            $data = '';
            switch ($heading) {
                case 'colis':
                    $data = '<a href="' . point_relais_url('colis/search/' . $_result['code_barre']) . '">' . $_result['code_barre'] . '</a>';
                    break;
                case 'bons_livraison':
                    $data = '<a href="' . point_relais_url('bons_livraison/bon/' . $_result['id']) . '">' . $_result['nom'] . '</a>';
                    break;
                case 'etats_colis_livrer':
                    $data = '<a href="' . point_relais_url('etats_colis_livrer/etat/' . $_result['id']) . '">' . $_result['nom'] . '</a>';
                    break;
                case 'demandes':
                    $data = '<a href="' . point_relais_url('demandes/preview/' . $_result['id']) . '">' . $_result['name'] . '</a>';
                    break;
            }

            ?>
            <li><?= $data; ?></li>
        <?php } ?>
    <?php } ?>
    <?php if ($total == 0) { ?>
        <li class="padding-5 text-center">Aucun résultat trouvé</li>
    <?php } ?>
</ul>


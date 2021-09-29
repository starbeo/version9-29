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
                    $data = '<a href="' . client_url('colis') . '">' . $_result['code_barre'] . '</a>';
                    break;
                case 'colis_en_attente':
                    $data = '<a href="' . client_url('colis_en_attente') . '">' . $_result['code_barre'] . '</a>';
                    break;
                case 'bons_livraison':
                    $data = '<a href="' . client_url('bons_livraison') . '">' . $_result['nom'] . '</a>';
                    break;
                case 'factures':
                    $data = '<a href="' . client_url('factures/preview/' . $_result['id']) . '">' . $_result['nom'] . '</a>';
                    break;
                case 'demandes':
                    $data = '<a href="' . client_url('demandes/preview/' . $_result['id']) . '">' . $_result['name'] . '</a>';
                    break;
            }

            ?>
            <li><?= $data; ?></li>
        <?php } ?>
    <?php } ?>
    <?php if ($total == 0) { ?>
        <li class="padding-5 text-center">No Results Found</li>
        <?php } ?>
</ul>


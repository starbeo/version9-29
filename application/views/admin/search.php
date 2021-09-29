 <ul class="dropdown-menu search-results animated fadeIn display-block" id="top_search_dropdown">
   <?php
   $total = 0;
   foreach($result as $heading => $results){
    if(count($results) > 0){
        $total++;
        ?>
        <li role="separator" class="divider"></li>
        <li class="dropdown-header"><?= ucwords(str_replace('_',' ',$heading)); ?></li>
        <?php } ?>
        <?php foreach($results as $_result){
            $data = '';
            switch($heading){
                case 'staff':
                $data = '<a href="'.admin_url('staff/member/'.$_result['staffid']).'">'.$_result['firstname']. ' ' . $_result['lastname'] .'</a>';
                break;
                case 'expediteurs':
                $data = '<a href="'.admin_url('expediteurs/expediteur/'.$_result['id']).'">'.$_result['nom'].'</a>';
                break;
                case 'colis':
                $data = '<a href="'.admin_url('colis').'">'.$_result['code_barre'].'</a>';
                break;
            }
            ?>
            <li><?= $data; ?></li>
            <?php } ?>
            <?php } ?>
            <?php if($total == 0){ ?>
            <li class="padding-5 text-center">No Results Found</li>
            <?php } ?>
        </ul>


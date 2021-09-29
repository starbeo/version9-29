<?php
if(!isset($filter_table_name)){
    $filter_table_name = '.table-expenses';
}
?>
<div class="_filters _hidden_inputs hidden">
   <?= form_hidden('billable');
   echo form_hidden('non-billable');
   echo form_hidden('invoiced');
   echo form_hidden('unbilled');
   echo form_hidden('recurring');
   foreach($years as $year){
    echo form_hidden('year_'.$year['year'],$year['year']);
}
for ($m = 1; $m <= 12; $m++) {
   echo form_hidden('expenses_by_month_'.$m);
}
foreach($categories as $category){
 echo form_hidden('expenses_by_category_'.$category['id']);
}
?>
</div>
<div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?= _l('filter_by'); ?>">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-filter" aria-hidden="true"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-right width300">
        <li>
            <a href="#" data-cview="all" onclick="dt_custom_view_1('','<?= $filter_table_name; ?>',''); return false;">
                <?= _l('expenses_list_all'); ?>
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="#" data-cview="billable" onclick="dt_custom_view_1('billable','<?= $filter_table_name; ?>','billable'); return false;">
                <?= _l('expenses_list_billable'); ?>
            </a>
        </li>
        <li>
            <a href="#" data-cview="non-billable" onclick="dt_custom_view_1('non-billable','<?= $filter_table_name; ?>','non-billable'); return false;">
                <?= _l('expenses_list_non_billable'); ?>
            </a>
        </li>
        <li>
        <a href="#" data-cview="invoiced" onclick="dt_custom_view_1('invoiced','<?= $filter_table_name; ?>','invoiced'); return false;">
                <?= _l('expenses_list_invoiced'); ?>
            </a>
        </li>
        <li>
            <a href="#" data-cview="unbilled" onclick="dt_custom_view_1('unbilled','<?= $filter_table_name; ?>','unbilled'); return false;">
                <?= _l('expenses_list_unbilled'); ?>
            </a>
        </li>
        <li>
            <a href="#" data-cview="recurring" onclick="dt_custom_view_1('recurring','<?= $filter_table_name; ?>','recurring'); return false;">
                <?= _l('expenses_list_recurring'); ?>
            </a>
        </li>
        <?php if(count($years) > 0){ ?>
            <li class="divider"></li>
            <?php foreach($years as $year){ ?>
                <li class="active">
                    <a href="#" data-cview="year_<?= $year['year']; ?>" onclick="dt_custom_view_1(<?= $year['year']; ?>,'<?= $filter_table_name; ?>','year_<?= $year['year']; ?>'); init_expenses_total(); return false;"><?= $year['year']; ?>
                    </a>
                </li>
                <?php } ?>
                <?php } ?>
                <?php if(count($categories) > 0){ ?>
                   <div class="clearfix"></div>
                   <li class="divider"></li>
                   <li class="dropdown-submenu pull-left">
                     <a href="#" tabindex="-1"><?= _l('expenses_filter_by_categories'); ?></a>
                     <ul class="dropdown-menu dropdown-menu-left">
                        <?php foreach($categories as $category){ ?>
                            <li>
                                <a href="#" data-cview="expenses_by_category_<?= $category['id']; ?>" onclick="dt_custom_view_1(<?= $category['id']; ?>,'<?= $filter_table_name; ?>','expenses_by_category_<?= $category['id']; ?>'); init_expenses_total(); return false;"><?= $category['name']; ?></a>
                            </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <div class="clearfix"></div>
                    <li class="divider"></li>
                    <li class="dropdown-submenu pull-left">
                      <a href="#" tabindex="-1"><?= _l('months'); ?></a>
                      <ul class="dropdown-menu dropdown-menu-left">
                        <?php for ($m = 1; $m <= 12; $m++) { ?>
                          <li><a href="#" data-cview="expenses_by_month_<?= $m; ?>" onclick="dt_custom_view_1(<?= $m; ?>,'<?= $filter_table_name; ?>','expenses_by_month_<?= $m; ?>'); init_expenses_total(); return false;"><?= _l(date('F', mktime(0, 0, 0, $m, 1))); ?></a></li>
                          <?php } ?>
                      </ul>
                  </li>
              </ul>
          </div>

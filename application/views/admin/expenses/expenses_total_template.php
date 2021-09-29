<div class="row">
    <?php if(isset($currencies)){
        $col = 'col-md-2 col-xs-12 ';
        ?>
        <div class="<?= $col; ?> stats-total-currency">
            <div class="panel_s">
                <div class="panel-body">
                    <select class="selectpicker" name="expenses_total_currency" onchange="init_expenses_total();" data-width="100%" data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                        <?php foreach($currencies as $currency){
                            $selected = '';
                              if(!$this->input->post('currency')){
                         if($currency['isdefault'] == 1 || isset($_currency) && $_currency == $currency['id']){
                           $selected = 'selected';
                         }
                       } else {
                         if($this->input->post('currency') == $currency['id']){
                          $selected = 'selected';
                        }
                       }
                           ?>
                           <option value="<?= $currency['id']; ?>" <?= $selected; ?> data-subtext="<?= $currency['name']; ?>"><?= $currency['symbol']; ?></option>
                           <?php } ?>
                       </select>
                   </div>
               </div>
           </div>
           <?php
       } else {
         $col = 'col-md-5ths col-xs-12 ';
     }
     ?>
     <div class="<?= $col ;?>total-column">
        <div class="panel_s">
            <div class="panel-body">
                <h3 class="text-muted _total">
                 <?= $totals['all']['total']; ?>
             </h3>
             <span class="text-warning"><?= _l('expenses_total'); ?></span>
         </div>
     </div>
 </div>
 <div class="<?= $col ;?>total-column">
    <div class="panel_s">
        <div class="panel-body">
            <h3 class="text-muted _total">
             <?= $totals['billable']['total']; ?>
         </h3>
         <span class="text-success"><?= _l('expenses_list_billable'); ?></span>
     </div>
 </div>
</div>
<div class="<?= $col ;?>total-column">
    <div class="panel_s">
        <div class="panel-body">
            <h3 class="text-muted _total">
             <?= $totals['non_billable']['total']; ?>
         </h3>
         <span class="text-warning"><?= _l('expenses_list_non_billable'); ?></span>
     </div>
 </div>
</div>
<div class="<?= $col ;?>total-column">
    <div class="panel_s">
        <div class="panel-body">
            <h3 class="text-muted _total">
             <?= $totals['unbilled']['total']; ?>
         </h3>
         <span class="text-danger"><?= _l('expenses_list_unbilled'); ?></span>
     </div>
 </div>
</div>
<div class="<?= $col ;?>total-column">
    <div class="panel_s">
        <div class="panel-body">
            <h3 class="text-muted _total">
             <?= $totals['billed']['total']; ?>
         </h3>
         <span class="text-success"><?= _l('expense_billed'); ?></span>
     </div>
 </div>
</div>
</div>
<div class="clearfix"></div>
<script>
    init_selectpicker();
</script>

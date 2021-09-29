<div class="col-md-12 no-padding animated fadeIn">
  <div class="panel_s">
    <div class="panel-body padding-16">
      <ul class="nav nav-tabs no-margin" role="tablist">
        <li role="presentation" class="active">
          <a href="#tab_expense" aria-controls="tab_expense" role="tab" data-toggle="tab">
            <?= _l('expense'); ?>
          </a>
        </li>
      </ul>
    </div>
  </div>
  <div class="panel_s">
    <div class="panel-body">
      <div class="row">
        <div class="col-md-7">
          <h3 class="bold no-margin"><?= $expense->category_name; ?></h3>
        </div>
        <div class="col-md-5 text-right">
        <div class="pull-right">
          <?php if(has_permission('expenses','','edit')){ ?>
          <a class="btn btn-default mright5" href="<?= admin_url('expenses/expense/'.$expense->expenseid); ?>" data-toggle="tooltip" data-placement="bottom" title="<?= _l('expense_edit'); ?>"><i class="fa fa-pencil-square-o"></i></a>
          <?php } ?>
          <?php if(has_permission('expenses','','delete')){ ?>
          <a class="btn btn-default mright5" href="<?= admin_url('expenses/delete/'.$expense->expenseid); ?>" data-toggle="tooltip" data-placement="bottom" title="<?= _l('expense_delete'); ?>"><i class="fa fa-remove"></i></a>
          <?php } ?>
          <?php if(has_permission('expenses','','create')){ ?>
          <a class="btn btn-default mright5" href="<?= admin_url('expenses/copy/'.$expense->expenseid); ?>" data-toggle="tooltip" data-placement="bottom" title="<?= _l('expense_copy'); ?>"><i class="fa fa-clone"></i></a>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <hr />
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane ptop10 active" id="tab_expense">
       <div class="row">
        <div class="col-md-6">
          <p><?= _l('expense_amount'); ?> <span class="text-danger bold font-medium"><?= format_money($expense->amount,$base_currency->symbol); ?></span>
            <?php if($expense->paymentmode != 0){ ?>
            <br><span class="text-muted"><?= _l('paid_via').' : '; ?> <?= $expense->payment_mode_name; ?></span>
            <?php } ?>
          </p>
          <p><?= _l('expense_date'); ?> <span class="text-muted"><?= _d($expense->date); ?></span></p>
          <br />
          <br />
          <?php if($expense->livreurid != NULL){ ?>
          <p><?= _l('expense_delivery_man'); ?></p>
          <p><a href="<?= admin_url('staff/member/'.$expense->livreurid); ?>"><?= $expense->firstname. ' ' .$expense->lastname; ?></a></p>
          <?php } ?>
          <?php if($expense->note != ''){ ?>
          <p><?= _l('expense_note'); ?></p>
          <p class="text-muted"><?= $expense->note; ?></p>
          <?php } ?>
        </div>
        <div class="col-md-6">
         <h4 class="bold text-muted no-margin"><?= _l('expense_receipt'); ?></h4>
         <hr />
         <?php if(empty($expense->attachment)) { ?>
         <?= form_open('admin/expenses/add_expense_attachment/'.$expense->expenseid,array('class'=>'mtop10 dropzone dropzone-expense-preview dropzone-manual','id'=>'expense-receipt-upload')); ?>
         <div id="dropzoneDragArea" class="dz-default dz-message">
          <span><?= _l('expense_add_edit_attach_receipt'); ?></span>
        </div>
        <?= form_close(); ?>
        <?php }  else { ?>
        <div class="row">
          <div class="col-md-10">
           <i class="<?= get_mime_class($expense->filetype); ?>"></i> <a href="<?= site_url('download/file/expense/'.$expense->expenseid); ?>"> <?= $expense->attachment; ?></a>
         </div>
         <div class="col-md-2 text-right">
          <a href="<?= admin_url('expenses/delete_expense_attachment/'.$expense->expenseid .'/'.'preview'); ?>" class="text-danger"><i class="fa fa-trash-o"></i></a>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
</div>
</div>
</div>
</div>
</div>
<script>
  if($('#dropzoneDragArea').length > 0){
    var expenseDropzone = new Dropzone("#expense-receipt-upload", {
      clickable: '#dropzoneDragArea',
      maxFiles: 1,
      success:function(file,response){
        init_expense(<?= $expense->expenseid; ?>);
      }
    });
  }
</script>


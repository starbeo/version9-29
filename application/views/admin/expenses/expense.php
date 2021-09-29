<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <?= form_open_multipart($this->uri->uri_string(),array('id'=>'expense-form','class'=>'dropzone dropzone-manual')) ;?>
            <div class="col-md-12">
                <div class="panel-heading bold">
                    <?= $title; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">

                  <div class="row">
                  <?php if(has_permission('expenses','','create')){ $col1 = 7; }else{ $col1 = 12; }
                  ?>
                  <div class="col-md-<?= $col1; ?>">
                  <?php $selected = (isset($expense) ? $expense->category : ''); ?>
                  <?= render_select('category',$categories,array('id','name'),'expense_category',$selected); ?>
                  </div>
                  <?php if(has_permission('expenses','','create')){ ?>
                  <div class="col-md-5">
                    <a href="#" class="btn pull-left btn-primary mtop25" data-toggle="modal" data-target="#add_category_expense_modal"><?= _l('new_expense_category'); ?></a>
                  </div>
                  <?php } ?>
                  </div>

                  <?php $value = (isset($expense) ? $expense->note : ''); ?>
                  <?= render_textarea('note','expense_add_edit_note',$value,array('rows'=>1),array()); ?>

                  <?php $value = (isset($expense) ? $expense->date : date('Y-m-d')); ?>
                  <?= render_date_input('date','expense_add_edit_date',$value); ?>
                  <label for="amount"><?= _l('expense_add_edit_amount'); ?></label>
                  <div class="input-group" data-toggle="tooltip" title="<?= _l('expense_add_edit_amount_tooltip'); ?>">
                    <input type="number" class="form-control" name="amount" value="<?php if(isset($expense)){echo $expense->amount; }?>">
                    <div class="input-group-addon">
                        <?= $base_currency->symbol; ?>
                    </div>
                </div>
                <div class="clearfix mtop15"></div>

                <?php $selected = (isset($expense) ? $expense->livreurid : ''); ?>
                <?= render_select('livreurid',$livreurs,array('staffid',array('firstname', 'lastname')),'expense_add_edit_delivery_man',$selected); ?>

                <?php $selected = (isset($expense) ? $expense->paymentmode : ''); ?>
                <?= render_select('paymentmode',$payment_modes,array('id','name'),'payment_mode',$selected); ?>
                <div class="clearfix mtop15"></div>
                <button type="submit" class="btn btn-primary pull-right mtop15"><?= _l('submit'); ?></button>
                </div>
              </div>
            </div>

            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                      
                        <?php if(isset($expense) && $expense->attachment !== ''){ ?>
                        <div class="row">
                            <div class="col-md-10">
                              <i class="<?= get_mime_class($expense->filetype); ?>"></i> 
                              <a href="<?= site_url('download/file/expense/'.$expense->expenseid); ?>"><?= $expense->attachment; ?></a>
                            </div>
                            <div class="col-md-2 text-right">
                                <a href="<?= admin_url('expenses/delete_expense_attachment/'.$expense->expenseid); ?>" class="text-danger"><i class="fa fa-trash-o"></i></a>
                            </div>
                        </div>
                        <hr />
                        <?php } ?>

                        <?php if(!isset($expense) || (isset($expense) && $expense->attachment == '')){ ?>
                        <div id="dropzoneDragArea" class="dz-default dz-message">
                          <span><?= _l('expense_add_edit_attach_receipt'); ?></span>
                      </div>
                      <div class="dropzone-previews"></div>
                      <?php } ?>
                    </div>
                </div>
            </div>
    </div>
    <?= form_close(); ?>
</div>
</div>
</div>
<?php $this->load->view('admin/expenses/modals/add_category_expense'); ?>
<?php init_tail(); ?>
<script>
Dropzone.autoDiscover = false;
if($('#dropzoneDragArea').length > 0){
    var expenseDropzone = new Dropzone("#expense-form", {
        autoProcessQueue: false,
        clickable: '#dropzoneDragArea',
        previewsContainer: '.dropzone-previews',
        addRemoveLinks: true,
        maxFiles: 1,
    });
}

$(document).ready(function(){
    _validate_form($('form'),{
        category:'required',
        date:'required',
        amount:'required'
    },expenseSubmitHandler);
});

function expenseSubmitHandler(form){
    $.post(form.action, $(form).serialize()).success(function(response) {
        response = $.parseJSON(response);
        if (response.expenseid) {
            if(typeof(expenseDropzone) !== 'undefined'){
                if (expenseDropzone.getQueuedFiles().length > 0) {
                    expenseDropzone.options.url = admin_url + 'expenses/add_expense_attachment/' + response.expenseid;
                    $.when(expenseDropzone.processQueue()).then(window.location.assign(response.url));
                } else {
                    window.location.assign(response.url)
                }
            } else {
                window.location.assign(response.url)
            }
        }
    });

    return false;
}
</script>
<?php $this->load->view('admin/expenses/modals/script_modal_add_category_expense'); ?>
</body>
</html>

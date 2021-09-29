                    <!-- Category Expense modal -->
                    <div class="modal fade" id="add_category_expense_modal" tabindex="-1" role="dialog">
                        <div class="modal-dialog">
                            <?= form_open(admin_url('expenses/add_category_ajax'), array('id'=>'form-add-category-expense')); ?>
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">
                                        <span><?= _l('new_expense_category'); ?></span>
                                    </h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div id="additional"></div>
                                            <?= render_input('name','expense_add_edit_name'); ?>
                                            <?= render_textarea('description','expense_add_edit_description'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                                    <button id="submit" type="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
                                </div>
                            </div><!-- /.modal-content -->
                            <?= form_close(); ?>
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
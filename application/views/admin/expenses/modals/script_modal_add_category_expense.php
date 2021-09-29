
<script>
$(document).ready(function(){
    $('#add_category_expense_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        $('#add_category_expense_modal input').val('');
        $('#add_category_expense_modal textarea').val('');
        $('#add_category_expense_modal button[id="submit"]').attr('disabled', false);
    });
    _validate_form($('#form-add-category-expense'), {
        name: 'required'
    }, manage_category_expense);

    function manage_category_expense(form) {
        $('#add_category_expense_modal button[id="submit"]').attr('disabled', true);
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).success(function(response) {
            response = $.parseJSON(response);
            if (response.success == true) {
                $('select[name="category"]').append('<option value="'+response.categoryid+'">'+response.name+'</option>');
                $('select[name="category"]').selectpicker('refresh');
                $('select[name="category"]').selectpicker('val', response.categoryid);
                alert_float('success', response.message);
            }
            $('#add_category_expense_modal').modal('hide');
        });

        return false;
    }
});
</script>
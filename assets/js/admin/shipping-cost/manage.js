$(document).ready(function () {
    // Init data table for shipping cost
    initDataTable('.table-shipping-cost', window.location.href, 'locations', [4], [4]);

    _validate_form($('#form-shipping-cost'), {
        name: 'required',
        shipping_cost: 'required'
    }, manage_shipping_cost);

    $('#shipping_cost_modal').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#shipping_cost_modal .add-title').removeClass('hide');
        $('#shipping_cost_modal .edit-title').addClass('hide');
        $('#shipping_cost_modal input').val('');
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $('#shipping_cost_modal input[name="id"]').val(id);
            $('#shipping_cost_modal .add-title').addClass('hide');
            $('#shipping_cost_modal .edit-title').removeClass('hide');
            $('#shipping_cost_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
            $('#shipping_cost_modal input[name="shipping_cost"]').val($(invoker).parents('tr').find('td').eq(1).text());
        }
    });
});

function manage_shipping_cost(form)
{
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        if (response.success === true) {
            $('.table-shipping-cost').DataTable().ajax.reload();
            alert_float('success', response.message);
        }
        $('#shipping_cost_modal').modal('hide');
    });

    return false;
}

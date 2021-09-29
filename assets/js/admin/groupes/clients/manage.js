$(document).ready(function () {
    // Init data table groupes
    var headers_groupes = $('.table-groupes').find('th');
    var not_sortable_groupes = (headers_groupes.length - 1);
    initDataTable('.table-groupes', window.location.href, 'Groupes', [not_sortable_groupes], [not_sortable_groupes]);
    // Validate form groupe
    _validate_form($('form'), {
        name: 'required'
    }, manage_groupe);
    // Show modal add & edit groupe
    $('#groupe_modal').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#groupe_modal .add-title').removeClass('hide');
        $('#groupe_modal .edit-title').addClass('hide');
        $('#groupe_modal input').val('');
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $('#groupe_modal input[name="id"]').val(id);
            $('#groupe_modal .add-title').addClass('hide');
            $('#groupe_modal .edit-title').removeClass('hide');
            $('#groupe_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
    // Show modal add & edit affectation point relai to staff
    $('#group_to_customer_modal').on('show.bs.modal', function () {
        $('#group_to_customer_modal select').selectpicker('refresh');
        $('#group_to_customer_modal button[id="submit-form-affectation-group-to-customer"]').attr('disabled', false);
    });
    // Validate form affectation group to customer
    _validate_form($('#form-affectation-group-to-customer'), {
        groupe: 'required',
        clients: 'required'
    }, manage_affectation_group_to_customer);
});
// Manage groupe
function manage_groupe(form)
{
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        alert_float(response.type, response.message);
        if (response.success === true) {
            $('.table-groupes').DataTable().ajax.reload();
        }
        $('#groupe_modal').modal('hide');
    });

    return false;
}
// Manage affectation group to customer
function manage_affectation_group_to_customer(form) {
    $('#group_to_customer_modal button[id="submit-form-affectation-group-to-customer"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        if (response.success === true) {
            $('.table-groupes').DataTable().ajax.reload();
            alert_float('success', response.message);
        }
        $('#group_to_customer_modal').modal('hide');
    });

    return false;
}

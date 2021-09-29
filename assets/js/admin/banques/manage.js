$(document).ready(function () {
    // Init data table banques
    var headers_banques = $('.table-banques').find('th');
    var not_sortable_banques = (headers_banques.length - 1);
    initDataTable('.table-banques', window.location.href, 'Banques', [not_sortable_banques], [not_sortable_banques]);
    // Validate form banque
    _validate_form($('form'), {
        name: 'required'
    }, manage_banque);
    // Show modal add & edit banque
    $('#banque_modal').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#banque_modal .add-title').removeClass('hide');
        $('#banque_modal .edit-title').addClass('hide');
        $('#banque_modal input').val('');
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $('#banque_modal input[name="id"]').val(id);
            $('#banque_modal .add-title').addClass('hide');
            $('#banque_modal .edit-title').removeClass('hide');
            $('#banque_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
});
// Manage banque
function manage_banque(form)
{
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        alert_float(response.type, response.message);
        if (response.success === true) {
            $('.table-banques').DataTable().ajax.reload();
        }
        $('#banque_modal').modal('hide');
    });

    return false;
}

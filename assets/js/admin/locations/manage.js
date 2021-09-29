$(document).ready(function () {
    _validate_form($('form'), {
        name: 'required'
    }, manage_location);
    initDataTable('.table-locations', window.location.href, 'locations', [1], [1]);

    $('#location_modal').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#location_modal .add-title').removeClass('hide');
        $('#location_modal .edit-title').addClass('hide');
        $('#location_modal input').val('');
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $('#location_modal input[name="id"]').val(id);
            $('#location_modal .add-title').addClass('hide');
            $('#location_modal .edit-title').removeClass('hide');
            $('#location_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
});
function manage_location(form)
{
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        if (response.success === true) {
            $('.table-locations').DataTable().ajax.reload();
            alert_float('success', response.message);
        }
        $('#location_modal').modal('hide');
    });

    return false;
}
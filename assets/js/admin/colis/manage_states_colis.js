$(document).ready(function () {
    _validate_form($('form'), {
        name: 'required'
    }, manage_location);
    initDataTable('.table-states-colis', window.location.href, 'states-colis', [1], [1]);
});

function manage_location(form) {
    $('#states_colis_modal button[id="submit"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        if (response.success === true) {
            $('.table-states-colis').DataTable().ajax.reload();
            alert_float('success', response.message);
        }
        $('#states_colis_modal').modal('hide');
    });

    return false;
}

$('#states_colis_modal').on('show.bs.modal', function (e) {
    var invoker = $(e.relatedTarget);
    var id = $(invoker).data('id');
    $('#states_colis_modal .add-title').removeClass('hide');
    $('#states_colis_modal .edit-title').addClass('hide');
    $('#states_colis_modal input').val('');
    $('#states_colis_modal button[id="submit"]').attr('disabled', false);
    // is from the edit button
    if (typeof (id) !== 'undefined') {
        $('#states_colis_modal input[name="id"]').val(id);
        $('#states_colis_modal .add-title').addClass('hide');
        $('#states_colis_modal .edit-title').removeClass('hide');
        $('#states_colis_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
    }
});
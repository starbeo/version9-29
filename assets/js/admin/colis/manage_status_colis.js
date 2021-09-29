$(document).ready(function () {
    var headers_status_colis = $('.table-status-colis').find('th');
    var not_sortable_status_colis = (headers_status_colis.length - 1);
    initDataTable('.table-status-colis', window.location.href, 'status-colis', [not_sortable_status_colis], [not_sortable_status_colis]);
    
    _validate_form($('form'), {
        name: 'required'
    }, manage_location);

    $('#status_colis_modal').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#status_colis_modal .add-title').removeClass('hide');
        $('#status_colis_modal .edit-title').addClass('hide');
        $('#status_colis_modal input').val('');
        //Disabled checked input
        $('#status_colis_modal input[name="show_in_delivery_app"]').prop('checked', false);
        $('#status_colis_modal button[id="submit"]').attr('disabled', false);

        // is from the edit button
        if (typeof (id) !== 'undefined') {
            var color = $(invoker).data('color');
            var showInDeliveryApp = $(invoker).data('show-in-delivery-app');
            $('#status_colis_modal .add-title').addClass('hide');
            $('#status_colis_modal .edit-title').removeClass('hide');
            $('#status_colis_modal input[name="id"]').val(id);
            $('#status_colis_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
            $('#status_colis_modal input[name="color"]').val(color);
            if (parseInt(showInDeliveryApp) === 1) {
                $('#status_colis_modal input[name="show_in_delivery_app"]').prop('checked', true);
            }
        }
    });
});

function manage_location(form) {
    $('#status_colis_modal button[id="submit"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        if (response.success === true) {
            $('.table-status-colis').DataTable().ajax.reload();
            alert_float('success', response.message);
        }
        $('#status_colis_modal').modal('hide');
    });

    return false;
}
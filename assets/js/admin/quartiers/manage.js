$(document).ready(function () {
    // Init data table quartiers
    var headers_quartiers = $('.table-quartiers').find('th');
    var not_sortable_quartiers = (headers_quartiers.length - 1);
    initDataTable('.table-quartiers', window.location.href, 'quartiers', [not_sortable_quartiers], [not_sortable_quartiers]);

    _validate_form($('form'), {
        ville_id: 'required',
        name: 'required'
    }, manage_quartier);

    $('#quartier_modal').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#quartier_modal .add-title').removeClass('hide');
        $('#quartier_modal .edit-title').addClass('hide');
        $('#quartier_modal input').val('');
        $('#quartier_modal select').selectpicker('val', '');
        $('#quartier_modal button[id="submit"]').attr('disabled', false);
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            var city = $(invoker).data('ville');
            $('#quartier_modal input[name="id"]').val(id);
            $('#quartier_modal .add-title').addClass('hide');
            $('#quartier_modal .edit-title').removeClass('hide');
            $('#quartier_modal select[name="ville_id"]').selectpicker('val', city);
            $('#quartier_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
        }
    });
});
function manage_quartier(form) {
    $('#quartier_modal button[id="submit"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        alert_float(response.type, response.message);
        if (response.success === true) {
            $('.table-quartiers').DataTable().ajax.reload();
        }
        $('#quartier_modal').modal('hide');
    });

    return false;
}

$(document).ready(function () {
    // Init data table for reclamations
    initDataTable('.table-reclamations', window.location.href, 'reclamations', [6], [6], 'undefined', [3, 'DESC']);

    _validate_form($('form'), {
        reponse: 'required'
    }, manage_reponse_reclamation);

    $('#reclamation').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#reclamation input[name="id"]').val('');
        $('#reclamation h5[name="objet"]').html('');
        $('#reclamation h5[name="message"]').html('');
        $('#reclamation textarea[name="reponse"]').val('');
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $.post(admin_url + "reclamations/get_info_reclamations/" + id, function (response) {
                var data = jQuery.parseJSON(response);
                $('#reclamation input[name="id"]').val(id);
                $('#reclamation h5[name="objet"]').append(data.objet);
                $('#reclamation h5[name="message"]').append(data.message);
                $('#reclamation textarea[name="reponse"]').val(data.reponse);
            });
        }
    });
});

function manage_reponse_reclamation(form) {
    var data = $(form).serialize();
    var url = form.action;

    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        if (response.success === true) {
            alert_float('success', response.message);
        } else if (response.success === 'access_denied') {
            alert_float('warning', response.message);
        }
        $('.table-reclamations').DataTable().ajax.reload();
        $('#reclamation').modal('hide');
    });
    return false;
}

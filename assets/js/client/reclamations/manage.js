$(document).ready(function () {
    // Init data table reclamations
    var headers_reclamations = $('.table-demandes').find('th');
    var not_sortable_reclamations = (headers_reclamations.length - 1);
    var ReclamationsServerParams = {
        "f-etat": "[name='f-etat']",
        "f-date-created": "[name='f-date-created']",
        "f-date-traitement": "[name='f-date-traitement']"
    };
    initDataTable('.table-reclamations', window.location.href, 'Reclamations', [not_sortable_reclamations], [not_sortable_reclamations], ReclamationsServerParams, [2, 'DESC']);
    // Validate form reclamation
    _validate_form($('form'), {
        objet: 'required',
        message: 'required'
    }, manage_reclamation);
    // Show & Hide reclamation
    $('#reclamation').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        var action = $(invoker).data('action');
        $('#reclamation input').val('');
        $('#reclamation textarea').val('');
        if (!$('#bloc-field-answer').hasClass('display-none')) {
            $('#bloc-field-answer').addClass('display-none');
        }
        if ($('#submit-form-reclamation').hasClass('display-none')) {
            $('#submit-form-reclamation').removeClass('display-none');
        }
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $.post(client_url + "reclamations/get_reclamation/" + id, function (response) {
                var response = $.parseJSON(response);
                $('#reclamation input[name="id"]').val(id);
                $('#reclamation input[name="objet"]').val(response.objet);
                $('#reclamation textarea[name="message"]').val(response.message);
                if (action === 'show') {
                    $('#reclamation textarea[name="reponse"]').val(response.reponse);
                    if ($('#bloc-field-answer').hasClass('display-none')) {
                        $('#bloc-field-answer').removeClass('display-none');
                    }
                    if (!$('#submit-form-reclamation').hasClass('display-none')) {
                        $('#submit-form-reclamation').addClass('display-none');
                    }
                }
            });
        }
    });
});
// Manage reclamation
function manage_reclamation(form) {
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
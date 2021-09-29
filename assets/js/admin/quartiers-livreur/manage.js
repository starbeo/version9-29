$(document).ready(function () {
    // Init data table for quartiers livreur
    var headers_quartiers_livreur = $('.table-quartiers-livreur').find('th');
    var not_sortable_quartiers_livreur = (headers_quartiers_livreur.length - 1);
    initDataTable('.table-quartiers-livreur', window.location.href, 'quartiers_livreur', [not_sortable_quartiers_livreur], [not_sortable_quartiers_livreur]);
    var hidden_columns_quartiers_livreur = [0];
    $.each(hidden_columns_quartiers_livreur, function (i, val) {
        var column_quartiers_livreur = $('.table-quartiers-livreur').DataTable().column(val);
        column_quartiers_livreur.visible(false);
    });

    _validate_form($('form'), {
        quartier_id: 'required',
        livreur_id: 'required'
    }, manage_quartier_livreur);

    $('#quartier_livreur_modal').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#quartier_livreur_modal .add-title').removeClass('hide');
        $('#quartier_livreur_modal .edit-title').addClass('hide');
        $('#quartier_livreur_modal input').val('');
        $('#quartier_livreur_modal select').selectpicker('val', '');
        $('#quartier_livreur_modal button[id="submit"]').attr('disabled', false);
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            var quartier_id = $(invoker).data('quartier-id');
            var livreur_id = $(invoker).data('livreur-id');
            $('#quartier_livreur_modal input[name="id"]').val(id);
            $('#quartier_livreur_modal .add-title').addClass('hide');
            $('#quartier_livreur_modal .edit-title').removeClass('hide');
            $('#quartier_livreur_modal select[name="quartier_id"]').selectpicker('val', quartier_id);
            $('#quartier_livreur_modal select[name="livreur_id"]').selectpicker('val', livreur_id);
        }
    });
});

function manage_quartier_livreur(form) {
    $('#quartier_livreur_modal button[id="submit"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        $('.table-quartiers-livreur').DataTable().ajax.reload();
        if (response.success === true) {
            alert_float('success', response.message);
        } else {
            alert_float(response.success, response.message);
        }
        $('#quartier_livreur_modal').modal('hide');
    });

    return false;
}

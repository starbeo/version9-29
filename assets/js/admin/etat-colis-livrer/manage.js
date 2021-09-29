$(document).ready(function () {
    // Init data table etat colis livrer
    var headers_etat_colis_livrer = $('.table-etat-colis-livrer').find('th');
    var not_sortable_etat_colis_livrer = (headers_etat_colis_livrer.length - 1);
    var EtatColisLivrerServerParams = {
        "f-type-livraison": "[name='f-type-livraison']",
        "f-livreur": "[name='f-livreur']",
        "f-user-point-relais": "[name='f-user-point-relais']",
        "f-utilisateur": "[name='f-utilisateur']",
        "f-date-created": "[name='f-date-created']"
    };
    initDataTable('.table-etat-colis-livrer', window.location.href, 'Etats Colis Livrer', [0, not_sortable_etat_colis_livrer], [0, not_sortable_etat_colis_livrer], EtatColisLivrerServerParams);
    // On click button filter
    $('body').on('click', '.btn-filtre', function () {
        if ($('#filtre-table').hasClass('display-none')) {
            $('#filtre-table').removeClass('display-none');
        } else {
            $('#filtre-table').addClass('display-none');
        }
    });
    // On submit filter
    $('body').on('click', '#filtre-submit', function () {
        $('.table-etat-colis-livrer').DataTable().ajax.reload();
        $('#filtre-table').addClass('display-none');
    });
    // Reset filter
    $('body').on('click', '#filtre-reset', function () {
        $('#filtre-table select').selectpicker('val', '');
        $('#filtre-table input').val('');
        $('.table-etat-colis-livrer').DataTable().ajax.reload();
    });
    // On click checkbox all etat
    $('body').on('change', '#checkbox-all-etat', function () {
        if ($('#checkbox-all-etat').is(':checked') === true) {
            $('.checkbox-etat').prop('checked', true);
        } else {
            $('.checkbox-etat').prop('checked', false);
        }
    });
    // On click
    $('body').on('click', '.paginate_button a', function () {
        $('#checkbox-all-etat').prop('checked', false);
    });
    // On click button upload etat colis livrer
    $('body').on('click', '.btn-upload-etat-colis-livrer', function () {
        if ($('#upload-etat-colis-livrer').hasClass('display-none') === true) {
            $('#upload-etat-colis-livrer').removeClass('display-none');
        } else {
            $('#upload-etat-colis-livrer').addClass('display-none');
        }
    });
    // On click button submit export
    $('body').on('click', '.btn-submit-export', function () {
        var id = $(this).attr('id');
        if(id === 'btn-submit-export-excel') {
            $('#upload-etat-colis-livrer-form').attr('action', admin_url + 'etat_colis_livrer/export_excel');
        } else if(id === 'btn-submit-export-pdf') {
            $('#upload-etat-colis-livrer-form').attr('action', admin_url + 'etat_colis_livrer/export_pdf');
        }
    });
    // Validate form upload etat colis livrer
    _validate_form($('#upload-etat-colis-livrer-form'), {
        date_start: 'required'
    });
    // Payments history
    $('#historique_versements').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var etatColisLivrerId = $(invoker).data('etat-colis-livrer-id');
        $('#historique_versements input[name="f-etat-colis-livrer"]').val(etatColisLivrerId);

        var count = $('#historique_versements input[name="historique-versements-count"]').val();
        if (parseInt(count) === 0) {
            $('#historique_versements input[name="historique-versements-count"]').val(1);
            //Init Data Table Historiques Status Coli
            var HistoriquesVersementsServerParams = {
                "f-etat-colis-livrer": "[name='f-etat-colis-livrer']"
            };
            initDataTable('.table-historiques-versements', admin_url + 'versements/livreurs', 'historiques-versements', 'undefined', 'undefined', HistoriquesVersementsServerParams);
            // Hide columns table historiques versements
            var hidden_historiques_versements_columns = [0];
            $.each(hidden_historiques_versements_columns, function (i, val) {
                var column_historiques_versements = $('.table-historiques-versements').DataTable().column(val);
                column_historiques_versements.visible(false);
            });
        } else {
            $('.table-historiques-versements').DataTable().ajax.reload();
        }
    });
});

function change_status_etat_colis_livrer(toType) {
    if (toType === 'en_attente' || toType === 'valider') {
        var data = $('#change-status-etat-colis-livrer-form input[name="ids[]"]').serialize();
        var status = 0;
        if (toType === 'en_attente') {
            status = 1;
        } else if (toType === 'valider') {
            status = 2;
        }
        $.post(admin_url + 'etat_colis_livrer/change_status/' + status, data).success(function (response) {
            response = $.parseJSON(response);
            alert_float(response.type, response.message);
            if (response.success === true) {
                $('.table-etat-colis-livrer').DataTable().ajax.reload();
            }
        });
    }

    return false;
}

function change_etat_etat_colis_livrer(toType) {
    if (toType === 'non_regle' || toType === 'regle') {
        var data = $('#change-status-etat-colis-livrer-form input[name="ids[]"]').serialize();
        var etat = 0;
        if (toType === 'non_regle') {
            etat = 1;
        } else if (toType === 'regle') {
            etat = 2;
        }
        $.post(admin_url + 'etat_colis_livrer/change_etat/' + etat, data).success(function (response) {
            response = $.parseJSON(response);
            alert_float(response.type, response.message);
            if (response.success === true) {
                $('.table-etat-colis-livrer').DataTable().ajax.reload();
            }
        });
    }

    return false;
}

$(document).ready(function () {
    // Validate form contrat
    _validate_form($('#form-contract'), {
        datestart: 'required',
        client_id: 'required',
        fullname: 'required',
        contact: 'required',
        address: 'required',
        frais_livraison_interieur: 'required',
        frais_livraison_exterieur: 'required',
        date_created_client: 'required'
    });
    // Get client
    $('body').on('change', 'select[name="client_id"]', function () {
        var clientId = $('select[name="client_id"]').selectpicker('val');
        if (!$('#bloc-infos-client').hasClass('display-none')) {
            $('#bloc-infos-client').addClass('display-none');
        }
        $('#form-contract input[name="fullname"]').val('');
        $('#form-contract input[name="contact"]').val('');
        $('#form-contract input[name="address"]').val('');
        $('#form-contract input[name="frais_livraison_interieur"]').val('');
        $('#form-contract input[name="frais_livraison_exterieur"]').val('');
        $('#form-contract input[name="commercial_register"]').val('');
        $('#form-contract input[name="date_created_client"]').val('');
        if ($.isNumeric(clientId)) {
            $.post(admin_url + "expediteurs/get_expediteur_by_id/" + clientId, function (response) {
                var result = $.parseJSON(response);
                var dataExpediteur = result.expediteur;
                if (dataExpediteur !== null) {
                    $('#form-contract input[name="fullname"]').val(dataExpediteur['nom']);
                    $('#form-contract input[name="contact"]').val(dataExpediteur['contact']);
                    $('#form-contract input[name="address"]').val(dataExpediteur['adresse']);
                    $('#form-contract input[name="frais_livraison_interieur"]').val(dataExpediteur['frais_livraison_interieur']);
                    $('#form-contract input[name="frais_livraison_exterieur"]').val(dataExpediteur['frais_livraison_exterieur']);
                    $('#form-contract input[name="commercial_register"]').val(dataExpediteur['registre_commerce']);
                    $('#form-contract input[name="date_created_client"]').val(dataExpediteur['date_created']);
                    if ($('#bloc-infos-client').hasClass('display-none')) {
                        $('#bloc-infos-client').removeClass('display-none');
                    }
                }
            });
        }
    });
});

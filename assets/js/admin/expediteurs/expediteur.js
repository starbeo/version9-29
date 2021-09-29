$(document).ready(function () {
    //Validate Form 
    _validate_form($('#expediteur-form'), {
        nom: 'required',
        adresse: 'required',
        contact: 'required',
        code_parrainage: 'required',
        frais_livraison_interieur: 'required',
        frais_livraison_exterieur: 'required',
        frais_retourne: 'required',
        frais_refuse: 'required',
        frais_supplementaire: 'required',
        frais_stockage: 'required',
        frais_emballage: 'required',
        frais_etiquette: 'required',
        email: {
            required: true,
            remote: {
                url: admin_url + "expediteurs/client_email_exists",
                type: 'post',
                data: {
                    email: function () {
                        return $('input[name="email"]').val();
                    },
                    client_id: function () {
                        return $('input[id="clientid"]').val();
                    }
                }
            }
        }
    });
    //Validate rib
    $('body').on('change', 'input[name="rib"]', function () {
        var rib = $('input[name="rib"]').val();
        var number = rib.length;
        var isnum = /^\d+$/.test(rib);
        if (isnum === false) {
            alert_float('warning', 'Ce champs doit contenir que des chiffres');
        } else if (number !== 24) {
            alert_float('warning', 'Ce champs doit contenir 24 chiffres');
        } else {
            alert_float('success', 'RIB Validé');
        }
    });

    var clientid = $('input[id="clientid"]').val();
    if ($.isNumeric(clientid)) {
        //Init Data Table Colis
        var ColisServerParams = {
            "custom_view": "[name='custom_view']",
            "etat": "[name='etat']"
        };
        initDataTable('.table-colis-expediteur', admin_url + 'expediteurs/init_colis_expediteur/' + clientid, 'colis-expediteur', 'undefined', 'undefined', ColisServerParams);
        //Hide First Column
        var hidden_columns = [0];
        $.each(hidden_columns, function (i, val) {
            var column_status = $('.table-colis-expediteur').DataTable().column(val);
            column_status.visible(false);
        });
        //Init Data Table Colis en attente
        var ColisEnAttenteServerParams = {
            "custom_view": "[name='custom_view']"
        };
        initDataTable('.table-colis-en-attente-expediteur', admin_url + 'expediteurs/init_colis_en_attente_expediteur/' + clientid, 'colis-en-attente-expediteur', 'undefined', 'undefined', ColisEnAttenteServerParams);
        //Hide First Column
        var hidden_columns = [0];
        $.each(hidden_columns, function (i, val) {
            var column_status = $('.table-colis-en-attente-expediteur').DataTable().column(val);
            column_status.visible(false);
        });
        //Init Data Table Bons livraison
        var BonsLivraisonServerParams = {
            "custom_view": "[name='custom_view']"
        };
        initDataTable('.table-bons-livraison-expediteur', admin_url + 'expediteurs/init_bons_livraison_expediteur/' + clientid, 'bons-livraison-expediteur', 'undefined', 'undefined', BonsLivraisonServerParams);
        //Hide First Column
        var hidden_columns = [0];
        $.each(hidden_columns, function (i, val) {
            var column_status = $('.table-bons-livraison-expediteur').DataTable().column(val);
            column_status.visible(false);
        });
        //Init Data Table Factures
        var FacturesServerParams = {
            "custom_view": "[name='custom_view']"
        };
        initDataTable('.table-factures-expediteur', admin_url + 'expediteurs/init_factures_expediteur/' + clientid, 'factures-expediteur', 'undefined', 'undefined', FacturesServerParams);
        //Hide First Column
        var hidden_columns = [0];
        $.each(hidden_columns, function (i, val) {
            var column_status = $('.table-factures-expediteur').DataTable().column(val);
            column_status.visible(false);
        });
        //Init Data Table Reclamtions
        var ReclamationsServerParams = {
            "custom_view": "[name='custom_view']"
        };
        initDataTable('.table-reclamations-expediteur', admin_url + 'expediteurs/init_reclamations_expediteur/' + clientid, 'reclamations-expediteur', 'undefined', 'undefined', ReclamationsServerParams);
        //Hide First Column
        var hidden_columns = [0];
        $.each(hidden_columns, function (i, val) {
            var column_status = $('.table-reclamations-expediteur').DataTable().column(val);
            column_status.visible(false);
        });
        //Init Data Table Activity log
        initDataTable('.table-activity-log-expediteur', admin_url + 'expediteurs/init_activity_log_expediteur/' + clientid, 'activity-log-expediteur');
        //Hide First Column
        var hidden_columns = [0];
        $.each(hidden_columns, function (i, val) {
            var column_status = $('.table-activity-log-expediteur').DataTable().column(val);
            column_status.visible(false);
        });
        //Check if code parrainage is empty
        if ($('#expediteur-form input[name="code_parrainage"]').val() === '') {
            generateCodeAffiliation($('#expediteur-form input[name="code_parrainage"]'));
        }
    } else {
        //Init
        generatePassword($('#expediteur-form input[name="password"]'));
        generateCodeAffiliation($('#expediteur-form input[name="code_parrainage"]'));
    }
});

// Generate random code d'affiliation
function generateCodeAffiliation(field) {
    var length = 10,
            charset = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
            retVal = "";
    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }

    $(field).parents().find('input.code-parrainage').val(retVal);
}
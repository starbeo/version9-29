$(document).ready(function () {
    //On submit form
    $('body').on('submit', '#etat-colis-livrer-form', function () {
        $('#etat-colis-livrer-form input').attr('disabled', false);
    });
    //Validate form
    _validate_form($('#etat-colis-livrer-form'), {
        user_point_relais: 'required'
    });
    //Add colis
    $('body').on('click', '.colis_added', function () {
        var etatId = $('input[name="etat_id"]').val();
        var colisId = $(this).attr('data-id');
        add_colis_to_etat_colis_livrer(etatId, colisId);
    });
    //Delete colis
    $('body').on('click', '.colis_remove', function () {
        var colisEtatColisLivrerId = $(this).attr('data-item-id');
        remove_colis_to_etat_colis_livrer(colisEtatColisLivrerId);
    });
    //On keyup total received facture
    $('body').on('keyup', 'input[name="total_received"]', function () {
        calculate_etat_colis_livre();
    });
    //Init Data tables
    var etatId = $('input[name="etat_id"]').val();
    if (typeof (etatId) !== 'undefined') {
        var itemsEtatColisLivrerServerParams = {
            "user_point_relais": "[name='user_point_relais']"
        };
        initDataTable('.table-items-etat-colis-livrer', point_relais_url + 'etats_colis_livrer/init_items_etat_colis_livrer', 'Colis', [0, 7, 8], [0, 7, 8], itemsEtatColisLivrerServerParams);
        var HistoriqueItemsEtatColisLivrerServerParams = {
            "etat_id": "[name='etat_id']"
        };
        initDataTable('.table-historique-items-etat-colis-livrer', point_relais_url + 'etats_colis_livrer/init_historique_items_etat_colis_livrer', 'Historique colis etat colis livrer', [0, 7, 8], [0, 7, 8], HistoriqueItemsEtatColisLivrerServerParams, [0, 'ASC'], [3]);
    }
    //Init Color Manque
    var manque = $('input[name="manque"]').val();
    if (typeof (manque) !== 'undefined') {
        calculate_etat_colis_livre();
    }
});

function calculate_etat_colis_livre() {
    var total_received = $('input[name="total_received"]').val();
    var total = $('input[name="total"]').val();
    var commision = $('input[name="commision"]').val();
    var manque = (parseFloat(total_received) - (parseFloat(total) - parseFloat(commision))).toFixed(2);
    var area_manque = $('input[name="manque"]');
    area_manque.val(manque);
    if (manque < 0) {
        area_manque.css('color', 'red');
    } else if (manque === 0 || manque === '') {
        area_manque.css('color', 'MediumSeaGreen');
    } else if (manque > 0) {
        area_manque.css('color', 'DodgerBlue');
    }

    var textarea_justif = $('textarea[name="justif"]');
    var textarea_justif_parent = $('textarea[name="justif"]').parent('div');
    if (manque > 0 || manque < 0) {
        if (textarea_justif_parent.hasClass('display-none')) {
            textarea_justif_parent.removeClass('display-none');
        }
    } else {
        if (!textarea_justif_parent.hasClass('display-none')) {
            textarea_justif.val('');
            textarea_justif_parent.addClass('display-none');
        }
    }
}

// Add colis to etat colis livré
function add_colis_to_etat_colis_livrer(etatId, colisId) {
    if ($.isNumeric(etatId) && $.isNumeric(colisId)) {
        $('.colis_added').addClass('hide');
        $.post(point_relais_url + "etats_colis_livrer/add_colis_to_etat_colis_livrer", {etat_id: etatId, colis_id: colisId}, function (response) {
            var response = $.parseJSON(response);
            if (response.success === true) {
                var commision = $('input[name="commision"]').val();
                commision = (parseFloat(commision) + parseFloat(response.commision)).toFixed(2);
                $('input[name="commision"]').val(commision);
                var total = $('input[name="total"]').val();
                total = (parseFloat(total) + parseFloat(response.total)).toFixed(2);
                $('input[name="total"]').val(total);
                calculate_etat_colis_livre();
                $('.colis_added').removeClass('hide');
                init_table();
            }
            alert_float(response.type, response.message);
        });
    }
}
// Remove colis to etat colis livré
function remove_colis_to_etat_colis_livrer(colisEtatColisLivrerId) {
    if ($.isNumeric(colisEtatColisLivrerId)) {
        $('.colis_remove').addClass('hide');
        $.post(point_relais_url + "etats_colis_livrer/remove_colis_to_etat_colis_livrer", {colis_etat_colis_livrer_id: colisEtatColisLivrerId}, function (response) {
            var response = $.parseJSON(response);
            if (response.success === true) {
                var commision = $('input[name="commision"]').val();
                commision = (parseFloat(commision) - parseFloat(response.commision)).toFixed(2);
                $('input[name="commision"]').val(commision);
                var total = $('input[name="total"]').val();
                total = (parseFloat(total) - parseFloat(response.total)).toFixed(2);
                $('input[name="total"]').val(total);
                calculate_etat_colis_livre();
                $('.colis_remove').removeClass('hide');
                window.scrollTo(0, 1000);
                init_table();
            }
            alert_float(response.type, response.message);
        });
    }
}
// Init table
function init_table() {
    $('.table-items-etat-colis-livrer').DataTable().ajax.reload();
    $('.table-historique-items-etat-colis-livrer').DataTable().ajax.reload();
}
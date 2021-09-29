$(document).ready(function () {
    // Validate form bon livraison
    _validate_form($('#bon-livraison-form'), {
        point_relai_id: 'required',
        type: 'required'
    });
    // Auto focus barcode
    $('#barcode-douchette').focus();
    // On keypress barcode douchette
    $('body').on('keypress', '#barcode-douchette', function (e) {
        if (e.which === 13) {
            var barcode = $('#barcode-douchette').val();
            if (barcode !== '') {
                $.post(point_relais_url + "bons_livraison/get_id_coli", {barcode: barcode}, function (response) {
                    var result = $.parseJSON(response);
                    if (result.success === true && $.isNumeric(result.id)) {
                        var colisId = result.id;
                        add_colis_to_bon_livraison(colisId);
                    } else {
                        alert_float('warning', result.message);
                    }
                    $('#barcode-douchette').val('');
                    $('#barcode-douchette').focus();
                });
            }
        }
    });
    // On click colis added
    $('body').on('click', '.colis_added', function () {
        var colisId = $(this).attr('data-id');
        add_colis_to_bon_livraison(colisId);
    });
    // On click colis remove
    $('body').on('click', '.colis_remove', function () {
        var colisBonLivraisonId = $(this).attr('data-colisbonlivraison-id');
        remove_colis_to_bon_livraison(colisBonLivraisonId);
    });
    // If bonlivraison id exist
    var bonlivraison_id = $('input[name="bonlivraison_id"]').val();
    if (typeof (bonlivraison_id) !== 'undefined') {
        var itemsBonLivraisonServerParams = {
            "bon_livraison_id": "[name='bonlivraison_id']",
            "point_relai_id": "[name='point_relai_id']",
            "type": "[name='type']"
        };
        initDataTable('.table-colis-bon-livraison', point_relais_url + 'bons_livraison/init_colis_bon_livraison', 'Colis', [0, 7, 8], [0, 7, 8], itemsBonLivraisonServerParams);
        var HistoriqueItemsBonLivraisonServerParams = {
            "bon_livraison_id": "[name='bonlivraison_id']"
        };
        initDataTable('.table-historique-colis-bon-livraison', point_relais_url + 'bons_livraison/init_historique_colis_bon_livraison', 'Historique colis bon de livraison', [0, 7, 8], [0, 7, 8], HistoriqueItemsBonLivraisonServerParams);
    }
});
// Add colis to bon livraison
function add_colis_to_bon_livraison(colisId) {
    var bonLivraisonId = $('input[name="bonlivraison_id"]').val();
    if ($.isNumeric(colisId) && $.isNumeric(bonLivraisonId)) {
        $('.colis_added').addClass('hide');
        $.post(point_relais_url + "bons_livraison/add_colis_to_bon_livraison", {bonlivraison_id: bonLivraisonId, colis_id: colisId}, function (response) {
            var response = $.parseJSON(response);
            alert_float(response.type, response.message);
            $('.colis_added').removeClass('hide');
            if (response.success === true) {
                init_table();
            }
        });
    }
}
// Remove colis to bon livraison
function remove_colis_to_bon_livraison(colisBonLivraisonId) {
    if (colisBonLivraisonId !== '') {
        $('.colis_remove').addClass('hide');
        $.post(point_relais_url + "bons_livraison/remove_colis_to_bon_livraison", {colisbonlivraison_id: colisBonLivraisonId}, function (response) {
            var response = $.parseJSON(response);
            alert_float(response.type, response.message);
            $('.colis_remove').removeClass('hide');
            if (response.success === true) {
                init_table();
            }
        });
    }
}
// Init table
function init_table() {
    $('.table-colis-bon-livraison').DataTable().ajax.reload();
    $('.table-historique-colis-bon-livraison').DataTable().ajax.reload();
}
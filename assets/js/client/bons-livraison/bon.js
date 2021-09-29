$(document).ready(function () {
    // Init table
    var bonLivraisonId = $('input[name="bonlivraison_id"]').val();
    if (typeof (bonLivraisonId) !== 'undefined') {
        initDataTable('.table-colis-bon-livraison', client_url + 'bons_livraison/init_colis_bon_livraison', 'Colis bon livraison');
        initDataTable('.table-historique-colis-bon-livraison', client_url + 'bons_livraison/init_historique_colis_bon_livraison/' + bonLivraisonId, 'Historique colis bon livraison');
    }
    // Add colis to bon livraison
    $('body').on('click', '.colis_added', function () {
        var bonlivraisonId = $('input[name="bonlivraison_id"]').val();
        var colisId = $(this).attr('data-id');
        if ($.isNumeric(colisId) && $.isNumeric(bonlivraisonId)) {
            $('.colis_added').addClass('hide');
            $.post(client_url + "bons_livraison/add_colis_to_bon_livraison", {bonlivraison_id: bonlivraisonId, colis_id: colisId}, function (response) {
                var response = $.parseJSON(response);
                alert_float(response.type, response.message);
                if (response.success === true) {
                    $('.colis_added').removeClass('hide');
                    $('.table-colis-bon-livraison').DataTable().ajax.reload();
                    $('.table-historique-colis-bon-livraison').DataTable().ajax.reload();
                }
            });
        }
    });
    // Remove colis to bon livraison
    $('body').on('click', '.colis_remove', function () {
        var colisBonLivraisonId = $(this).attr('data-colisbonlivraison-id');
        if ($.isNumeric(colisBonLivraisonId)) {
            $('.colis_remove').addClass('hide');
            $.post(client_url + "bons_livraison/remove_colis_to_bon_livraison", {colisbonlivraison_id: colisBonLivraisonId}, function (response) {
                var response = $.parseJSON(response);
                alert_float(response.type, response.message);
                if (response.success === true) {
                    $('.colis_remove').removeClass('hide');
                    window.scrollTo(0, 1000);
                    $('.table-colis-bon-livraison').DataTable().ajax.reload();
                    $('.table-historique-colis-bon-livraison').DataTable().ajax.reload();
                }
            });
        }
    });
});
let code_br='';
$(document).ready(function () {
    // Validate form bon livraison
    _validate_form($('#bon-livraison-form'), {
        type_livraison: 'required',
        id_livreur: {
            required: {
                depends: function () {
                    return ($('select[name="type_livraison"]').val() === 'a_domicile') ? true : false;
                }
            }
        },
        point_relai_id: {
            required: {
                depends: function () {
                    return ($('select[name="type_livraison"]').val() === 'point_relai') ? true : false;
                }
            }
        },
        type: 'required'
    });
    //On change type livraison
    $('body').on('change', 'select[name="type_livraison"]', function () {
        var typeLivraison = $('select[name="type_livraison"]').selectpicker('val');
        if (typeLivraison === '' || typeLivraison === 'a_domicile') {
            $('select[name="point_relai_id"]').selectpicker('val', '');
            if ($('#bloc-select-livreur').hasClass('display-none')) {
                $('#bloc-select-livreur').removeClass('display-none');
            }
            if (!$('#bloc-select-point-relai').hasClass('display-none')) {
                $('#bloc-select-point-relai').addClass('display-none');
            }
        } else {
            $('select[name="id_livreur"]').selectpicker('val', '');
            if (!$('#bloc-select-livreur').hasClass('display-none')) {
                $('#bloc-select-livreur').addClass('display-none');
            }
            if ($('#bloc-select-point-relai').hasClass('display-none')) {
                $('#bloc-select-point-relai').removeClass('display-none');
            }
        }
    });
    // Auto focus barcode

    $('#barcode-douchette').focus();
    // On keypress barcode douchette
    $('body').on('keypress', '#barcode-douchette', function (e) {
        if (e.which === 13) {
            var barcode = $('#barcode-douchette').val();
            code_br = barcode;
            if (barcode !== '') {
                $.post(admin_url + "bon_livraison/get_id_coli", {barcode: barcode}, function (response) {
                    var result = $.parseJSON(response);
                    if (result.success === true && $.isNumeric(result.id)) {
                        var colis_id = result.id;
                     //
                        check_city(colis_id);
                    } else {
                        console.log(result)
                        Swal.fire(
                            {
                                icon: 'warning',
                                title: 'Oops...',
                                text:"Veuillez faire attention au colis "+barcode +" n'est pas valider",

                            }
                        )
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
            "type_livraison": "[name='type_livraison']",
            "id_livreur": "[name='id_livreur']",
            "point_relai_id": "[name='point_relai_id']",
            "type": "[name='type']"
        };
        initDataTable('.table-colis-bon-livraison', admin_url + 'bon_livraison/init_colis_bon_livraison', 'Colis', [0, 7, 8], [0, 7, 8], itemsBonLivraisonServerParams);

        var HistoriqueItemsBonLivraisonServerParams = {
            "bon_livraison_id": "[name='bonlivraison_id']"
        };
        initDataTable('.table-historique-colis-bon-livraison', admin_url + 'bon_livraison/init_historique_colis_bon_livraison', 'Historique colis bon de livraison', [0, 7, 8], [0, 7, 8], HistoriqueItemsBonLivraisonServerParams);
    }
});
// Add colis to bon livraison
function add_colis_to_bon_livraison(colisId) {
    var bonLivraisonId = $('input[name="bonlivraison_id"]').val();
    if ($.isNumeric(colisId) && $.isNumeric(bonLivraisonId)) {
        $('.colis_added').addClass('hide');
        $.post(admin_url + "bon_livraison/add_colis_to_bon_livraison", {bonlivraison_id: bonLivraisonId, colis_id: colisId}, function (response) {
            var response = $.parseJSON(response);
            console.log(response);
            if (response.type === 'warning')
            {
                Swal.fire(
                    {
                        icon: 'error',
                        title: 'Attention...',
                        text: response.message
                    }
                )
            }
            else if (response.success === true) {
                init_table();
            }
            $('.colis_added').removeClass('hide');

        });
    }
}
// Remove colis to bon livraison
function remove_colis_to_bon_livraison(colisBonLivraisonId) {
    if (colisBonLivraisonId !== '') {
        $('.colis_remove').addClass('hide');
        $.post(admin_url + "bon_livraison/remove_colis_to_bon_livraison", {colisbonlivraison_id: colisBonLivraisonId}, function (response) {
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



function check_city(coli_id){
    $.post(admin_url + "bon_livraison/checkville", {coli_id: coli_id}, function (response) {
        var response = $.parseJSON(response);
       // comissionadd(response.success,e)
     //   console.log(response.success)
         if (response.success)
         {
             add_colis_to_bon_livraison(coli_id);
         } else
         {

           //  alert_float("warning", "cette ville n'inclut pas");
             Swal.fire(
                 {
                     icon: 'error',
                     title: 'Attention...',
                     text:"Veuillez faire attention au colis  "+" "+code_br+" "+"de ville "+" "+response.citie+" dans la mauvaise destination"



                 }
             )

         }
    });

}




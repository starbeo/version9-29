$(document).ready(function () {
    $('input[name="total"]').change();
    //On submit form
    /*$('body').on('submit', '#facture-interne-form', function(e) {
     $('#facture-interne-form input').attr('disabled', false);
     });*/
    _validate_form($('#facture-interne-form'));
    //Added facture
    $('body').on('click', '.facture_added', function () {
        var facture_interne_id = $('input[name="facture_id"]').val();
        var facture_id = $(this).attr('data-id');
        var total = $('input[name="total"]').val();
        var total_frais = $('input[name="total_frais"]').val();
        var total_refuse = $('input[name="total_refuse"]').val();
        var total_parrainage = $('input[name="total_parrainage"]').val();
        var total_remise = $('input[name="total_remise"]').val();
        var total_net = $('input[name="total_net"]').val();
        if (facture_id !== '' && facture_interne_id !== '') {
            $('.facture_added').addClass('hide');
            $.when(
                    $.post(admin_url + "factures_internes/add_facture_to_facture_interne", {facture_interne_id: facture_interne_id, facture_id: facture_id}, function (response) {
                        var response = $.parseJSON(response);
                        if (response.success === true) {
                            total = (parseFloat(total) + parseFloat(response.total_crbt)).toFixed(2);
                            total_frais = (parseFloat(total_frais) + parseFloat(response.total_frais)).toFixed(2);
                            total_refuse = (parseFloat(total_refuse) + parseFloat(response.total_refuse)).toFixed(2);
                            total_parrainage = (parseFloat(total_parrainage) + parseFloat(response.total_parrainage)).toFixed(2);
                            total_remise = (parseFloat(total_remise) + parseFloat(response.total_remise)).toFixed(2);
                            total_net = (parseFloat(total_net) + parseFloat(response.total_net)).toFixed(2);
                            $('input[name="total"]').val(total);
                            $('input[name="total_frais"]').val(total_frais);
                            $('input[name="total_refuse"]').val(total_refuse);
                            $('input[name="total_parrainage"]').val(total_parrainage);
                            $('input[name="total_remise"]').val(total_remise);
                            $('input[name="total_net"]').val(total_net);
                            calculate_rest_facture_interne();
                            $('.facture_added').removeClass('hide');
                        }
                        alert_float(response.type, response.message);
                    })
                    ).then(
                    hide_facture($(this))
                    );
        }
    });
    //Delete facture
    $('body').on('click', '.facture_remove', function () {
        var id = $(this).attr('data-item-id');
        if (id !== '') {
            $('.facture_remove').addClass('hide');
            var total = $('input[name="total"]').val();
            var total_frais = $('input[name="total_frais"]').val();
            var total_refuse = $('input[name="total_refuse"]').val();
            var total_parrainage = $('input[name="total_parrainage"]').val();
            var total_remise = $('input[name="total_remise"]').val();
            var total_net = $('input[name="total_net"]').val();
            $.when(
                    $.post(admin_url + "factures_internes/remove_facture_to_facture_interne", {id: id}, function (response) {
                        var response = $.parseJSON(response);
                        if (response.success === true) {
                            total = (parseFloat(total) - parseFloat(response.total_crbt)).toFixed(2);
                            total_frais = (parseFloat(total_frais) - parseFloat(response.total_frais)).toFixed(2);
                            total_refuse = (parseFloat(total_refuse) - parseFloat(response.total_refuse)).toFixed(2);
                            total_parrainage = (parseFloat(total_parrainage) - parseFloat(response.total_parrainage)).toFixed(2);
                            total_remise = (parseFloat(total_remise) - parseFloat(response.total_remise)).toFixed(2);
                            total_net = (parseFloat(total_net) - parseFloat(response.total_net)).toFixed(2);
                            $('input[name="total"]').val(total);
                            $('input[name="total_frais"]').val(total_frais);
                            $('input[name="total_refuse"]').val(total_refuse);
                            $('input[name="total_parrainage"]').val(total_parrainage);
                            $('input[name="total_remise"]').val(total_remise);
                            $('input[name="total_net"]').val(total_net);
                            calculate_rest_facture_interne();
                            $('.facture_remove').removeClass('hide');
                            window.scrollTo(0, 1000);
                        }
                        alert_float(response.type, response.message);
                    })
                    ).then(
                    hide_facture($(this))
                    );
        }
    });

    $('body').on('change', '.product_checked', function () {
        var nbrColisSelected = $('input[id="nbr_colis_selected"]').val();
        var alertColisSelected = $('input[id="alert_colis_selected"]').val();
        var product_id = $(this).val();
        if ($(this).prop('checked') === true) {
       
                if ($('input[id="checked_product_' + product_id + '"]').length === 0) {
                    $('#checked-products').append('<input id="checked_product_' + product_id + '" type="hidden" name="checked_products[]" value="' + product_id + '">');
                    nbrColisSelected++;
                }
     
        } else {
            if ($('input[id="checked_product_' + product_id + '"]').length > 0) {
                $('input[id="checked_product_' + product_id + '"]').remove();
                nbrColisSelected--;
                if ($('#product_checked_' + product_id).prop('checked') === true) {
                    $('#product_checked_' + product_id).prop('checked', false);
                }
                $('input[id="alert_colis_selected"]').val(0);
            }
        }
        $('input[id="nbr_colis_selected"]').val(nbrColisSelected);
    });

    //Init Data tables
    var facture_interne_id = $('input[name="facture_id"]').val();
    if (typeof (facture_interne_id) !== 'undefined') {
        var HistoriqueServerParams = {};
        initDataTable('.table-items-facture-interne', admin_url + 'factures_internes/init_items_facture_interne', 'items-facture-interne');
        initDataTable('.table-historique-items-facture-interne', admin_url + 'factures_internes/init_historique_items_facture_interne/' + facture_interne_id, 'historique-items-facture-interne', [6], [6], HistoriqueServerParams, [0, 'ASC'], [1]);
    }

    // If check all colis
    $('body').on('change', '.check_all_product_checked', function () {
        if ($(this).prop('checked') === true) {
            $('.product_checked').prop('checked', true);
        }
        $('.check_all_product_checked').prop('checked', false);
        $('.product_checked').change();
    });
    // If uncheck all colis
    $('body').on('change', '.uncheck_all_product_checked', function () {
        if ($(this).prop('checked') === true) {
            $('.product_checked').prop('checked', false);
        }
        $('.uncheck_all_product_checked').prop('checked', false);
        $('.product_checked').change();
    });


});
$('#submit_f_i').click(function(){
    getInputsData()
});
function calculate_rest_facture_interne() {
    var total_received = $('input[name="total_received"]').val();
    var total = $('input[name="total"]').val();
    var total_frais = $('input[name="total_frais"]').val();
    var total_refuse = $('input[name="total_refuse"]').val();
    var total_parrainage = $('input[name="total_parrainage"]').val();
    var total_remise = $('input[name="total_remise"]').val();
    var total_net = $('input[name="total_net"]').val();
    var rest = (parseFloat(total_received) - parseFloat(total_net)).toFixed(2);
    $('input[name="total_net"]').val(total_net);
    var area_rest = $('input[name="rest"]');
    area_rest.val(rest);
    if (rest < 0) {
        area_rest.css('color', 'red');
    } else if (rest === 0 || rest === '') {
        area_rest.css('color', 'MediumSeaGreen');
    } else if (rest > 0) {
        area_rest.css('color', 'DodgerBlue');
    }

    //Show & Hide Textarea Motif
    var textarea_motif = $('textarea[name="motif"]');
    var textarea_motif_parent = $('textarea[name="motif"]').parent('div');
    if (rest > 0 || rest < 0) {
        if (textarea_motif_parent.hasClass('display-none')) {
            textarea_motif_parent.removeClass('display-none');
        }
    } else {
        if (!textarea_motif_parent.hasClass('display-none')) {
            textarea_motif.val('');
            textarea_motif_parent.addClass('display-none');
        }
    }
}

function hide_facture(row) {
    $(row).parents('tr').addClass('animated fadeOut', function () {
        setTimeout(function () {
            $(row).parents('tr').remove();
        }, 300);
    });
    $('.table-items-facture-interne').DataTable().ajax.reload();
    $('.table-historique-items-facture-interne').DataTable().ajax.reload();
}

function getInputsData ()
{
   // let data = $( "input[name='checked_products[]']" );
    var values =  $( "input[name='checked_products[]']" ).map(function(){
        return this.value;
    }).get();
    console.log(values);
    console.log(values.length);
values.forEach((element) => {
    add_facture(element)
})
    window.location.reload(true);

}

function add_facture(data_id) {
    var facture_interne_id = $('input[name="facture_id"]').val();
    var facture_id = data_id;
    var total = $('input[name="total"]').val();
    var total_frais = $('input[name="total_frais"]').val();
    var total_refuse = $('input[name="total_refuse"]').val();
    var total_parrainage = $('input[name="total_parrainage"]').val();
    var total_remise = $('input[name="total_remise"]').val();
    var total_net = $('input[name="total_net"]').val();
    if (facture_id !== '' && facture_interne_id !== '') {
        $('.facture_added').addClass('hide');
        $.when(
            $.post(admin_url + "factures_internes/add_facture_to_facture_interne", {
                facture_interne_id: facture_interne_id,
                facture_id: facture_id
            }, function (response) {
                var response = $.parseJSON(response);
                if (response.success === true) {
                    total = (parseFloat(total) + parseFloat(response.total_crbt)).toFixed(2);
                    total_frais = (parseFloat(total_frais) + parseFloat(response.total_frais)).toFixed(2);
                    total_refuse = (parseFloat(total_refuse) + parseFloat(response.total_refuse)).toFixed(2);
                    total_parrainage = (parseFloat(total_parrainage) + parseFloat(response.total_parrainage)).toFixed(2);
                    total_remise = (parseFloat(total_remise) + parseFloat(response.total_remise)).toFixed(2);
                    total_net = (parseFloat(total_net) + parseFloat(response.total_net)).toFixed(2);
                    $('input[name="total"]').val(total);
                    $('input[name="total_frais"]').val(total_frais);
                    $('input[name="total_refuse"]').val(total_refuse);
                    $('input[name="total_parrainage"]').val(total_parrainage);
                    $('input[name="total_remise"]').val(total_remise);
                    $('input[name="total_net"]').val(total_net);
                    calculate_rest_facture_interne();
                    $('.facture_added').removeClass('hide');
                }
                alert_float(response.type, response.message);
            })
        ).then(
         //   hide_facture($(this))
        console.log('baqqi hadi')
        );
    }

}




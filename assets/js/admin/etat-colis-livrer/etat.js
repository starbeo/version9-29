$(document).ready(function () {
    //On submit form
    $('body').on('submit', '#etat-colis-livrer-form', function (e) {
        $('#etat-colis-livrer-form input').attr('disabled', false);
    });
    //Validate form
    _validate_form($('#etat-colis-livrer-form'), {
        type_livraison: 'required',
        id_livreur: {
            required: {
                depends: function () {
                    return ($('select[name="type_livraison"]').val() === 'a_domicile') ? true : false;
                }
            }
        },
        user_point_relais: {
            required: {
                depends: function () {
                    return ($('select[name="type_livraison"]').val() === 'point_relai') ? true : false;
                }
            }
        }
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
    //Add colis
    $('body').on('click', '.colis_added', function () {
        var etatId = $('input[name="etat_id"]').val();
        var colis_id = $(this).attr('data-id');
        if (colis_id !== '' && etatId !== '') {
            $('.colis_added').addClass('hide');
            $.when(
                    $.post(admin_url + "etat_colis_livrer/add_colis_to_etat_colis_livrer", {etat_id: etatId, colis_id: colis_id}, function (response) {
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
                        }
                        alert_float(response.type, response.message);
                    })
                    ).then(
                    hide_colis($(this))
                    );
        }
    });
    //Delete colis
    $('body').on('click', '.colis_remove', function () {
        var colis_etat_colis_livrer_id = $(this).attr('data-item-id');
        if (colis_etat_colis_livrer_id !== '') {
            $('.colis_remove').addClass('hide');
            $.when(
                    $.post(admin_url + "etat_colis_livrer/remove_colis_to_etat_colis_livrer", {colis_etat_colis_livrer_id: colis_etat_colis_livrer_id}, function (response) {
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
                        }
                        alert_float(response.type, response.message);
                    })
                    ).then(
                    hide_colis($(this))
                    );
        }
    });
    //On keyup total received facture
    $('body').on('keyup', 'input[name="total_received"]', function () {
        calculate_etat_colis_livre();
    });
    //Init Data tables
    var etatId = $('input[name="etat_id"]').val();
    if (typeof (etatId) !== 'undefined') {
        var itemsEtatColisLivrerServerParams = {
            "type_livraison": "[name='type_livraison']",
            "id_livreur": "[name='id_livreur']",
            "user_point_relais": "[name='user_point_relais']"
        };
        initDataTable('.table-items-etat-colis-livrer', admin_url + 'etat_colis_livrer/init_items_etat_colis_livrer', 'Colis', [0, 6, 7], [0, 6, 7], itemsEtatColisLivrerServerParams);
        var HistoriqueItemsEtatColisLivrerServerParams = {
            "etat_id": "[name='etat_id']"
        };
        initDataTable('.table-historique-items-etat-colis-livrer', admin_url + 'etat_colis_livrer/init_historique_items_etat_colis_livrer', 'Historique colis etat colis livrer', [0, 6, 7], [0, 6, 7], HistoriqueItemsEtatColisLivrerServerParams, [0, 'ASC'], [3]);
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

function hide_colis(row) {
    $(row).parents('tr').addClass('animated fadeOut', function () {
        setTimeout(function () {
            $(row).parents('tr').remove();
        }, 300);
    });
    $('.table-items-etat-colis-livrer').DataTable().ajax.reload();
    $('.table-historique-items-etat-colis-livrer').DataTable().ajax.reload();
}

function validate_etat_etat_colis_livrer() {
    var etatId = $('input[name="etat_id"]').val();
    if ($.isNumeric(etatId)) {
        $.post(admin_url + 'etat_colis_livrer/validate_etat/' + etatId).success(function (response) {
            response = $.parseJSON(response);
            alert_float(response.type, response.message);
            if (response.success === true) {
                $('.table-historique-items-etat-colis-livrer').DataTable().ajax.reload();
            }
        });
    }

    return false;
}
$(document).ready(function () {
    var headers_colis = $('.table-colis').find('th');
    var not_sortable_colis = (headers_colis.length - 1);
    var ColisServerParams = {
        "custom_view": "[name='custom_view']",
        "f-point-relai": "[name='f-point-relai']",
        "f-clients": "[name='f-clients']",
        "f-statut": "[name='f-statut']",
        "f-etat": "[name='f-etat']",
        "f-ville": "[name='f-ville']",
        "f-date-ramassage-start": "[name='f-date-ramassage-start']",
        "f-date-ramassage-end": "[name='f-date-ramassage-end']",
        "f-date-livraison-start": "[name='f-date-livraison-start']",
        "f-date-livraison-end": "[name='f-date-livraison-end']"
    };
    initDataTable('.table-colis', window.location.href, 'colis', [not_sortable_colis], [not_sortable_colis], ColisServerParams);
    var hidden_columns = [0];
    $.each(hidden_columns, function (i, val) {
        var column_status = $('.table-colis').DataTable().column(val);
        column_status.visible(false);
    });
    //Validate form colis
    _validate_form($('#form-colis'), {
        num_commande: {
            required: true,
            check_numero_commande: {
                url: point_relais_url + "colis/check_num_commande_exists",
                type: 'post',
                data: {
                    num_commande: function () {
                        return $('input[name="num_commande"]').val();
                    },
                    colis_id: function () {
                        return $('input[name="id"]').val();
                    }
                }
            }
        },
        id_expediteur: 'required',
        point_relai_id: 'required',
        telephone: {
            required: true,
            remote: {
                url: point_relais_url + "colis/check_telephone",
                type: 'post',
                data: {
                    telephone: function () {
                        return $('input[name="telephone"]').val();
                    },
                    colis_id: function () {
                        return $('input[name="id"]').val();
                    }
                }
            }
        },
        crbt: 'required',
        frais: 'required'
    }, manage_colis);
    //Show & hide form
    $('#colis').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#colis .add-title').removeClass('hide');
        $('#colis .edit-title').addClass('hide');
        //Hide input barcode
        if (!$('#colis #bloc-input-barcode').hasClass('display-none')) {
            $('#colis #bloc-input-barcode').addClass('display-none');
        }
        //Init input, textarea and select
        $('#colis input').val('');
        $('#colis textarea').val('');
        $('#colis select').selectpicker('val', '');
        //Disabled input
        $('#colis input[name="num_commande"]').attr('disabled', false);
        $('#colis input[name="crbt"]').attr('disabled', false);
        //Disabled checked input
        $('#colis input[name="ouverture"]').prop('checked', false);
        $('#colis input[name="option_frais"]').prop('checked', false);
        $('#colis input[name="option_frais_assurance"]').prop('checked', false);
        //Disabled button
        $('#colis button[id="submit"]').attr('disabled', false);

        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $.post(point_relais_url + "colis/get_info_colis/" + id, function (response) {
                var data = $.parseJSON(response);
                $('#colis input[name="id"]').val(id);
                $('#colis .add-title').addClass('hide');
                $('#colis .edit-title').removeClass('hide');
                if ($('#colis #bloc-input-barcode').hasClass('display-none')) {
                    $('#colis #bloc-input-barcode').removeClass('display-none');
                }
                $('#colis input[name="code_barre"]').val(data['code_barre']);
                $('#colis input[name="code_barre"]').attr('disabled', true);
                $('#colis input[name="num_commande"]').val(data['num_commande']);
                $('#colis input[name="num_commande"]').attr('disabled', true);
                if (parseInt(data['ouverture']) === 1) {
                    $('#colis input[name="ouverture"]').prop('checked', true);
                }
                if (parseInt(data['option_frais']) === 1) {
                    $('#colis input[name="option_frais"]').prop('checked', true);
                }
                if (parseInt(data['option_frais_assurance']) === 1) {
                    $('#colis input[name="option_frais_assurance"]').prop('checked', true);
                }
                $('select[name="id_expediteur"]').selectpicker('val', data['id_expediteur']);
                $('select[name="point_relai_id"]').selectpicker('val', data['point_relai_id']);
                $('select[name="ville"]').selectpicker('val', data['ville']);
                $('#colis input[name="nom_complet"]').val(data['nom_complet']);
                $('#colis input[name="telephone"]').val(data['telephone']);
                $('#colis input[name="crbt"]').val(data['crbt']);
                $('#colis input[name="crbt"]').prop('disabled', true);
                if (parseInt(data['crbt_modifiable']) === 1) {
                    $('#colis input[name="crbt"]').prop('disabled', false);
                }
                $('#colis input[name="frais"]').val(data['frais']);
                $('#colis textarea[name="adresse"]').val(data['adresse']);
                $('#colis textarea[name="commentaire"]').val(data['commentaire']);
            });
        } else {
            $('#colis input[name="telephone"]').val('06');
        }
    });
    //ON CHANGE SELECT POINT RELAI
    $('body').on('change', 'select[name="point_relai_id"]', function () {
        var pointRelaiId = $('select[name="point_relai_id"]').selectpicker('val');
        if ($.isNumeric(pointRelaiId)) {
            $.post(point_relais_url + "misc/get_city/" + pointRelaiId, function (response) {
                var result = $.parseJSON(response);
                var cityPointRelai = result.city;
                if ($.isNumeric(cityPointRelai)) {
                    $('#colis select[name="ville"]').selectpicker('val', cityPointRelai);
                    if (cityPointRelai !== 0) {
                        $('#colis select[name="ville"]').change();
                    }
                }
            });
        }
    });
    //Show statistique
    $('body').on('click', '.btn-statistique-colis', function () {
        if ($('#statistique-colis').hasClass('display-none')) {
            $('#statistique-colis').removeClass('display-none');
            $('#filtre-table').addClass('display-none');
        } else {
            $('#statistique-colis').addClass('display-none');
        }
    });
    //ON CHANGE SELECT CLIENT
    $('body').on('change', 'select[name="id_expediteur"]', function () {
        var expediteurId = $('select[name="id_expediteur"]').selectpicker('val');
        if ($.isNumeric(expediteurId)) {
            $.post(point_relais_url + "misc/get_ouverture_colis/" + expediteurId, function (response) {
                var result = $.parseJSON(response);
                var ouvertureColis = result.ouverture_colis;
                $('#colis input[name="ouverture"]').prop('checked', false);
                if ($.isNumeric(ouvertureColis) && parseInt(ouvertureColis) === 1) {
                    $('#colis input[name="ouverture"]').prop('checked', true);
                }
                var optionFrais = result.option_frais;
                $('#colis input[name="option_frais"]').prop('checked', false);
                if ($.isNumeric(optionFrais) && parseInt(optionFrais) === 1) {
                    $('#colis input[name="option_frais"]').prop('checked', true);
                }
                var optionFraisAssurance = result.option_frais_assurance;
                $('#colis input[name="option_frais_assurance"]').prop('checked', false);
                if ($.isNumeric(optionFraisAssurance) && parseInt(optionFraisAssurance) === 1) {
                    $('#colis input[name="option_frais_assurance"]').prop('checked', true);
                }
                $('input[name="crbt"]').change();
            });
        }
    });
    //ON CHANGE INPUT CRBT
    $('body').on('change', 'input[name="crbt"]', function () {
        var shippingCostByVille = $('input[name="shipping_cost_by_ville"]').val();
        if (parseInt(shippingCostByVille) === 0) {
            var expediteur_id = $('select[name="id_expediteur"]').selectpicker('val');
            var ville_id = $('select[name="ville"]').val();
            $.post(point_relais_url + "misc/get_expediteur_by_id/" + expediteur_id + '/' + ville_id, function (response) {
                var result = $.parseJSON(response);
                var dataExpediteur = result.expediteur;
                if (dataExpediteur !== null) {
                    if ($.isNumeric(dataExpediteur['ville_id'])) {
                        var optionFrais = false;
                        if ($('#colis input[name="option_frais"]').prop('checked')) {
                            optionFrais = true;
                        }
                        var frais = 0;
                        var fraisInitiale = 0;
                        var fraisSpecial = 0;
                        if(result.frais_special > 0) {
                            fraisInitiale = fraisSpecial;
                        } else {
                            if (dataExpediteur['ville_id'] === ville_id) {
                                fraisInitiale = dataExpediteur['frais_livraison_interieur'];
                            } else {
                                fraisInitiale = dataExpediteur['frais_livraison_exterieur'];
                            }   
                        }
                        if (optionFrais === true) {
                            frais = (parseFloat(fraisInitiale)
                                    + parseFloat(dataExpediteur['frais_supplementaire'])
                                    + parseFloat(dataExpediteur['frais_stockage'])
                                    + parseFloat(dataExpediteur['frais_emballage'])
                                    + parseFloat(dataExpediteur['frais_etiquette'])
                                    ).toFixed(2);
                        } else {
                            frais = (parseFloat(fraisInitiale)
                                    + parseFloat(dataExpediteur['frais_supplementaire'])
                                    ).toFixed(2);
                        }
                        var optionFraisAssurance = false;
                        if ($('#colis input[name="option_frais_assurance"]').prop('checked')) {
                            optionFraisAssurance = true;
                        }
                        var crbt = $('input[name="crbt"]').val();
                        if (optionFraisAssurance === true && $.isNumeric(crbt)) {
                            var percentFraisAssurance = result.pourcentage_frais_assurance;
                            frais = (parseFloat(frais) + ((crbt * parseFloat(percentFraisAssurance)) / 100)).toFixed(2);
                        }
                        $('input[name="frais"]').val(frais);
                    }
                }
            });
        }
    });
    //ON CHANGE SELECT VILLE
    $('body').on('change', 'select[name="ville"]', function () {
        var expediteur_id = $('select[name="id_expediteur"]').selectpicker('val');
        if (expediteur_id === '') {
            $('select[name="ville"]').selectpicker('val', '');
            alert_float('warning', 'Sélectionner un client !!');
        } else {
            var ville_id = $('select[name="ville"]').val();
            $('input[name="frais"]').val('');
            if (ville_id !== '') {
                var shippingCostByVille = $('input[name="shipping_cost_by_ville"]').val();
                if (parseInt(shippingCostByVille) === 1) {
                    $.post(point_relais_url + "misc/get_shipping_cost/" + ville_id, function (response) {
                        var result = $.parseJSON(response);
                        $('input[name="frais"]').val((parseFloat(result.shipping_cost)).toFixed(2));
                    });
                } else {
                    $.post(point_relais_url + "misc/get_expediteur_by_id/" + expediteur_id + '/' + ville_id, function (response) {
                        var result = $.parseJSON(response);
                        var dataExpediteur = result.expediteur;
                        if (dataExpediteur !== null) {
                            if ($.isNumeric(dataExpediteur['ville_id'])) {
                                var optionFrais = false;
                                if ($('#colis input[name="option_frais"]').prop('checked')) {
                                    optionFrais = true;
                                }
                                var frais = 0;
                                var fraisInitiale = 0;
                                var fraisSpecial = 0;
                                if(result.frais_special > 0) {
                                    fraisInitiale = fraisSpecial;
                                } else {
                                    if (dataExpediteur['ville_id'] === ville_id) {
                                        fraisInitiale = dataExpediteur['frais_livraison_interieur'];
                                    } else {
                                        fraisInitiale = dataExpediteur['frais_livraison_exterieur'];
                                    }   
                                }
                                if (optionFrais === true) {
                                    frais = (parseFloat(fraisInitiale)
                                            + parseFloat(dataExpediteur['frais_supplementaire'])
                                            + parseFloat(dataExpediteur['frais_stockage'])
                                            + parseFloat(dataExpediteur['frais_emballage'])
                                            + parseFloat(dataExpediteur['frais_etiquette'])
                                            ).toFixed(2);
                                } else {
                                    frais = (parseFloat(fraisInitiale)
                                            + parseFloat(dataExpediteur['frais_supplementaire'])
                                            ).toFixed(2);
                                }
                                var optionFraisAssurance = false;
                                if ($('#colis input[name="option_frais_assurance"]').prop('checked')) {
                                    optionFraisAssurance = true;
                                }
                                var crbt = $('input[name="crbt"]').val();
                                if (optionFraisAssurance === true && $.isNumeric(crbt)) {
                                    var percentFraisAssurance = result.pourcentage_frais_assurance;
                                    frais = (parseFloat(frais) + ((crbt * parseFloat(percentFraisAssurance)) / 100)).toFixed(2);
                                }
                                $('input[name="frais"]').val(frais);
                            }
                        }
                    });
                }
            } else {
                $('select[name="ville"]').selectpicker('val', '');
                alert_float('warning', 'Sélectionner une ville !!');
            }
        }
    });

    //ON CHANGE INPUT option_frais OR option_frais_assurance
    $('body').on('change', 'input[name="option_frais"], input[name="option_frais_assurance"]', function () {
        var villeId = $('select[name="ville"]').val();
        if ($.isNumeric(villeId)) {
            $('select[name="ville"]').change();
        }
    });
});

function manage_colis(form) {
    $('#colis input[name="code_barre"]').attr('disabled', false);
    $('#colis input[name="num_commande"]').attr('disabled', false);
    $('#colis input[name="crbt"]').attr('disabled', false);
    $('#colis button[id="submit"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;

    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        alert_float(response.type, response.message);
        $('.table-colis').DataTable().ajax.reload();
        $('#colis').modal('hide');
    });

    return false;
}
$(document).ready(function () {
    // Init data table colis en attente
    var headers_colis_en_attente = $('.table-colis-en-attente').find('th');
    var not_sortable_colis_en_attente = (headers_colis_en_attente.length - 1);
    var ColisEnAttenteServerParams = {
        "f-type-livraison": "[name='f-type-livraison']",
        "f-point-relai": "[name='f-point-relai']",
        "f-ville": "[name='f-ville']",
        "f-date-created-start": "[name='f-date-created-start']",
        "f-date-created-end": "[name='f-date-created-end']"
    };
    initDataTable('.table-colis-en-attente', window.location.href, 'Colis en attente', [not_sortable_colis_en_attente], [not_sortable_colis_en_attente], ColisEnAttenteServerParams);
    // Validate form colis en attente
    _validate_form($('#colis-en-attente-form'), {
        num_commande: {
            required: true,
            check_numero_commande: {
                url: client_url + "colis_en_attente/check_num_commande_exists",
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
        crbt: 'required',
        type_livraison: 'required',
        point_relai_id: {
            required: {
                depends: function () {
                    return ($('select[name="type_livraison"]').val() === 'point_relai') ? true : false;
                }
            }
        },
        ville: {
            required: {
                depends: function () {
                    return ($('select[name="type_livraison"]').val() === 'a_domicile') ? true : false;
                }
            }
        },
        nom_complet: 'required',
        telephone: {
            required: true,
            remote: {
                url: site_url + "client/colis_en_attente/check_telephone",
                type: 'post',
                data: {
                    telephone: function () {
                        return $('input[name="telephone"]').val();
                    }
                }
            }
        },
        adresse: 'required'
    }, manage_colis_en_attente);
    // validator regex
    $.validator.addMethod(
            "regex",
            function (value, element, regexp) {
                var check = false;
                var re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            },
            "<i class='fa fa-exclamation-triangle' style='font-size:14px;color:red'></i> Les caractères spéciaux sont interdits, (Merci de revoir l'adresse que vous avez entrer, s'il ya un espace à la fin ou bien un saut de la ligne il faut le supprimer)"
            );
    // Show & Hide colis en attente
    $('#colis-en-attente').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        var prefix = $('#prefix').val();
        //Get value option point relai
        var showPointRelai = $('#colis-en-attente input[id="show_point_relai"]').val();
        $('#colis-en-attente .add-title').removeClass('hide');
        $('#colis-en-attente .edit-title').addClass('hide');
        //Hide input barcode
        if (!$('#colis-en-attente #bloc-input-barcode').hasClass('display-none')) {
            $('#colis-en-attente #bloc-input-barcode').addClass('display-none');
        }
        //Init input, textarea and select
        $('#colis-en-attente input').val('');
        $('#colis-en-attente textarea').val('');
        $('#colis-en-attente select').selectpicker('val', '');
        //Init select type livraison default value "a_domicile"
        $('#colis-en-attente select[name="type_livraison"]').selectpicker('val', 'a_domicile');
        //Disabled input
        $('#colis-en-attente input[name="num_commande"]').attr('disabled', false);
        $('#colis-en-attente input[name="num_commande"]').val(prefix);
        //Disabled button
        $('#colis-en-attente button[id="submit"]').attr('disabled', false);
        //Check if point relai actived
        if (parseInt(showPointRelai) === 1) {
            $('#colis-en-attente input[id="show_point_relai"]').val(showPointRelai);
            //Show select type livraison
            if ($('#colis-en-attente #bloc-select-type-livraison-colis').hasClass('display-none')) {
                $('#colis-en-attente #bloc-select-type-livraison-colis').removeClass('display-none');
            }
        } else {
            $('#colis-en-attente input[id="show_point_relai"]').val(showPointRelai);
            //Hide select type livraison
            if (!$('#colis-en-attente #bloc-select-type-livraison-colis').hasClass('display-none')) {
                $('#colis-en-attente #bloc-select-type-livraison-colis').addClass('display-none');
            }
        }
        //Show select ville
        if ($('#colis-en-attente #bloc-select-ville-colis').hasClass('display-none')) {
            $('#colis-en-attente #bloc-select-ville-colis').removeClass('display-none');
        }
        //Show select quartier
        if ($('#colis-en-attente #bloc-select-quartier-colis').hasClass('display-none')) {
            $('#colis-en-attente #bloc-select-quartier-colis').removeClass('display-none');
        }
        //Hide select point relai
        if (!$('#colis-en-attente #bloc-select-point-relai-colis').hasClass('display-none')) {
            $('#colis-en-attente #bloc-select-point-relai-colis').addClass('display-none');
        }

        $.post(client_url + "profile/get_settings_client", function (response1) {
            var response1 = $.parseJSON(response1);
            if (response1.success === true) {
                var ouvertureColis = response1.ouverture_colis;
                $('#colis-en-attente input[name="ouverture"]').prop('checked', false);
                if ($.isNumeric(ouvertureColis) && parseInt(ouvertureColis) === 1) {
                    $('#colis-en-attente input[name="ouverture"]').prop('checked', true);
                }
                var optionFrais = response1.option_frais;
                $('#colis-en-attente input[name="option_frais"]').prop('checked', false);
                if ($.isNumeric(optionFrais) && parseInt(optionFrais) === 1) {
                    $('#colis-en-attente input[name="option_frais"]').prop('checked', true);
                }
                var optionFraisAssurance = response1.option_frais_assurance;
                $('#colis-en-attente input[name="option_frais_assurance"]').prop('checked', false);
                if ($.isNumeric(optionFraisAssurance) && parseInt(optionFraisAssurance) === 1) {
                    $('#colis-en-attente input[name="option_frais_assurance"]').prop('checked', true);
                }
            }
        });
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $.post(client_url + "colis_en_attente/get_colis_en_attente/" + id, function (response) {
                var data = $.parseJSON(response);
                $('#colis-en-attente input[name="id"]').val(id);
                $('#colis-en-attente .add-title').addClass('hide');
                $('#colis-en-attente .edit-title').removeClass('hide');
                if ($('#colis-en-attente #bloc-input-barcode').hasClass('display-none')) {
                    $('#colis-en-attente #bloc-input-barcode').removeClass('display-none');
                }
                $('#colis-en-attente input[name="code_barre"]').attr('disabled', true);
                $('#colis-en-attente input[name="code_barre"]').val(data['code_barre']);
                $('#colis-en-attente input[name="num_commande"]').attr('disabled', true);
                $('#colis-en-attente input[name="num_commande"]').val(data['num_commande']);
                $('#colis-en-attente input[name="crbt"]').val(data['crbt']);
                $('#colis-en-attente select[name="type_livraison"]').selectpicker('val', data['type_livraison']);
                if (data['type_livraison'] === 'a_domicile') {
                    //Show select ville
                    $('#colis-en-attente select[name="ville"]').selectpicker('val', data['ville']);
                    if ($('#colis-en-attente #bloc-select-ville-colis').hasClass('display-none')) {
                        $('#colis-en-attente #bloc-select-ville-colis').removeClass('display-none');
                    }
                    //Show select quartier
                    $('#colis-en-attente select[name="quartier"]').selectpicker('val', data['quartier']);
                    if ($('#colis-en-attente #bloc-select-quartier-colis').hasClass('display-none')) {
                        $('#colis-en-attente #bloc-select-quartier-colis').removeClass('display-none');
                    }
                    //Hide select point relai
                    $('#colis-en-attente select[name="point_relai_id"]').selectpicker('val', '');
                    if (!$('#colis-en-attente #bloc-select-point-relai-colis').hasClass('display-none')) {
                        $('#colis-en-attente #bloc-select-point-relai-colis').addClass('display-none');
                    }
                } else {
                    //Show select point relai
                    $('#colis-en-attente select[name="point_relai_id"]').selectpicker('val', data['point_relai_id']);
                    if ($('#colis-en-attente #bloc-select-point-relai-colis').hasClass('display-none')) {
                        $('#colis-en-attente #bloc-select-point-relai-colis').removeClass('display-none');
                    }
                    //Hide select ville
                    $('#colis-en-attente select[name="ville"]').selectpicker('val', data['ville']);
                    if (!$('#colis-en-attente #bloc-select-ville-colis').hasClass('display-none')) {
                        $('#colis-en-attente #bloc-select-ville-colis').addClass('display-none');
                    }
                    //Hide select quartier
                    $('#colis-en-attente select[name="quartier"]').selectpicker('val', '');
                    if (!$('#colis-en-attente #bloc-select-quartier-colis').hasClass('display-none')) {
                        $('#colis-en-attente #bloc-select-quartier-colis').addClass('display-none');
                    }
                }
                $('#colis-en-attente input[name="nom_complet"]').val(data['nom_complet']);
                $('#colis-en-attente input[name="telephone"]').val(data['telephone']);
                $('#colis-en-attente textarea[name="adresse"]').val(data['adresse']);
                if (parseInt(data['ouverture']) === 1) {
                    $('#colis-en-attente input[name="ouverture"]').prop('checked', true);
                } else {
                    $('#colis-en-attente input[name="ouverture"]').prop('checked', false);
                }
                if (parseInt(data['option_frais']) === 1) {
                    $('#colis-en-attente input[name="option_frais"]').prop('checked', true);
                } else {
                    $('#colis-en-attente input[name="option_frais"]').prop('checked', false);
                }
                if (parseInt(data['option_frais_assurance']) === 1) {
                    $('#colis-en-attente input[name="option_frais_assurance"]').prop('checked', true);
                } else {
                    $('#colis-en-attente input[name="option_frais_assurance"]').prop('checked', false);
                }
                $('#colis-en-attente textarea[name="commentaire"]').val(data['commentaire']);
            });
        }
    });
    //On change type livraison
    $('body').on('change', '#colis-en-attente select[name="type_livraison"]', function () {
        var typeLivraison = $('#colis-en-attente select[name="type_livraison"]').selectpicker('val');
        if (typeLivraison === '' || typeLivraison === 'a_domicile') {
            //Show select ville
            $('#colis-en-attente select[name="ville"]').selectpicker('val', '');
            if ($('#bloc-select-ville-colis').hasClass('display-none')) {
                $('#bloc-select-ville-colis').removeClass('display-none');
            }
            //Show select quartier
            $('#colis-en-attente select[name="quartier"]').selectpicker('val', '');
            if ($('#bloc-select-quartier-colis').hasClass('display-none')) {
                $('#bloc-select-quartier-colis').removeClass('display-none');
            }
            //Hide select point relai
            $('#colis-en-attente select[name="point_relai_id"]').selectpicker('val', '');
            if (!$('#bloc-select-point-relai-colis').hasClass('display-none')) {
                $('#bloc-select-point-relai-colis').addClass('display-none');
            }
        } else {
            //Show select point relai
            $('#colis-en-attente select[name="point_relai_id"]').selectpicker('val', '');
            if ($('#bloc-select-point-relai-colis').hasClass('display-none')) {
                $('#bloc-select-point-relai-colis').removeClass('display-none');
            }
            //Hide select ville
            $('#colis-en-attente select[name="ville"]').selectpicker('val', '');
            if (!$('#bloc-select-ville-colis').hasClass('display-none')) {
                $('#bloc-select-ville-colis').addClass('display-none');
            }
            //Hide select quartier
            $('#colis-en-attente select[name="quartier"]').selectpicker('val', '');
            if (!$('#bloc-select-quartier-colis').hasClass('display-none')) {
                $('#bloc-select-quartier-colis').addClass('display-none');
            }
        }
    });
    //ON CHANGE SELECT POINT RELAI
    $('body').on('change', 'select[name="point_relai_id"]', function () {
        var pointRelaiId = $('select[name="point_relai_id"]').selectpicker('val');
        if ($.isNumeric(pointRelaiId)) {
            $.post(client_url + "misc/get_city/" + pointRelaiId, function (response) {
                var result = $.parseJSON(response);
                var cityPointRelai = result.city;
                if ($.isNumeric(cityPointRelai)) {
                    $('#colis-en-attente select[name="ville"]').selectpicker('val', cityPointRelai);
                }
            });
        }
    });
    //ON CHANGE SELECT VILLE
    $('body').on('change', 'select[name="ville"]', function () {
        var ville_id = $('select[name="ville"]').val();
        if (ville_id !== '') {
            $.post(client_url + "colis_en_attente/get_quartiers_by_villeid/" + ville_id, function (response1) {
                var quartiers = $.parseJSON(response1);
                if (quartiers !== null) {
                    $('select[name="quartier"]').html('<option value=""></option>');
                    for (var i = 0; i < quartiers.length; i++) {
                        $('select[name="quartier"]').append('<option value="' + quartiers[i].id + '">' + quartiers[i].name + '</option>');
                    }
                    $('select[name="quartier"]').selectpicker('refresh');
                }
            });
        } else {
            $('select[name="ville"]').selectpicker('val', '');
            alert_float('warning', 'Sélectionner une ville !!');
        }
    });
});
// Manage colis en attente
function manage_colis_en_attente(form) {
    $('#colis-en-attente input[name="code_barre"]').attr('disabled', false);
    $('#colis-en-attente button[id="submit"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;

    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        if (response.success === true) {
            alert_float('success', response.message);
        } else if (response.success === 'access_denied') {
            alert_float('warning', response.message);
        }
        $('.table-colis-en-attente').DataTable().ajax.reload();
        $('#colis-en-attente').modal('hide');
    });

    return false;
}
$(document).ready(function () {
    var ColisEnAttenteServerParams = {
        "custom_view": "[name='custom_view']"
    };
    var headers_colis_en_attente = $('.table-colis-en-attente').find('th');
    var not_sortable_colis_en_attente = (headers_colis_en_attente.length - 1);
    initDataTable('.table-colis-en-attente', window.location.href, 'colis', [not_sortable_colis_en_attente], [not_sortable_colis_en_attente], ColisEnAttenteServerParams);
    var hidden_columns = [0];
    $.each(hidden_columns, function (i, val) {
        var column = $('.table-colis-en-attente').DataTable().column(val);
        column.visible(false);
    });
    //On click button export excel
    $('#btn-export-excel').on('click', function () {
        var dateStart = $('#delete-colis-en-attente-by-date input[name="start"]').val();
        var dateEnd = $('#delete-colis-en-attente-by-date input[name="end"]').val();
        if (dateStart !== '' && dateEnd !== '') {
            var url = $(this).attr('data-url');
            window.location.href = url + '?start=' + dateStart + '&end=' + dateEnd;
        } else {
            alert_float('warning', 'Remplissez la date de début et la date de fin');
        }
    });
    //Validate form
    _validate_form($('#delete-colis-en-attente-form'), {
        start: 'required',
        end: 'required'
    }, manage_delete_colis_en_attente);
    //Validate form
    _validate_form($('#colis-en-attente-form'), {
        num_commande: {
            required: true,
            check_numero_commande: {
                url: admin_url + "colis/check_num_commande_exists",
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
        type_livraison: 'required',
        livreur: {
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
        ville: {
            required: {
                depends: function () {
                    return ($('select[name="type_livraison"]').val() === 'a_domicile') ? true : false;
                }
            }
        },
        telephone: {
            required: true,
            remote: {
                url: site_url + "admin/colis/check_telephone",
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
        //Get value option point relai
        var showPointRelai = $('#colis input[id="show_point_relai"]').val();
        //Hide input barcode
        if (!$('#colis #bloc-input-barcode').hasClass('display-none')) {
            $('#colis #bloc-input-barcode').addClass('display-none');
        }
        //Init input, textarea and select
        $('#colis input').val('');
        $('#colis textarea').val('');
        $('#colis select').selectpicker('val', '');
        //Init select type livraison default value "a_domicile"
        $('#colis select[name="type_livraison"]').selectpicker('val', 'a_domicile');
        //Disabled input
        $('#colis input[name="code_barre"]').attr('disabled', true);
        $('#colis input[name="num_commande"]').attr('disabled', true);
        //Disabled checked input
        $('#colis input[name="ouverture"]').prop('checked', false);
        $('#colis input[name="option_frais"]').prop('checked', false);
        $('#colis input[name="option_frais_assurance"]').prop('checked', false);
        //Disabled button
        $('#colis button[id="submit"]').attr('disabled', false);
        //Check if point relai actived
        if (parseInt(showPointRelai) === 1) {
            $('#colis input[id="show_point_relai"]').val(showPointRelai);
            //Show select type livraison
            if ($('#colis #bloc-select-type-livraison-colis').hasClass('display-none')) {
                $('#colis #bloc-select-type-livraison-colis').removeClass('display-none');
            }
        } else {
            $('#colis input[id="show_point_relai"]').val(showPointRelai);
            //Hide select type livraison
            if (!$('#colis #bloc-select-type-livraison-colis').hasClass('display-none')) {
                $('#colis #bloc-select-type-livraison-colis').addClass('display-none');
            }
        }
        //Show select ville
        if($('#colis #bloc-select-ville-colis').hasClass('display-none')) {
            $('#colis #bloc-select-ville-colis').removeClass('display-none');
        }
        //Show select quartier
        if($('#colis #bloc-select-quartier-colis').hasClass('display-none')) {
            $('#colis #bloc-select-quartier-colis').removeClass('display-none');
        }
        //Show select livreur
        if ($('#colis #bloc-select-livreur-colis').hasClass('display-none')) {
            $('#colis #bloc-select-livreur-colis').removeClass('display-none');
        }
        //Hide select point relai
        if (!$('#colis #bloc-select-point-relai-colis').hasClass('display-none')) {
            $('#colis #bloc-select-point-relai-colis').addClass('display-none');
        }

        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $.post(admin_url + "colis_en_attente/get_info_colis_en_attente/" + id, function (response) {
                var data = $.parseJSON(response);
                $('#colis input[name="id"]').val('');
                $('#colis input[name="colis_en_attente_id"]').val(id);
                if ($('#colis #bloc-input-barcode').hasClass('display-none')) {
                    $('#colis #bloc-input-barcode').removeClass('display-none');
                }
                $('#colis input[name="code_barre"]').val(data['code_barre']);
                $('#colis input[name="num_commande"]').val(data['num_commande']);
                if (parseInt(data['ouverture']) === 1) {
                    $('#colis input[name="ouverture"]').prop('checked', true);
                }
                if (parseInt(data['option_frais']) === 1) {
                    $('#colis input[name="option_frais"]').prop('checked', true);
                }
                if (parseInt(data['option_frais_assurance']) === 1) {
                    $('#colis input[name="option_frais_assurance"]').prop('checked', true);
                }
                $('#colis select[name="type_livraison"]').selectpicker('val', data['type_livraison']);
                $('#colis select[name="id_expediteur"]').selectpicker('val', data['id_expediteur']);
                if (data['type_livraison'] === 'a_domicile') {
                    //Show select ville
                    $('#colis select[name="ville"]').selectpicker('val', data['ville']);
                    if (data['ville'] !== 0) {
                        $('#colis select[name="ville"]').change();
                    }
                    if ($('#colis #bloc-select-ville-colis').hasClass('display-none')) {
                        $('#colis #bloc-select-ville-colis').removeClass('display-none');
                    }
                    //Show select quartier
                    $('#colis select[name="quartier"]').selectpicker('val', data['quartier']);
                    if (data['quartier'] !== 0) {
                        $('#colis select[name="quartier"]').change();
                    }
                    if ($('#colis #bloc-select-quartier-colis').hasClass('display-none')) {
                        $('#colis #bloc-select-quartier-colis').removeClass('display-none');
                    }
                    //Show select livreur
                    if ($('#colis #bloc-select-livreur-colis').hasClass('display-none')) {
                        $('#colis #bloc-select-livreur-colis').removeClass('display-none');
                    }
                    //Hide select point relai
                    $('#colis select[name="point_relai_id"]').selectpicker('val', '');
                    if (!$('#colis #bloc-select-point-relai-colis').hasClass('display-none')) {
                        $('#colis #bloc-select-point-relai-colis').addClass('display-none');
                    }
                } else {
                    //Show select point relai
                    $('#colis select[name="point_relai_id"]').selectpicker('val', data['point_relai_id']);
                    if ($('#colis #bloc-select-point-relai-colis').hasClass('display-none')) {
                        $('#colis #bloc-select-point-relai-colis').removeClass('display-none');
                    }
                    //Hide select ville
                    $('#colis select[name="ville"]').selectpicker('val', data['ville']);
                    if (data['ville'] !== 0) {
                        $('#colis select[name="ville"]').change();
                    }
                    if (!$('#colis #bloc-select-ville-colis').hasClass('display-none')) {
                        $('#colis #bloc-select-ville-colis').addClass('display-none');
                    }
                    //Hide select quartier
                    $('#colis select[name="quartier"]').selectpicker('val', '');
                    if (!$('#colis #bloc-select-quartier-colis').hasClass('display-none')) {
                        $('#colis #bloc-select-quartier-colis').addClass('display-none');
                    }
                    //Hide select livreur
                    $('#colis select[name="livreur"]').selectpicker('val', '');
                    if(!$('#colis #bloc-select-livreur-colis').hasClass('display-none')) {
                        $('#colis #bloc-select-livreur-colis').addClass('display-none');
                    }
                }
                $('#colis input[name="nom_complet"]').val(data['nom_complet']);
                var telephone = data['telephone'];
                telephone = telephone.split(' ').join('');
                $('#colis input[name="telephone"]').val(telephone);
                $('#colis input[name="crbt"]').val(data['crbt']);
                $('#colis textarea[name="adresse"]').val(data['adresse']);
                $('#colis textarea[name="commentaire"]').val(data['commentaire']);
                $('#colis input[name="importer"]').val(data['importer']);
            });
        }
    });
    //On change type livraison
    $('body').on('change', '#colis select[name="type_livraison"]', function () {
        var typeLivraison = $('#colis select[name="type_livraison"]').selectpicker('val');
        if (typeLivraison === '' || typeLivraison === 'a_domicile') {
            //Show select ville
            $('#colis select[name="ville"]').selectpicker('val', '');
            if ($('#bloc-select-ville-colis').hasClass('display-none')) {
                $('#bloc-select-ville-colis').removeClass('display-none');
            }
            //Show select quartier
            $('#colis select[name="quartier"]').selectpicker('val', '');
            if ($('#bloc-select-quartier-colis').hasClass('display-none')) {
                $('#bloc-select-quartier-colis').removeClass('display-none');
            }
            //Show select livreur
            $('#colis select[name="livreur"]').selectpicker('val', '');
            if ($('#bloc-select-livreur-colis').hasClass('display-none')) {
                $('#bloc-select-livreur-colis').removeClass('display-none');
            }
            //Hide select point relai
            $('#colis select[name="point_relai_id"]').selectpicker('val', '');
            if (!$('#bloc-select-point-relai-colis').hasClass('display-none')) {
                $('#bloc-select-point-relai-colis').addClass('display-none');
            }
        } else {
            //Show select point relai
            $('#colis select[name="point_relai_id"]').selectpicker('val', '');
            if ($('#bloc-select-point-relai-colis').hasClass('display-none')) {
                $('#bloc-select-point-relai-colis').removeClass('display-none');
            }
            //Hide select ville
            $('#colis select[name="ville"]').selectpicker('val', '');
            if (!$('#bloc-select-ville-colis').hasClass('display-none')) {
                $('#bloc-select-ville-colis').addClass('display-none');
            }
            //Hide select quartier
            $('#colis select[name="quartier"]').selectpicker('val', '');
            if (!$('#bloc-select-quartier-colis').hasClass('display-none')) {
                $('#bloc-select-quartier-colis').addClass('display-none');
            }
            //Hide select livreur
            $('#colis select[name="livreur"]').selectpicker('val', '');
            if (!$('#bloc-select-livreur-colis').hasClass('display-none')) {
                $('#bloc-select-livreur-colis').addClass('display-none');
            }
        }
    });
    //ON CHANGE SELECT POINT RELAI
    $('body').on('change', 'select[name="point_relai_id"]', function () {
        var pointRelaiId = $('select[name="point_relai_id"]').selectpicker('val');
        if ($.isNumeric(pointRelaiId)) {
            $.post(admin_url + "points_relais/get_city/" + pointRelaiId, function (response) {
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
    //ON CHANGE SELECT QUARTIER SHOW LIVREUR
    $('body').on('change', 'select[name="quartier"]', function () {
        var quartier_id = $('select[name="quartier"]').val();
        $('select[name="livreur"]').selectpicker('val', '');
        if (typeof (quartier_id) !== 'undefined' && quartier_id !== '') {
            $.post(admin_url + "quartiers_livreur/get_livreurs_by_quartier/" + quartier_id, function (response) {
                var data = $.parseJSON(response);
                if (data !== null && typeof (data) !== 'undefined' && data.length > 0) {
                    var selected_id = '';
                    $('select[name="livreur"]').html('<option value=""></option>');
                    for (var i = 0; i < data.length; i++) {
                        if (i === 0) {
                            selected_id = data[i].id;
                        }
                        $('select[name="livreur"]').append('<option value="' + data[i].id + '">' + data[i].firstname + ' ' + data[i].lastname + '</option>');
                    }
                    $('select[name="livreur"]').selectpicker('refresh');
                    $('select[name="livreur"]').selectpicker('val', selected_id);
                    alert_float('success', 'Vous Pouvez voir les livreurs affecter à ce quartier');
                }
            });
        } else {
            $.post(admin_url + "staff/get_livreurs", function (response) {
                var data = $.parseJSON(response);
                if (data !== null && typeof (data) !== 'undefined' && data.length > 0) {
                    $('select[name="livreur"]').html('<option value=""></option>');
                    for (var i = 0; i < data.length; i++) {
                        $('select[name="livreur"]').append('<option value="' + data[i].id + '">' + data[i].firstname + ' ' + data[i].lastname + '</option>');
                    }
                    $('select[name="livreur"]').selectpicker('refresh');
                }
            });
        }
    });

    //ON CHANGE SELECT CLIENT
    $('body').on('change', 'select[name="id_expediteur"]', function () {
        var expediteurId = $('select[name="id_expediteur"]').selectpicker('val');
        if ($.isNumeric(expediteurId)) {
            $.post(admin_url + "expediteurs/get_ouverture_colis/" + expediteurId, function (response) {
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
            $.post(admin_url + "expediteurs/get_expediteur_by_id/" + expediteur_id + '/' + ville_id, function (response) {
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
                        if(result.frais_special > 0) {
                            fraisInitiale = result.frais_special;
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
                    $.post(admin_url + "villes/get_shipping_cost/" + ville_id, function (response) {
                        var result = $.parseJSON(response);
                        $('input[name="frais"]').val((parseFloat(result.shipping_cost)).toFixed(2));
                    });
                } else {
                    $.post(admin_url + "expediteurs/get_expediteur_by_id/" + expediteur_id + '/' + ville_id, function (response) {
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
                                if(result.frais_special > 0) {
                                    fraisInitiale = result.frais_special;
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

                $.post(admin_url + "quartiers/get_quartiers_by_villeid/" + ville_id, function (response1) {
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

function manage_delete_colis_en_attente(form) {
    var r = confirm("Vous avez déjà télécharger l'excel de ces colis en attente avant de les supprimer ?");
    if (r === true) {
        var data = $(form).serialize();
        var url = form.action;

        $.post(url, data).success(function (response) {
            response = $.parseJSON(response);
            if (response.success === true) {
                alert_float('success', response.message);
                $('.table-colis-en-attente').DataTable().ajax.reload();
            } else {
                alert_float('warning', response.message);
            }
            $('#delete-colis-en-attente-by-date').modal('hide');
        });
    }
}
function manage_colis(form) {
    $('#colis input[name="code_barre"]').attr('disabled', false);
    $('#colis input[name="num_commande"]').attr('disabled', false);
    $('#colis button[id="submit"]').attr('disabled', true);
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
        $('#colis').modal('hide');
    });
    return false;
}
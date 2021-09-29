$(document).ready(function () {
    var headers_colis = $('.table-colis').find('th');
    var not_sortable_colis = (headers_colis.length - 1);
    var ColisServerParams = {
        "custom_view": "[name='custom_view']",
        "bonlivraison": "[name='bonlivraison']",
        "f-type-livraison": "[name='f-type-livraison']",
        "f-livreur": "[name='f-livreur']",
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
    //Validate form export colis
    $('body').on('click', '#export-colis, #export-colis-facturer', function (e) {
        e.preventDefault();
        var dateRamassageStart = $('input[name="f-date-ramassage-start"]').val();
        var dateRamassageEnd = $('input[name="f-date-ramassage-end"]').val();
        var dateLivraisonStart = $('input[name="f-date-livraison-start"]').val();
        var dateLivraisonEnd = $('input[name="f-date-livraison-end"]').val();
        var check = true;
        /*if(dateRamassageStart === '' && dateLivraisonStart === '') {
            alert_float('warning', 'Date début de ramassage ou bien Date début de livraison est obligatoire !!');
        } else if(dateRamassageStart !== '' && dateLivraisonStart === '') {
            if(dateRamassageEnd !== '') {
                var diffDateRamassage = difference_entre_date(dateRamassageStart, dateRamassageEnd);
                if(diffDateRamassage.day <= 30) {
                    check = true;
                }
            } else {
                alert_float('warning', 'Date fin de ramassage est obligatoire !!');
            }
        } else if(dateLivraisonStart !== '' && dateRamassageStart === '') {
            if(dateLivraisonEnd !== '') {
                var diffDateLivraison = difference_entre_date(dateLivraisonStart, dateLivraisonEnd);
                if(diffDateLivraison.day <= 30) {
                    check = true;
                }
            } else {
                alert_float('warning', 'Date fin de livraison est obligatoire !!');
            }
        } else {
            if(dateRamassageEnd === '') {
                alert_float('warning', 'Date fin de ramassage est obligatoire !!');
            }
            if(dateLivraisonEnd === '') {
                alert_float('warning', 'Date fin de livraison est obligatoire !!');
            }
            if(dateRamassageEnd !== '' && dateLivraisonEnd !== '') {
                var diffDateRamassage = difference_entre_date(dateRamassageStart, dateRamassageEnd);
                var diffDateLivraison = difference_entre_date(dateLivraisonStart, dateLivraisonEnd);
                if(diffDateRamassage.day <= 30 && diffDateLivraison.day <= 30) {
                    check = true;
                }
            }
        }*/

        if(check === true) {
            if($(this).attr('id') === 'export-colis-facturer') {
                $('input[name="colis-facturer"]').val(1);
            } else {
                $('#colis-facturer').val(0);
            }
            $('#form-export-colis').submit();
        } else {
            alert_float('warning', 'L\'intervale entre la date de début et de fin est d\'un mois');
        }
    });
    //Validate form colis
    _validate_form($('#form-colis'), {
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
        //Init select type livraison default value "a_domicile"
        $('#colis select[name="type_livraison"]').selectpicker('val', 'a_domicile');
        //Disabled input
      $('#colis input[name="num_commande"]').attr('disabled', false);
        $('#colis input[name="crbt"]').attr('disabled', false);
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
        if ($('#colis #bloc-select-ville-colis').hasClass('display-none')) {
            $('#colis #bloc-select-ville-colis').removeClass('display-none');
        }
        //Show select quartier
        if ($('#colis #bloc-select-quartier-colis').hasClass('display-none')) {
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
            $.post(admin_url + "colis/get_info_colis/" + id, function (response) {
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
                $('select[name="type_livraison"]').selectpicker('val', data['type_livraison']);
                $('select[name="type_livraison"]').attr('disabled',false)
                $('select[name="id_expediteur"]').selectpicker('val', data['id_expediteur']);
                $('select[name="id_expediteur"]').attr('disabled',true)
                if (data['type_livraison'] === 'a_domicile') {
                    //Show select ville
                    $('select[name="ville"]').selectpicker('val', data['ville']);
                    $('select[name="ville"]').attr('disabled',false)
                    if ($('#bloc-select-ville-colis').hasClass('display-none')) {
                        $('#bloc-select-ville-colis').removeClass('display-none');
                    }

                    if (parseInt(data['crbt_modifiable']) === 1) {
                        $('select[name="ville"]').attr('disabled',false)
                        $('select[name="type_livraison"]').attr('disabled',false)
                    }
                    //Show select quartier


                    $('select[name="quartier"]').selectpicker('val', data['quartier']);
                    $('select[name="quartier"]').attr('disabled',false)
                    if ($('#bloc-select-quartier-colis').hasClass('display-none')) {
                        $('#bloc-select-quartier-colis').removeClass('display-none');
                    }
                    //Show select livreur
                    $('select[name="livreur"]').selectpicker('val', data['livreur']);


                    if ($('#colis #bloc-select-livreur-colis').hasClass('display-none')) {
                        $('#colis #bloc-select-livreur-colis').removeClass('display-none');
                    }
                    //Hide select point relai
                    $('select[name="point_relai_id"]').selectpicker('val', '');
                    $('select[name="point_relai_id"]').attr('disabled',false)

                    if (!$('#colis #bloc-select-point-relai-colis').hasClass('display-none')) {
                        $('#colis #bloc-select-point-relai-colis').addClass('display-none');
                    }
                } else {
                    //Show select point relai
                    $('select[name="point_relai_id"]').selectpicker('val', data['point_relai_id']);
                    if ($('#colis #bloc-select-point-relai-colis').hasClass('display-none')) {
                        $('#colis #bloc-select-point-relai-colis').removeClass('display-none');
                    }
                    //Hide select ville
                    $('select[name="ville"]').selectpicker('val', data['ville']);
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
                    if (!$('#colis #bloc-select-livreur-colis').hasClass('display-none')) {
                        $('#colis #bloc-select-livreur-colis').addClass('display-none');
                    }
                }
                $('#colis input[name="nom_complet"]').val(data['nom_complet']);
                $('#colis input[name="nom_complet"]').attr('disabled', false);
                $('#colis input[name="telephone"]').val(data['telephone']);
                $('#colis input[name="telephone"]').attr('disabled', false);
                $('#colis input[name="crbt"]').val(data['crbt']);
                $('#colis input[name="crbt"]').prop('disabled', true);
                $('#colis input[name="frais"]').val(data['frais']);
                $('#colis input[name="frais"]').attr('disabled', false);
                $('#colis textarea[name="adresse"]').val(data['adresse']);
                $('#colis textarea[name="adresse"]').attr('disabled', false);
                $('#colis textarea[name="commentaire"]').val(data['commentaire']);
                $('#colis textarea[name="commentaire"]').attr('disabled', false);


                if (parseInt(data['crbt_modifiable']) === 1) {
                      $('#colis input[name="crbt"]').prop('disabled', false);
                      $('#colis textarea[name="commentaire"]').attr('disabled', false);
                     $('#colis textarea[name="adresse"]').attr('disabled', false);
                    $('#colis input[name="frais"]').attr('disabled', false);
                   $('#colis input[name="nom_complet"]').attr('disabled', false);
                    $('#colis input[name="telephone"]').attr('disabled', false);
                    $('select[name="quartier"]').attr('disabled',false);
                    $('select[name="point_relai_id"]').attr('disabled',false);
                    $('select[name="id_expediteur"]').attr('disabled',true)
                    $('select[name="ville"]').attr('disabled',false)
                    $('select[name="type_livraison"]').attr('disabled',false)

                }
            });
        } else {
            $('#colis input[name="telephone"]').val('06');
            $('#colis input[name="crbt"]').prop('disabled', false);
            $('#colis textarea[name="commentaire"]').attr('disabled', false);
            $('#colis textarea[name="adresse"]').attr('disabled', false);
            $('#colis input[name="frais"]').attr('disabled', false);
            $('#colis input[name="nom_complet"]').attr('disabled', false);
            $('#colis input[name="telephone"]').attr('disabled', false);
            $('select[name="quartier"]').attr('disabled',false);
            $('select[name="point_relai_id"]').attr('disabled',false);
            $('select[name="id_expediteur"]').attr('disabled',false)
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
    //Show historique
    $('#historiques').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var coli_id = $(invoker).data('coli-id');
        $('#historiques input[name="f-coli-id"]').val(coli_id);
        var barcode = $(invoker).data('barcode');
        $('#historiques input[name="f-code-barre"]').val(barcode);

        var count = $('#historiques input[name="historique-count"]').val();
        if (count === '0') {
            $('#historiques input[name="historique-count"]').val(1);
            //Init Data Table Historiques Status Coli
            var HistoriquesStatusServerParams = {
                "f-code-barre": "[name='f-code-barre']"
            };
            initDataTable('.table-historiques-status', admin_url + 'status', 'historiques-status', 'undefined', 'undefined', HistoriquesStatusServerParams);
            var hidden_historiques_status_columns = [0];
            $.each(hidden_historiques_status_columns, function (i, val) {
                console.log(val);
                var column_historiques_status = $('.table-historiques-status').DataTable().column(val);
                console.log(column_historiques_status);
                column_historiques_status.visible(false);
            });

            //Init Data Table Historiques Bons Livraison Coli
            var headers_historiques_bons_livraison = $('.table-historiques-bons-livraison').find('th');
            var not_sortable_historiques_bons_livraison = (headers_historiques_bons_livraison.length - 1);
            var HistoriquesBonsLivraisonServerParams = {
                "f-coli-id": "[name='f-coli-id']"
            };
            initDataTable('.table-historiques-bons-livraison', admin_url + 'colis/historiques_bons_livraison', 'historiques-bons-livraison', [not_sortable_historiques_bons_livraison], [not_sortable_historiques_bons_livraison], HistoriquesBonsLivraisonServerParams);
            var hidden_historiques_bons_livraison_columns = [0];
            $.each(hidden_historiques_bons_livraison_columns, function (i, val) {
                var column_historiques_bons_livraison = $('.table-historiques-bons-livraison').DataTable().column(val);
                column_historiques_bons_livraison.visible(false);
            });

            //Init Data Table Historiques Appels Livreur Coli
            var HistoriquesAppelsLivreurServerParams = {
                "f-coli-id": "[name='f-coli-id']"
            };
            initDataTable('.table-historiques-appels-livreur', admin_url + 'appels/livreurs', 'historiques-appels-livreur', 'undefined', 'undefined', HistoriquesAppelsLivreurServerParams);
            var hidden_historiques_appels_livreur_columns = [0];
            $.each(hidden_historiques_appels_livreur_columns, function (i, val) {
                var column_historiques_appels_livreur = $('.table-historiques-appels-livreur').DataTable().column(val);
                column_historiques_appels_livreur.visible(false);
            });

            //Init Data Table Historiques coli info

      var HistoriquesColiInfoServerParams = {
            "f-coli-id": "[name='f-coli-id']"
            };
// admin_url + 'colis/historiques_coli_info'
           initDataTable('.table-historiques-coli-info',admin_url + 'colis/historiques_coli_info', 'historiques-appels-livreur', 'undefined', 'undefined', HistoriquesColiInfoServerParams);
           var hidden_historiques_appels_livreur_columns = [0];
           $.each(hidden_historiques_appels_livreur_columns, function (i, val) {
              var column_historiques_appels_livreur = $('.table-historiques-appels-livreur').DataTable().column(val);
            column_historiques_appels_livreur.visible(false);
           });




        } else {
            $('.table-historiques-status').DataTable().ajax.reload();
            $('.table-historiques-bons-livraison').DataTable().ajax.reload();
            $('.table-historiques-appels-livreur').DataTable().ajax.reload();
            $('.table-historiques-coli-info').DataTable().ajax.reload();


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


//send message
    $('body').on('click', '#submit-message-discussion', function () {
        $('#submit-message-discussion').attr('disabled', true);
        var content = $('textarea[name="message_discussion"]').val();
        var demandeId = $('input[name="demande_id"]').val();
        if ($.isNumeric(demandeId) && content !== '') {
            $.post(admin_url + 'demandes/add_discussion', {demande_id: demandeId, content: content}).success(function (response) {
                response = $.parseJSON(response);
                alert_float(response.type, response.message);
                if (response.success === true) {
                    init_discussion(demandeId);
                    $('textarea[name="message_discussion"]').val('');
                }
                $('#submit-message-discussion').attr('disabled', false);
            });
        } else {
            alert_float('warning', 'Le message est obligatoire !!');
            $('#submit-message-discussion').attr('disabled', false);
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
//remove reporty

  
});

function manage_colis(form) {
  $('#colis input[name="code_barre"]').attr('disabled', false);
    $('#colis input[name="num_commande"]').attr('disabled', false);
    $('#colis input[name="crbt"]').attr('disabled', false);
    $('#colis button[id="submit"]').attr('disabled', true);
    $('select[name="ville"]').attr('disabled',false)
    $('select[name="type_livraison"]').attr('disabled',false)
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

// Datatables bon livraison
function dt_bon_livraison(view, table) {
    $('input[name="bonlivraison"]').val(view);
    $(table).DataTable().ajax.reload();
    $('input[name="bonlivraison"]').val('');
}



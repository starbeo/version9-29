$(document).ready(function () {
    // Init data table versements
    var headers_versements = $('.table-versements').find('th');
    var not_sortable_versements = (headers_versements.length - 1);
    var versementsServerParams = {
        "f-type-livraison": "[name='f-type-livraison']",
        "f-livreur": "[name='f-livreur']",
        "f-point-relai": "[name='f-point-relai']",
        "f-utilisateur": "[name='f-utilisateur']",
        "f-date-created": "[name='f-date-created']"
    };
    initDataTable('.table-versements', window.location.href, 'Versements', [not_sortable_versements], [not_sortable_versements], versementsServerParams);
    // Hide columns table versements
    var hidden_columns_versements = [0];
    $.each(hidden_columns_versements, function (i, val) {
        var column_versements = $('.table-versements').DataTable().column(val);
        column_versements.visible(false);
    });
    // Validate form versement
    _validate_form($('#form-versement'), {
        type_livraison: 'required',
        livreur: {
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
        },
        etat_colis_livre_id: 'required',
        total: 'required'
    }, manage_versement);
    // Show modal add & edit versement
    $('#versement_modal').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#versement_modal .add-title').removeClass('hide');
        $('#versement_modal .edit-title').addClass('hide');
        //Get value option point relai
        var showPointRelai = $('#versement_modal input[id="show_point_relai"]').val();
        $('#versement_modal input').val('');
        $('#versement_modal select[name="livreur_id"]').selectpicker('val', '');
        //Init select type livraison default value "a_domicile"
        $('#versement_modal select[name="type_livraison"]').selectpicker('val', 'a_domicile');
        //Check if point relai actived
        if (parseInt(showPointRelai) === 1) {
            $('#versement_modal input[id="show_point_relai"]').val(showPointRelai);
            //Show select type livraison
            if ($('#versement_modal #bloc-select-type-livraison-versement').hasClass('display-none')) {
                $('#versement_modal #bloc-select-type-livraison-versement').removeClass('display-none');
            }
        } else {
            $('#versement_modal input[id="show_point_relai"]').val(showPointRelai);
            //Hide select type livraison
            if (!$('#versement_modal #bloc-select-type-livraison-versement').hasClass('display-none')) {
                $('#versement_modal #bloc-select-type-livraison-versement').addClass('display-none');
            }
        }
        //Show select livreur
        if ($('#versement_modal #bloc-select-livreur-versement').hasClass('display-none')) {
            $('#versement_modal #bloc-select-livreur-versement').removeClass('display-none');
        }
        //Hide select point relai
        if (!$('#versement_modal #bloc-select-point-relai-versement').hasClass('display-none')) {
            $('#versement_modal #bloc-select-point-relai-versement').addClass('display-none');
        }

        $('select[name="etat_colis_livre_id"]').html('<option value=""></option>');
        $('select[name="etat_colis_livre_id"]').selectpicker('refresh');
        if (!$('#bloc_rest').hasClass('display-none')) {
            $('#bloc_rest').addClass('display-none');
        }
        $('#versement_modal button[id="submit"]').attr('disabled', false);

        // is from the edit button
        if (typeof (id) !== 'undefined') {
            var typeLivraison = $(invoker).data('type-livraison');
            var livreurId = $(invoker).data('livreur-id');
            var etatColisLivrerId = $(invoker).data('etat-colis-livrer-id');
            var total = $(invoker).data('total');
            $('#versement_modal input[name="id"]').val(id);
            $('#versement_modal .add-title').addClass('hide');
            $('#versement_modal .edit-title').removeClass('hide');
            $('#versement_modal select[name="type_livraison"]').selectpicker('val', typeLivraison);
            if (typeLivraison === 'a_domicile') {
                //Show select livreur
                $('select[name="livreur_id"]').selectpicker('val', livreurId);
                if ($('#versement_modal #bloc-select-livreur-versement').hasClass('display-none')) {
                    $('#versement_modal #bloc-select-livreur-versement').removeClass('display-none');
                }
                //Hide select point relai
                $('select[name="user_point_relais"]').selectpicker('val', '');
                if (!$('#versement_modal #bloc-select-point-relai-versement').hasClass('display-none')) {
                    $('#versement_modal #bloc-select-point-relai-versement').addClass('display-none');
                }
            } else {
                //Show select point relai
                $('select[name="user_point_relais"]').selectpicker('val', livreurId);
                if ($('#versement_modal #bloc-select-point-relai-versement').hasClass('display-none')) {
                    $('#versement_modal #bloc-select-point-relai-versement').removeClass('display-none');
                }
                //Hide select livreur
                $('#versement_modal select[name="livreur_id"]').selectpicker('val', '');
                if (!$('#versement_modal #bloc-select-livreur-versement').hasClass('display-none')) {
                    $('#versement_modal #bloc-select-livreur-versement').addClass('display-none');
                }
            }
            $('#versement_modal select[name="livreur_id"]').selectpicker('val', livreurId);
            $('#versement_modal input[name="total"]').val((parseFloat(total)).toFixed(2));
            initSelectEtatColisLivrer(typeLivraison, livreurId, etatColisLivrerId);
        }
    });
    //On change type livraison
    $('body').on('change', '#versement_modal select[name="type_livraison"]', function () {
        var typeLivraison = $('#versement_modal select[name="type_livraison"]').selectpicker('val');
        if (typeLivraison === '' || typeLivraison === 'a_domicile') {
            //Show select livreur
            $('#versement_modal select[name="livreur_id"]').selectpicker('val', '');
            if ($('#bloc-select-livreur-versement').hasClass('display-none')) {
                $('#bloc-select-livreur-versement').removeClass('display-none');
            }
            //Hide select point relai
            $('#versement_modal select[name="user_point_relais"]').selectpicker('val', '');
            if (!$('#bloc-select-point-relai-versement').hasClass('display-none')) {
                $('#bloc-select-point-relai-versement').addClass('display-none');
            }
        } else {
            //Show select point relai
            $('#versement_modal select[name="user_point_relais"]').selectpicker('val', '');
            if ($('#bloc-select-point-relai-versement').hasClass('display-none')) {
                $('#bloc-select-point-relai-versement').removeClass('display-none');
            }
            //Hide select livreur
            $('#versement_modal select[name="livreur_id"]').selectpicker('val', '');
            if (!$('#bloc-select-livreur-versement').hasClass('display-none')) {
                $('#bloc-select-livreur-versement').addClass('display-none');
            }
        }
    });
    // On click button filter
    $('body').on('click', '.btn-filtre', function () {
        if ($('#filtre-table').hasClass('display-none')) {
            $('#filtre-table').removeClass('display-none');
        } else {
            $('#filtre-table').addClass('display-none');
        }
    });
    // On submit filter
    $('body').on('click', '#filtre-submit', function () {
        $('.table-versements').DataTable().ajax.reload();
        $('#filtre-table').addClass('display-none');
    });
    // Reset filter
    $('body').on('click', '#filtre-reset', function () {
        $('#filtre-table select').selectpicker('val', '');
        $('#filtre-table input').val('');
        $('.table-versements').DataTable().ajax.reload();
    });
    //On change select delivery men
    $('body').on('change', 'select[name="livreur_id"], select[name="user_point_relais"]', function () {
        var typeLivraison = $('select[name="type_livraison"]').selectpicker('val');
        if (typeLivraison !== '') {
            if (typeLivraison === 'a_domicile') {
                var livreurId = $('select[name="livreur_id"]').selectpicker('val');
                if ($.isNumeric(livreurId)) {
                    initSelectEtatColisLivrer(typeLivraison, livreurId);
                } else {
                    alert_float('warning', 'Sélectionner un livreur.');
                }
            } else if (typeLivraison === 'point_relai') {
                var userPointRelaisId = $('select[name="user_point_relais"]').selectpicker('val');
                if ($.isNumeric(userPointRelaisId)) {
                    initSelectEtatColisLivrer(typeLivraison, userPointRelaisId);
                } else {
                    alert_float('warning', 'Sélectionner un point relais.');
                }
            }
        }
    });
    //On change select delivery men
    $('body').on('change', 'select[name="etat_colis_livre_id"]', function () {
        if (!$('#bloc_rest').hasClass('display-none')) {
            $('#bloc_rest').addClass('display-none');
        }

        var typeLivraison = $('select[name="type_livraison"]').selectpicker('val');
        if (typeLivraison !== '') {
            if (typeLivraison === 'a_domicile') {
                var livreurId = $('select[name="livreur_id"]').selectpicker('val');
            } else if (typeLivraison === 'point_relai') {
                var livreurId = $('select[name="user_point_relais"]').selectpicker('val');
            }
            if ($.isNumeric(livreurId)) {
                var etatColisLivrerId = $('select[name="etat_colis_livre_id"]').selectpicker('val');
                if ($.isNumeric(etatColisLivrerId)) {
                    $.post(admin_url + "etat_colis_livrer/check_etat_colis_livrer", {livreur_id: livreurId, etat_colis_livrer_id: etatColisLivrerId}, function (response) {
                        var response = $.parseJSON(response);
                        if (response.exist === false) {
                            $('#form-versement #rest').html(response.rest + ' Dhs');
                            $('#bloc_rest').removeClass('display-none');
                        } else {
                            alert_float('danger', 'Un versement existe déjà avec le même livreur et la même Etat Colis Livrer.');
                            $('select[name="etat_colis_livre_id"]').selectpicker('val', '');
                        }
                    });
                } else {
                    alert_float('warning', 'Sélectionner une état colis livrer.');
                }
            } else {
                if (typeLivraison === 'a_domicile') {
                    alert_float('warning', 'Sélectionner un livreur.');
                } else if (typeLivraison === 'point_relai') {
                    alert_float('warning', 'Sélectionner un point relais.');
                }
            }
        }
    });
});

function manage_versement(form)
{
    $('#versement_modal button[id="submit"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        if (response.success === true) {
            $('.table-versements').DataTable().ajax.reload();
            alert_float('success', response.message);
        }
        $('#versement_modal').modal('hide');
    });

    return false;
}

function initSelectEtatColisLivrer(typeLivraison, livreurId, etatColisLivrerId)
{
    if (typeLivraison !== '' && $.isNumeric(livreurId)) {
        $.post(admin_url + "etat_colis_livrer/get_list_etat_colis_livrer", {type_livraison: typeLivraison, livreur_id: livreurId, etat_colis_livrer_id: etatColisLivrerId}, function (response) {
            var response = $.parseJSON(response);
            $('select[name="etat_colis_livre_id"]').html('<option value=""></option>');
            for (var i = 0; i < response.length; i++) {
                $('select[name="etat_colis_livre_id"]').append('<option value="' + response[i].id + '">' + response[i].nom + '</option>');
            }
            $('select[name="etat_colis_livre_id"]').selectpicker('refresh');
            if ($.isNumeric(etatColisLivrerId)) {
                $('#versement_modal select[name="etat_colis_livre_id"]').selectpicker('val', etatColisLivrerId);
            }
        });
    }
}
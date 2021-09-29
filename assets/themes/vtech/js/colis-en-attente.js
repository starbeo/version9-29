// JS files used for colis en attente
$(document).ready(function () {
    //Init Data Table Colis en attente
    var headers_colis_en_attente = $('.table-colis-en-attente').find('th');
    var not_sortable_colis_en_attente = (headers_colis_en_attente.length - 1);
    initDataTable('.table-colis-en-attente', window.location.href, 'colis', [not_sortable_colis_en_attente], [not_sortable_colis_en_attente], [4, 'DESC']);

    $.validator.addMethod(
        "regex",
        function (value, element, regexp) {
            var check = false;
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "<i class='fa fa-exclamation-triangle' style='font-size:14px;color:red'></i> Les caractères spéciaux sont interdits, (Merci de revoir l'adresse que vous avez entrer, s'il ya un espace à la fin ou bien un saut de la ligne il faut le supprimer)"
    );

    _validate_form($('#colis-en-attente-form'), {
        nom_complet: 'required',
        crbt: 'required',
        ville: 'required',
        adresse: {
            "required": true,
            "regex": /^[a-zA-Z0-9-\/] ?([a-zA-Z0-9-\/]|[a-zA-Z0-9-\/] )*[a-zA-Z0-9-\/]$/
        },
        commentaire: {
            "regex": /^[a-zA-Z0-9-\/] ?([a-zA-Z0-9-\/]|[a-zA-Z0-9-\/] )*[a-zA-Z0-9-\/]$/
        }
    }, manage_colis_en_attente);

    $('#colis-en-attente').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        var prefix = $('#prefix').val();
        $('#colis-en-attente .add-title').removeClass('hide');
        $('#colis-en-attente .edit-title').addClass('hide');
        if (! $('#colis-en-attente #bloc-input-barcode').hasClass('display-none')) {
            $('#colis-en-attente #bloc-input-barcode').addClass('display-none');
        }
        $('#colis-en-attente input').val('');
        $('#colis-en-attente textarea').val('');
        $('#colis-en-attente select').selectpicker('val', '');
        $('#colis-en-attente input[name="num_commande"]').val(prefix);
        $('#colis-en-attente button[id="submit"]').attr('disabled', false);

        $.post(site_url + "expediteurs/get_settings_client", function (response1) {
            var response1 = $.parseJSON(response1);
            if(response1.success === true) {
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
            }
        });
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $.post(site_url + "expediteurs/get_info_colis_en_attente/" + id, function (response) {
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
                if(parseInt(data['ouverture']) === 1) {
                    $('#colis-en-attente input[name="ouverture"]').prop('checked', true);
                }
                if (parseInt(data['option_frais']) === 1) {
                    $('#colis-en-attente input[name="option_frais"]').prop('checked', true);
                }
                $('#colis-en-attente input[name="crbt"]').val(data['crbt']);
                $('#colis-en-attente input[name="nom_complet"]').val(data['nom_complet']);
                $('#colis-en-attente input[name="telephone"]').val(data['telephone']);
                $('#colis-en-attente textarea[name="adresse"]').val(data['adresse']);
                $('#colis-en-attente select[name="quartier"]').selectpicker('val', data['quartier']);
                $('#colis-en-attente select[name="ville"]').selectpicker('val', data['ville']);
                $('#colis-en-attente textarea[name="commentaire"]').val(data['commentaire']);
            });
        }
    });

    //ON CHANGE SELECT VILLE
    $('body').on('change', 'select[name="ville"]', function () {
        var ville_id = $('select[name="ville"]').val();
        if (ville_id !== '') {
            $.post(site_url + "expediteurs/get_quartiers_by_villeid/" + ville_id, function (response1) {
                var quartiers = jQuery.parseJSON(response1);
                if (quartiers !== null) {
                    $('select[name="quartier"]').html('<option value=""></option>');
                    for (var i = 0; i < quartiers.length; i++) {
                        $('select[name="quartier"]').append('<option value="' + quartiers[i].id + '">' + quartiers[i].name + '</option>');
                    }
                    $('select[name="quartier"]').selectpicker('refresh');
                }
            });
        }
    });

});

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
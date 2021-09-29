$(document).ready(function () {
    // On submit facture form
    $('#facture-form').on('submit', function () {
        $('button[id="submit"]').attr('disabled', true);
    });
    // Validate facture form
    _validate_form($('#facture-form'), {
        expediteurs: 'required',
        types: 'required'
    });
    // On change select clients
    $('body').on('change', 'select[name="expediteurs"], select[name="types"]', function () {
        var expediteur = $('select[name="expediteurs"]').selectpicker('val');
        var type = $('select[name="types"]').selectpicker('val');
        $('input[name="id_expediteur"]').val('');
        $('input[name="type"]').val('');
        $('button[id="submit"]').attr('disabled', false);

        if (expediteur !== '' && type !== '') {
            initDataTable_1('.table-colis-facture', admin_url + 'factures/init_colis_facture/' + expediteur + '/' + type, 'colis-facture', [6, 7], [6, 7]);
            $('input[name="id_expediteur"]').val(expediteur);
            $('input[name="type"]').val(type);
            $('select[name="expediteurs"]').attr('disabled', 'disabled');
            $('select[name="types"]').attr('disabled', 'disabled');
        } else if (expediteur === '') {
            $('select[name="types"]').selectpicker('val', '');
            alert('Choississez un expéditeur.');
        } else if (type === '') {
            alert('Choississez un type.');
        }
    });
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
    //if check new colis
    $('body').on('change', '.product_checked', function () {
        var nbrColisSelected = $('input[id="nbr_colis_selected"]').val();
        var alertColisSelected = $('input[id="alert_colis_selected"]').val();
        var product_id = $(this).val();
        if ($(this).prop('checked') === true) {
            if (nbrColisSelected < 50) {
                if ($('input[id="checked_product_' + product_id + '"]').length === 0) {
                    $('#checked-products').append('<input id="checked_product_' + product_id + '" type="hidden" name="checked_products[]" value="' + product_id + '">');
                    nbrColisSelected++;
                }
            } else {
                if ($('#product_checked_' + product_id).prop('checked') === true) {
                    $('#product_checked_' + product_id).prop('checked', false);
                }
                if (parseInt(alertColisSelected) === 0) {
                    alert_float('warning', 'Maximum 50 colis par facture');
                    $('input[id="alert_colis_selected"]').val(1);
                }
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
    // Is edit
    var isedit = $('input[name="isedit"]').val();
    if (typeof (isedit) !== 'undefined') {
        var facture_id = $('input[name="facture_id"]').val();
        var id_expediteur = $('input[name="id_expediteur"]').val();
        var type = $('input[name="type"]').val();
        initDataTable_1('.table-colis-facture', admin_url + 'factures/init_colis_facture/' + id_expediteur + '/' + type, 'colis-facture', [0, 6, 7], [0, 6, 7]);
        initDataTable_1('.table-historique-colis-factures', admin_url + 'factures/init_historique_colis_facture/' + facture_id + '/' + type, 'historique-colis-factures', [0, 6, 7], [0, 6, 7]);
    }
    // On change discount type
    $('body').on('change', '#discount-type input[type="radio"]', function () {
        if (this.value === 'percentage') {
            $('#discount-addon').html('%');
        } else {
            $('#discount-addon').html('Dhs');
        }
    });
});

function init_facture() {
    var type = $('select[name="types"]').selectpicker('val');
    window.location.href = admin_url + 'factures/facture/false/' + type;
}
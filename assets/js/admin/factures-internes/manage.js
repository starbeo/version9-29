$(document).ready(function () {
    // Init data table for factures internes
    var headers_factures_internes = $('.table-factures-internes').find('th');
    var not_sortable_factures_internes = (headers_factures_internes.length - 1);
    initDataTable('.table-factures-internes', window.location.href, 'Factures Internes', [not_sortable_factures_internes], [not_sortable_factures_internes]);
    var hidden_columns_factures_internes = [0];
    $.each(hidden_columns_factures_internes, function (i, val) {
        var column_factures_internes = $('.table-factures-internes').DataTable().column(val);
        column_factures_internes.visible(false);
    });
    // Init single facture if id exist in url
    init_facture_interne();
});
// Init single facture interne
function init_facture_interne(id) {
    var _facture_interne_id = $('input[name="facture_interne_id"]').val();
    // Check if facture interne passed from url
    if (_facture_interne_id !== '') {
        id = _facture_interne_id;
        // Clear the current facture interne value in case user click on the left sidebar invoices
        $('input[name="facture_interne_id"]').val('');
    } else {
        if (typeof (id) === 'undefined' || id === '') {
            return;
        }
    }
    if (!$('body').hasClass('small-table')) {
        toggle_small_view('.table-factures-internes', '#facture_interne');
    }
    $('#facture_interne').load(admin_url + 'factures_internes/get_facture_interne_data_ajax/' + id);

    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $('#facture_interne').offset().top + 150
        }, 600);
    } else {
        $('html, body').animate({
            scrollTop: $('#facture_interne').offset().top
        }, 400);
    }
}
// Record payment function
function record_payment_facture_interne(id) {
    if (typeof (id) === 'undefined' || id === '') {
        return;
    }
    $('#facture_interne').load(admin_url + 'factures_internes/record_facture_interne_payment_ajax/' + id);
}
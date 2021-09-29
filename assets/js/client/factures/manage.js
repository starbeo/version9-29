$(document).ready(function () {
    // Init data table for factures
    var headers_factures = $('.table-factures').find('th');
    var not_sortable_factures = (headers_factures.length - 1);
    var FacturesServerParams = {
        "f-type": "[name='f-type']",
        "f-statut": "[name='f-statut']",
        "f-date-created-start": "[name='f-date-created-start']",
        "f-date-created-end": "[name='f-date-created-end']"
    };
    initDataTable('.table-factures', window.location.href, 'Factures', [not_sortable_factures], [not_sortable_factures], FacturesServerParams);
    // Init single facture if id exist in url
    init_facture();
});

// Init single facture
function init_facture(id) {
    var _invoiceid = $('input[name="invoiceid"]').val();
    // Check if invoice passed from url
    if (_invoiceid !== '') {
        id = _invoiceid;
        // Clear the current invoice value in case user click on the left sidebar invoices
        $('input[name="invoiceid"]').val('');
    } else {
        if (typeof (id) === 'undefined' || id === '') {
            return;
        }
    }
    if (!$('body').hasClass('small-table')) {
        toggle_small_view('.table-factures', '#facture');
    }
    $('#facture').load(client_url + 'factures/get_facture_data_ajax/' + id);

    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $('#facture').offset().top + 150
        }, 600);
    } else {
        $('html, body').animate({
            scrollTop: $('#facture').offset().top
        }, 400);
    }
}
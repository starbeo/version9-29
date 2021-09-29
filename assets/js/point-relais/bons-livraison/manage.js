$(document).ready(function () {
    // Init data table bon livraison
    var headers_delivery_notes = $('.table-delivery-notes').find('th');
    var not_sortable_delivery_notes = (headers_delivery_notes.length - 1);
    var BonsLivraisonServerParams = {
        "f-point-relai": "[name='f-point-relai']",
        "f-date-created": "[name='f-date-created']"
    };
    initDataTable('.table-delivery-notes', window.location.href, 'Bons livraison', [not_sortable_delivery_notes], [not_sortable_delivery_notes], BonsLivraisonServerParams);
    var hidden_columns = [0];
    $.each(hidden_columns, function (i, val) {
        var column_status = $('.table-delivery-notes').DataTable().column(val);
        column_status.visible(false);
    });
});
$(document).ready(function () {
    // Init data table delivery notes
    var headers_delivery_notes = $('.table-delivery-notes').find('th');
    var not_sortable_delivery_notes = (headers_delivery_notes.length - 1);
    var DeliveryNotesServerParams = {
        "f-date-created-start": "[name='f-date-created-start']",
        "f-date-created-end": "[name='f-date-created-end']"
    };
    initDataTable('.table-delivery-notes', window.location.href, 'Bons Livraison', [not_sortable_delivery_notes], [not_sortable_delivery_notes], DeliveryNotesServerParams);
});
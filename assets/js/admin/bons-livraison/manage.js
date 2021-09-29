$(document).ready(function () {
    // Init data table bon livraison
    var headers_delivery_notes = $('.table-delivery-notes').find('th');
    var not_sortable_delivery_notes = (headers_delivery_notes.length - 1);
    var BonsLivraisonServerParams = {
        "f-type-livraison": "[name='f-type-livraison']",
        "f-livreur": "[name='f-livreur']",
        "f-point-relai": "[name='f-point-relai']",
        "f-utilisateur": "[name='f-utilisateur']",
        "f-date-created": "[name='f-date-created']"
    };
    initDataTable('.table-delivery-notes', window.location.href, 'Bons livraison', [not_sortable_delivery_notes], [not_sortable_delivery_notes], BonsLivraisonServerParams);
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
        $('.table-delivery-notes').DataTable().ajax.reload();
        $('#filtre-table').addClass('display-none');
    });
    // Reset filter
    $('body').on('click', '#filtre-reset', function () {
        $('#filtre-table select').selectpicker('val', '');
        $('#filtre-table input').val('');
        $('.table-delivery-notes').DataTable().ajax.reload();
    });
    // On click checkbox all etat
    $('body').on('change', '#checkbox-all-bons-livraison', function () {
        if ($('#checkbox-all-bons-livraison').is(':checked') === true) {
            $('.checkbox-bon-livraison').prop('checked', true);
        } else {
            $('.checkbox-bon-livraison').prop('checked', false);
        }
    });
    // On click
    $('body').on('click', '.paginate_button a', function () {
        $('#checkbox-all-bons-livraison').prop('checked', false);
    });
});
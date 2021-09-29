$(document).ready(function () {
    // Init data table appels livreurs
    var AppelsLivreursServerParams = {
        "f-livreur": "[name='f-livreur']",
        "f-client": "[name='f-client']",
        "f-date-created": "[name='f-date-created']"
    };
    initDataTable('.table-appels-livreurs', window.location.href, 'Appels', 'undefined', 'undefined', AppelsLivreursServerParams);
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
        $('.table-appels-livreurs').DataTable().ajax.reload();
        $('#filtre-table').addClass('display-none');
    });
    // Reset filter
    $('body').on('click', '#filtre-reset', function () {
        $('#filtre-table select').selectpicker('val', '');
        $('#filtre-table input').val('');
        $('.table-appels-livreurs').DataTable().ajax.reload();
    });
});
$(document).ready(function () {
    // Init data table contrats
    var headers_contrats = $('.table-contrats').find('th');
    var not_sortable_contrats = (headers_contrats.length - 1);
    var ContratsServerParams = {
        "f-client": "[name='f-client']",
        "f-date-start": "[name='f-date-start']",
        "f-date-end": "[name='f-date-end']",
        "f-date-created": "[name='f-date-created']"
    };
    initDataTable('.table-contrats', window.location.href, 'Contrats', [not_sortable_contrats], [not_sortable_contrats], ContratsServerParams);
    // On click button filter
    $('body').on('click', '.btn-filtre', function () {
        if ($('#filtre-table').hasClass('display-none')) {
            $('#filtre-table').removeClass('display-none');
            $('#statistique-contracts').addClass('display-none');
        } else {
            $('#filtre-table').addClass('display-none');
        }
    });
    // On submit filter
    $('body').on('click', '#filtre-submit', function () {
        $('.table-contracs').DataTable().ajax.reload();
        $('#filtre-table').addClass('display-none');
    });
    // Reset filter
    $('body').on('click', '#filtre-reset', function () {
        $('#filtre-table select').selectpicker('val', '');
        $('#filtre-table input').val('');
        $('.table-contracs').DataTable().ajax.reload();
    });
    // On click button statistique contracts
    $('body').on('click', '.btn-statistique-contracts', function () {
        if ($('#statistique-contracts').hasClass('display-none')) {
            $('#statistique-contracts').removeClass('display-none');
            $('#filtre-table').addClass('display-none');
        } else {
            $('#statistique-contracts').addClass('display-none');
        }
    });
});

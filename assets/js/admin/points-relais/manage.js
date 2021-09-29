$(document).ready(function () {
    // Init data table points relais
    var headers_points_relais = $('.table-points-relais').find('th');
    var not_sortable_points_relais = (headers_points_relais.length - 1);
    var PointsRelaisServerParams = {
        "f-ville": "[name='f-ville']",
        "f-date-created": "[name='f-date-created']"
    };
    initDataTable('.table-points-relais', window.location.href, 'Points relais', [not_sortable_points_relais], [not_sortable_points_relais], PointsRelaisServerParams);
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
        $('.table-points-relais').DataTable().ajax.reload();
        $('#filtre-table').addClass('display-none');
    });
    // Reset filter
    $('body').on('click', '#filtre-reset', function () {
        $('#filtre-table select').selectpicker('val', '');
        $('#filtre-table input').val('');
        $('.table-points-relais').DataTable().ajax.reload();
    });
});
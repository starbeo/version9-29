$(document).ready(function () {
    // Init data table for expediteurs
    var headers_expediteurs = $('.table-expediteurs').find('th');
    var not_sortable_expediteurs = (headers_expediteurs.length - 1);
    initDataTable('.table-expediteurs', window.location.href, 'expediteurs', [not_sortable_expediteurs], [not_sortable_expediteurs]);
    var hidden_columns_expediteurs = [0];
    $.each(hidden_columns_expediteurs, function (i, val) {
        var column_expediteurs = $('.table-expediteurs').DataTable().column(val);
        column_expediteurs.visible(false);
    });
});
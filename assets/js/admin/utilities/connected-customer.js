$(document).ready(function () {
    // Init data table for connected customer
    initDataTable('.table-connected-customer', window.location.href, 'connected customer');
    var hidden_columns = [0];
    $.each(hidden_columns, function (i, val) {
        var column_activity_log = $('.table-connected-customer').DataTable().column(val);
        column_activity_log.visible(false);
    });
});

$(document).ready(function () {
    // Init data table for activities log
    var ActivityLogServerParams = {
        "f-date-created": "[name='f-date-created']"
    };
    initDataTable('.table-activities-log', window.location.href, 'Journal d\'activité', 'undefined', 'undefined', ActivityLogServerParams);
    var hidden_columns = [0];
    $.each(hidden_columns, function (i, val) {
        var column_activity_log = $('.table-activities-log').DataTable().column(val);
        column_activity_log.visible(false);
    });
});

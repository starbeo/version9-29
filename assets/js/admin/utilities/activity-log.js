$(document).ready(function () {
    // Init data table for activities log
    var ActivityLogServerParams = {
        "custom_view": "[name='custom_view']"
    };
    initDataTable('.table-activity-log', window.location.href, 'activity log', 'undefined', 'undefined', ActivityLogServerParams);
    var hidden_columns = [0];
    $.each(hidden_columns, function (i, val) {
        var column_activity_log = $('.table-activity-log').DataTable().column(val);
        column_activity_log.visible(false);
    });
});

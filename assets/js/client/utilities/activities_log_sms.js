$(document).ready(function () {
    // Init data table activities log
    var ActivitiesLogSmsServerParams = {
        "f-date-created-start": "[name='f-date-created-start']",
        "f-date-created-end": "[name='f-date-created-end']"
    };
    initDataTable('.table-activities-log-sms', window.location.href, 'Journale d\'activités SMS', 'undefined', 'undefined', ActivitiesLogSmsServerParams);
});
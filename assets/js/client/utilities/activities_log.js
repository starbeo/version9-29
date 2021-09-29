$(document).ready(function () {
    // Init data table activities log
    var ActivitiesLogServerParams = {
        "f-date-created-start": "[name='f-date-created-start']",
        "f-date-created-end": "[name='f-date-created-end']"
    };
    initDataTable('.table-activities-log', window.location.href, 'Journale d\'activité', 'undefined', 'undefined', ActivitiesLogServerParams);
});
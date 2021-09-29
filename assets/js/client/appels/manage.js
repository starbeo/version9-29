$(document).ready(function () {
    // Init data table appels livreurs
    var AppelsLivreursServerParams = {
        "f-date-created-start": "[name='f-date-created-start']",
        "f-date-created-end": "[name='f-date-created-end']"
    };
    initDataTable('.table-appels-livreurs', window.location.href, 'Appels', 'undefined', 'undefined', AppelsLivreursServerParams);
});
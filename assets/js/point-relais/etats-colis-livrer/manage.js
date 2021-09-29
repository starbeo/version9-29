$(document).ready(function () {
    // Init data table etat colis livrer
    var headers_etat_colis_livrer = $('.table-etats-colis-livrer').find('th');
    var not_sortable_etat_colis_livrer = (headers_etat_colis_livrer.length - 1);
    var EtatColisLivrerServerParams = {
        "f-date-created": "[name='f-date-created']"
    };
    initDataTable('.table-etats-colis-livrer', window.location.href, 'Etats Colis Livrer', [not_sortable_etat_colis_livrer], [not_sortable_etat_colis_livrer], EtatColisLivrerServerParams);
    var hidden_columns = [0];
    $.each(hidden_columns, function (i, val) {
        var column_status = $('.table-etats-colis-livrer').DataTable().column(val);
        column_status.visible(false);
    });
});

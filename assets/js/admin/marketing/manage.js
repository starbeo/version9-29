$(document).ready(function () {
    // Init data table marketing
    var headers_marketing = $('.table-marketing').find('th');
    var not_sortable_marketing = (headers_marketing.length - 1);
    initDataTable('.table-marketing', window.location.href, 'Marketing', [not_sortable_marketing], [not_sortable_marketing]);
    // Show historique marketing
    $('#historiques').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var marketingId = $(invoker).data('marketing-id');
        $('#historiques input[name="f-marketing-id"]').val(marketingId);

        var count = $('#historiques input[name="historique-count"]').val();
        if (count === '0') {
            $('#historiques input[name="historique-count"]').val(1);
            //Init Data Table Historiques marketing
            var HistoriquesMarketingServerParams = {
                "f-marketing-id": "[name='f-marketing-id']"
            };
            initDataTable('.table-historiques-marketing', admin_url + 'marketing/init_historique', 'Historique marketing', 'undefined', 'undefined', HistoriquesMarketingServerParams);
        } else {
            $('.table-historiques-marketing').DataTable().ajax.reload();
        }
    });
});

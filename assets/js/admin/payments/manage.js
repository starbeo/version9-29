$(document).ready(function () {
    // Init data table payments
    var Payments_ServerParams = {
        'custom_view': '[name="custom_view"]'
    };
    initDataTable('.table-payments', window.location.href, 'payments', [4], [4], Payments_ServerParams);
});

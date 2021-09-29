$(document).ready(function () {
    // Init data table for staff
    var headers_staff = $('.table-staff').find('th');
    var not_sortable_staff = (headers_staff.length - 1);
    initDataTable('.table-staff', window.location.href, 'staff members', [not_sortable_staff], [not_sortable_staff]);
    var hidden_columns_staff = [0];
    $.each(hidden_columns_staff, function (i, val) {
        var column_staff = $('.table-staff').DataTable().column(val);
        column_staff.visible(false);
    });
});
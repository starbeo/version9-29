$(document).ready(function () {
    // Init Table Sliders
    var headers_sliders = $('.table-sliders').find('th');
    var not_sortable_sliders = (headers_sliders.length - 1);
    initDataTable('.table-sliders', window.location.href, 'Sliders', [not_sortable_sliders], [not_sortable_sliders]);
});


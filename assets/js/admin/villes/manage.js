$(document).ready(function () {
    // Init data table villes
    var headers_villes = $('.table-villes').find('th');
    var not_sortable_villes = (headers_villes.length - 1);
    var villesServerParams = {};
    initDataTable('.table-villes', window.location.href, 'Villes', [not_sortable_villes], [not_sortable_villes], villesServerParams, [0, 'ASC']);
    // Show modal add & edit city
    $('#city_modal').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#city_modal .add-title').removeClass('hide');
        $('#city_modal .edit-title').addClass('hide');
        $('#city_modal input').val('');
        $('#city_modal select').selectpicker('val', '');
        $('#city_modal button[id="submit-form-city"]').attr('disabled', false);
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            var fraisSpecial = $(invoker).data('frais-special');
            var delai = $(invoker).data('delai');
            $('#city_modal input[name="id"]').val(id);
            $('#city_modal .add-title').addClass('hide');
            $('#city_modal .edit-title').removeClass('hide');
            $('#city_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
            if ($('#city_modal select[name="category_shipping_cost"]').length > 0) {
                var categoryShippingCost = $(invoker).data('category-shipping-cost');
                $('#city_modal select[name="category_shipping_cost"]').selectpicker('val', categoryShippingCost);
            }
            $('#city_modal input[name="frais_special"]').val(fraisSpecial);
            $('#city_modal input[name="delai"]').val(delai);
        }
    });
    // Show modal add & edit shipping cost to cities
    $('#shipping_cost_to_cities_modal').on('show.bs.modal', function () {
        $('#shipping_cost_to_cities_modal select').selectpicker('refresh');
        $('#shipping_cost_to_cities_modal input').val('');
        $('#shipping_cost_to_cities_modal button[id="submit-form-shipping-cost-to-cities"]').attr('disabled', false);
    });
    // Validate form city
    _validate_form($('#form-city'), {
        name: 'required'
    }, manage_ville);
    // Validate form affectation shipping cost to cities
    _validate_form($('#form-affectation-shipping-cost-to-cities'), {
        cities: 'required',
        shipping_cost: 'required'
    }, manage_affectation_shipping_cost_to_cities);
});
// Manage ville
function manage_ville(form) {
    $('#city_modal button[id="submit-form-city"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        if (response.success === true) {
            $('.table-villes').DataTable().ajax.reload();
            alert_float('success', response.message);
        }
        $('#city_modal').modal('hide');
    });

    return false;
}
// Manage affectation shipping cost to cities
function manage_affectation_shipping_cost_to_cities(form) {
    $('#shipping_cost_to_cities_modal button[id="submit-form-shipping-cost-to-cities"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        if (response.success === true) {
            $('.table-villes').DataTable().ajax.reload();
            alert_float('success', response.message);
        }
        $('#shipping_cost_to_cities_modal').modal('hide');
    });

    return false;
}
$(document).ready(function () {
    //Init Data Table Demandes
    var headers_demandes = $('.table-demandes').find('th');
    var not_sortable_demandes = (headers_demandes.length - 1);
    initDataTable('.table-demandes', window.location.href, 'demandes', [not_sortable_demandes], [not_sortable_demandes]);
    //Validate form demande
    _validate_form($('#demande-form'), {
        object: 'required',
        priorite: 'required',
        message: 'required'
    });
    //If relation id not empty
    var relId = $('input[name="hidden_rel_id"]').val();
    if (relId !== '') {
        onChangeSelectObject(relId);
    }
    //On change object
    $('body').on('change', 'select[name="object"]', function () {
        onChangeSelectObject(relId);
    });
    //On click button send_note
    $('body').on('click', '#send_note', function () {
        var demandeId = document.getElementById('demande_id').value;
        var rating = document.getElementById('rating').value;
        if ($.isNumeric(demandeId) && $.isNumeric(rating)) {
            $.post(site_url + "expediteurs/add_rating_demande", {demande_id: demandeId, rating: rating}, function (response) {
                var response = $.parseJSON(response);
                alert_float(response.type, response.message);
            });
        }
    });
    //Init rating
    var defaultRating = $('input[name="rating"]').val();
    if ($.isNumeric(defaultRating) && parseInt(defaultRating) !== 0) {
        changeRating("rating_" + defaultRating);
    }
});
//On change select object rating
function onChangeSelectObject(relId) {
    var object = $('select[name="object"]').selectpicker('val');
    $('select[name="rel_id"]').html('<option value=""></option>');
    // Get service
    $('input[name="department"]').val('');
    if (!$('#bloc-input-department').hasClass('display-none')) {
        $('#bloc-input-department').addClass('display-none');
    }
    if($.isNumeric(object)) {
        $.post(site_url + "expediteurs/get_department_by_object", {object_id: object}, function (response) {
            var response = $.parseJSON(response);
            if (response.success === true) {
                $('input[name="department"]').val(response.department_name);
                if ($('#bloc-input-department').hasClass('display-none')) {
                    $('#bloc-input-department').removeClass('display-none');
                }
            }
        });
    }
    // Get relation
    if (parseInt(object) === 1 || parseInt(object) === 4 || parseInt(object) === 14) {
        if ($('#relation').hasClass('display-none')) {
            $('#relation').removeClass('display-none');
        }
        $.post(site_url + "expediteurs/get_relations_demande", {object_id: object}, function (response) {
            var list = $.parseJSON(response);
            if (list !== null) {
                for (var i = 0; i < list.length; i++) {
                    var selected = '';
                    if (parseInt(relId) === parseInt(list[i].id)) {
                        selected = 'selected';
                    }
                    $('select[name="rel_id"]').append('<option value="' + list[i].id + '" ' + selected + '>' + list[i].name + '</option>');
                }
                $('select[name="rel_id"]').selectpicker('refresh');
            }
        });
    } else {
        $('select[name="rel_id"]').selectpicker('refresh');
        if (!$('#relation').hasClass('display-none')) {
            $('#relation').addClass('display-none');
        }
    }
}
//Change rating
function changeRating(id)
{
    var ab = document.getElementById(id + '_hidden').value;
    document.getElementById("rating").value = ab;

    for (var i = ab; i >= 1; i--)
    {
        document.getElementById("rating_" + i).src = site_url + "assets/images/defaults/rating-star2.png";
    }
    var id = parseInt(ab) + 1;
    for (var j = id; j <= 5; j++)
    {
        document.getElementById("rating_" + j).src = site_url + "assets/images/defaults/rating-star1.png";
    }
}
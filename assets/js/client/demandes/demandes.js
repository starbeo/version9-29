$(document).ready(function () {


    //Validate form demande
    _validate_form($('#demande-form'), {
        object: 'required',
        priorite: 'required',
        message: 'required'
    });
    //If relation id not empty
    if ($('input[name="hidden_rel_id[]"]').length === 0 ){
        var relId = $('input[name="hidden_rel_id"]').val();
        if (relId !== '') {
            onChangeSelectObject(relId);
        }
    }else{
        $('input[name="hidden_rel_id[]"]').each(function () {
            var relId = this.value;
            console.log(relId)
            if (relId !== '') {
                onChangeSelectObject(relId);
            }
        });
    }


    $('body').on('change', '#relation select[name="rel_id"]', function() {
        var clients = $('#relation select[name="rel_id"]').selectpicker('val');
        $('#relation .bloc-relation-selected').html('');
        console.log(clients);
        if(clients.length > 0) {
            for(var i=0; i < clients.length; i++) {
                var clientValue = clients[i];
                var clientText = $("#relation option[value='"+clientValue+"']").text();
                if($('#relation-added-' + clientValue).length === 0) {
                    $('#relation .selectpicker.bs-select-hidden').append('<label id="relation-added-' + clientValue + '" class="label label-default lineh30 mright5">' + clientText + '"' + '<input type="hidden" name="rels_id[]" value="' + clientValue + '"></label>');

                }
            }
        }
    });
    //On change type
    $('body').on('change', 'select[name="type"]', function () {
        onChangeSelectType();
    });

    onChangeSelectType('demand');
    $('select[name="type"]').addClass('display-none');
    $('label[for="type"]').addClass('display-none');
    $('button[data-id="type"]').addClass('display-none');
    //$('select[name="priorite"]').hide();



    $('body').on('change', 'select[name="object"]', function () {
      onChangeSelectObject2();
      //  console.log('working');
    });
    //On change client
    $('body').on('change', 'select[name="client_id"]', function () {
        var clientId = $('select[name="client_id"]').selectpicker('val');
        if ($.isNumeric(clientId)) {
            $('select[name="object"]').selectpicker('val', '');
            $('select[name="rel_id"]').html('<option value=""></option>');
            $('select[name="rel_id"]').selectpicker('refresh');
        }
    });
    //On change object
    $('body').on('change', 'select[name="object"]', function () {
        onChangeSelectObject(relId);
    });
    //On click button send_note
    $('body').on('click', '#send_note', function () {
        var demandeId = document.getElementById('demande_id').value;
        var rating = document.getElementById('rating').value;
        if ($.isNumeric(demandeId) && $.isNumeric(rating)) {
            $.post(client_url + "demandes/add_rating_demande", {demande_id: demandeId, rating: rating}, function (response) {
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

//On change select type
function onChangeSelectType(type) {
    if (typeof (type) === 'undefined') {
        var type = $('select[name="type"]').selectpicker('val');
    }
    if (type !== '') {
        $.post(client_url + "demandes/get_object_by_type", {type: type}, function (response) {
            var response = $.parseJSON(response);
            if (response.success === true) {
                list = response.objets;
                console.log(list)
                $('select[name="object"]').html('<option value=""></option>');
                for (var i = 0; i < list.length; i++) {
                    var selected = '';
                    if (parseInt(type) === parseInt(list[i].id)) {
                        selected = 'selected';
                    }
                    $('select[name="object"]').append('<option value="' + list[i].id + '" ' + selected + '>' + list[i].name + '</option>');
                }
                $('select[name="object"]').selectpicker('refresh');
            }
        });
    }
}




function onChangeSelectObject2(object) {
    if (typeof (object) === 'undefined') {
        var object = $('select[name="object"]').selectpicker('val');
        console.log(object);
        if (typeof (object) === 'undefined') {
            var object = $('select[name="object"]').selectpicker('val');
            console.log(object);
            changetype(object)
        }
    }
    if (object !== '') {
        $.post(client_url + "demandes/get_object_by_priority", {type: object}, function (response) {
            var response = $.parseJSON(response);
            console.log(response)
            if (response.success === true) {
                list = response.objets;
                $('select[name="priorite"]').html('<option value=""></option>');
                for (var i = 1; i < list.length; i++) {
                    $('select[name="priorite"]').append('<option value="' + list[i-1] + '" selected>' + list[i]+ '</option>');
                }
                $('select[name="priorite"]').selectpicker('refresh');
            }
        });
    }
}



//On change select object rating
function onChangeSelectObject(relId) {
    var object = $('select[name="object"]').selectpicker('val');
    // Get service
    $('input[name="department"]').val('');
    if (!$('#bloc-input-department').hasClass('display-none')) {
        $('#bloc-input-department').addClass('display-none');
    }
    changetype(object);

    $('select[name="rel_id"]').html('<option value=""></option>');
    if ($.isNumeric(object)) {
        // Get department by object
        $.post(client_url + "demandes/get_department_by_object", {object_id: object}, function (response) {
            var response = $.parseJSON(response);
            if (response.success === true) {
                $('input[name="department"]').val(response.department_name);
                if ($('#bloc-input-department').hasClass('display-none')) {
                    $('#bloc-input-department').removeClass('display-none');
                }
            }
        });
        // Get relation
        $.post(client_url + "demandes/get_relations_demande", {object_id: object}, function (response) {
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
                if ($('#relation').hasClass('display-none')) {
                    $('#relation').removeClass('display-none');
                }
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

function changetype(object)
{
    let dm = ['28','25','24','22','20','19','18','17','15','14','13','8','7','6'];
    //   let objectselected = $('select[name="object"]').selectpicker('val');

    console.log(dm.includes(object))
    if (dm.includes(object))
    {
        $('select[name="type"]').empty()
        $('select[name="type"]').append('<option value="demande" selected>Demande</option>');
        $('select[name="type"]').selectpicker('refresh');

    }
    else
    {
        $('select[name="type"]').empty()
        $('select[name="type"]').append('<option value="reclamation" selected>reclamation</option>');
        $('select[name="type"]').selectpicker('refresh');

    }
    $('select[name="type"]').removeClass('display-none');
    $('label[for="type"]').removeClass('display-none');
    $('button[data-id="type"]').removeClass('display-none');
    $('.bootstrap-select').first().removeClass('display-none');
}

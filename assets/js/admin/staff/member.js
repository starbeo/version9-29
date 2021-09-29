$(document).ready(function () {
    var livreurId = $('input[id="livreurid"]').val();
    console.log(livreurId)
// let comissionval =   removePointRelais(livreurId)
    var staffId = $('input[id="staff_id"]').val();
    var staffTypePointRelais = $('input[id="staff_point_relais"]').val();
    // Init roles permissions
    init_roles_permissions();
    if (typeof ($('input[name="isedit"]').val()) === 'undefined') {
        var typeStaff = $('input[id="type_staff"]').val();
        if (parseInt(typeStaff) === 0 || typeStaff === '') {
            $('select[name="role"]').selectpicker('val', 1);
            show_onglet_permissions();
        } else if (parseInt(typeStaff) === 1) {
            $('select[name="role"]').selectpicker('val', '');
            $('.roles').find('input').prop('checked', true);
            $('.roles').find('input').prop('disabled', true);
            hide_onglet_permissions();
        } else if (parseInt(typeStaff) === 2) {
            $('select[name="role"]').selectpicker('val', 2);
            show_onglet_permissions();
        } else {
            $('select[name="role"]').selectpicker('val', '');
            $('.roles').find('input').prop('checked', false);
            $('.roles').find('input').prop('disabled', false);
            //Show select point relais
            if (parseInt(typeStaff) === 4) {
                hide_onglet_permissions();
                if ($('#bloc-onglet-point-relais').hasClass('display-none')) {
                    $('#bloc-onglet-point-relais').removeClass('display-none');
                    $('.bloc-point-relais').removeClass('display-none');
                } else {
                    $('#bloc-onglet-point-relais').addClass('display-none');
                    $('.bloc-point-relais').addClass('display-none');
                }
            } else {
                show_onglet_permissions();
            }
        }
        $('select[name="role"]').change();
        $('input[id="_type"]').val(typeStaff);
    }
    // On change role
    $('select[name="role"]').on('change', function () {
        var roleid = $(this).val();
        if (roleid !== '') {
            init_roles_permissions(roleid, true);
        } else {
            $('.roles').find('input').prop('disabled', false);
            $('.roles').find('input').prop('checked', false);
        }
    });
    // On change input administrator
    $('input[name="administrator"]').on('change', function () {
        var valeur = $(this).val();
        $('input[id="_type"]').val(valeur);
        if (parseInt(valeur) === 1) {
            $('select[name="role"]').selectpicker('val', '');
            $('.roles').find('input').prop('checked', true);
            $('.roles').find('input').prop('disabled', true);
            hide_onglet_permissions();
        } else if (parseInt(valeur) === 0) {
            $('select[name="role"]').selectpicker('val', 1);
            $('select[name="role"]').change();
            show_onglet_permissions();
        } else if (parseInt(valeur) === 2) {
            $('select[name="role"]').selectpicker('val', 2);
            $('select[name="role"]').change();
            show_onglet_permissions();
        } else {
            $('select[name="role"]').selectpicker('val', '');
            $('.roles').find('input').prop('checked', false);
            $('.roles').find('input').prop('disabled', false);
            //Show select point relais
            if (parseInt(valeur) === 4) {
                hide_onglet_permissions();
                if ($('#bloc-onglet-point-relais').hasClass('display-none')) {
                    $('#bloc-onglet-point-relais').removeClass('display-none');
                    $('.bloc-point-relais').removeClass('display-none');
                } else {
                    $('#bloc-onglet-point-relais').addClass('display-none');
                    $('.bloc-point-relais').addClass('display-none');
                }
            } else {
                show_onglet_permissions();
            }
        }
    });
    // On submit form staff
   // $('#submit').on('click', function (e) {

    //})
    $('.staff-form').on('submit', function (e) {
        //$('button[id="submit"]').attr('disabled', true);

        $('.roles').find('input').prop('disabled', false);
        if (parseInt($('input[id="_type"]').val()) !== 1 && parseInt($('input[id="_type"]').val()) !== 4 && $('select[name="role"]').selectpicker('val') === '') {
            alert_float('warning', 'Rôle obligatoire dans l\'onlget "Permissions des modules" !!');
        }
     //   console.log(comissionval)
if (livreurId != null){
    $.post(admin_url + "staff/checkcomission", {type: livreurId}, function (response) {
        var response = $.parseJSON(response);
        comissionadd(response.success,e)

    });
}

  
    });
    _validate_form($('.staff-form'), {
        administrator: 'required',
        firstname: 'required',
        lastname: 'required',
        city: 'required',
        email: {
            required: true,
            email: true,
            remote: {
                url: site_url + "admin/misc/staff_email_exists",
                type: 'post',
                data: {
                    email: function () {
                        return $('input[name="email"]').val();
                    },
                    memberid: function () {
                        return $('input[name="memberid"]').val();
                    }
                }
            }
        },
        password: {
            required: {
                depends: function () {
                    return (parseInt($('input[name="isedit"]').length) === 0) ? true : false;
                }
            }
        },
        role: {
            required: {
                depends: function () {
                    return (parseInt($('input[id="_type"]').val()) !== 1 && parseInt($('input[id="_type"]').val()) !== 4 && $('select[name="role"]').selectpicker('val') === '') ? true : false;
                }
            }
        }
    });

    if (typeof (staffTypePointRelais) !== 'undefined' && $.isNumeric(staffId)) {
        // Init data table points relais
        var headers_points_relais = $('.table-points-relais').find('th');
        var not_sortable_points_relais = (headers_points_relais.length - 1);
        initDataTable('.table-points-relais', admin_url + 'staff/init_points_relais/' + staffId, 'Points Relais', [not_sortable_points_relais], [not_sortable_points_relais]);
        // Validate form point relais
        _validate_form($('#form-point-relais'), {
            point_relais_id: 'required'
        }, manage_point_relais);
        // Show modal add & edit point relais
        $('#point_relais_modal').on('show.bs.modal', function (e) {
            var invoker = $(e.relatedTarget);
            var id = $(invoker).data('id');
            $('#point_relais_modal .add-title').removeClass('hide');
            $('#point_relais_modal .edit-title').addClass('hide');
            $('#point_relais_modal select').selectpicker('val', '');
            $('#point_relais_modal input').val('');
            $('#point_relais_modal input[name="staff_id"]').val(staffId);
            // is from the edit button
            if (typeof (id) !== 'undefined') {
                var pointRelaisId = $(invoker).data('point-relais-id');
                $('#point_relais_modal input[name="id"]').val(id);
                $('#point_relais_modal .add-title').addClass('hide');
                $('#point_relais_modal .edit-title').removeClass('hide');
                $('#point_relais_modal select[name="point_relais_id"]').selectpicker('val', pointRelaisId);
            }
        });
    }
    if (typeof (livreurId) !== 'undefined' && $.isNumeric(livreurId)) {
        //Init Data Table Colis
        var ColisServerParams = {
            "custom_view": "[name='custom_view']",
            "etat": "[name='etat']"
        };
        initDataTable('.table-colis-delivery-men', admin_url + 'staff/init_colis_livreur/' + livreurId, 'Colis', 'undefined', 'undefined', ColisServerParams);
        //Hide Column
        var hidden_columns = [0];
        $.each(hidden_columns, function (i, val) {
            var column_status = $('.table-colis-livreur').DataTable().column(val);
            column_status.visible(false);
        });

        // Init data table colis en attente
        initDataTable('.table-colis-en-attente-delivery-men', admin_url + 'staff/init_colis_en_attente_livreur/' + livreurId, 'Colis en attente');

        // Init data table bons livraison
        initDataTable('.table-delivery-notes-delivery-men', admin_url + 'staff/init_bons_livraison_livreur/' + livreurId, 'Bons livraison');

        // Init data table etats colis livrer
        initDataTable('.table-etat-colis-livrer-delivery-men', admin_url + 'staff/init_etat_colis_livrer_livreur/' + livreurId, 'Etat colis livrer');

        // Init data table factures
        initDataTable('.table-factures-delivery-men', admin_url + 'staff/init_factures_livreur/' + livreurId, 'Factures');

        //Init Data Table Activity log
        initDataTable('.table-activity-log-staff', admin_url + 'staff/init_activity_log_staff/' + livreurId, 'Journale d\'activité');
        //Hide Column
        var hidden_columns = [0];
        $.each(hidden_columns, function (i, val) {
            var column_status = $('.table-activity-log-staff').DataTable().column(val);
            column_status.visible(false);
        });

        // Init data table commisions
        var headers_commisions = $('.table-commisions').find('th');
        var not_sortable_commisions = (headers_commisions.length - 1);
        initDataTable('.table-commisions', admin_url + 'commisions/index/' + livreurId, 'Commisions', [not_sortable_commisions], [not_sortable_commisions]);
        // Validate form commision
        _validate_form($('#form-commision'), {
            ville: 'required',
            commision: 'required',
            commision_refuse: 'required'
        }, manage_commision);
        // Show modal add & edit commision
        $('#commision_modal').on('show.bs.modal', function (e) {
            var invoker = $(e.relatedTarget);
            var id = $(invoker).data('id');
            $('#commision_modal .add-title').removeClass('hide');
            $('#commision_modal .edit-title').addClass('hide');
            $('#commision_modal select').selectpicker('val', '');
            $('#commision_modal input').val('');
            $('#commision_modal input[name="livreur"]').val(livreurId);
            // is from the edit button
            if (typeof (id) !== 'undefined') {
                var cityId = $(invoker).data('city');
                var commision = $(invoker).data('commision');
                var commision_refuse = $(invoker).data('commisionrefuse');
                console.log($(invoker).data())
                $('#commision_modal input[name="id"]').val(id);
                $('#commision_modal .add-title').addClass('hide');
                $('#commision_modal .edit-title').removeClass('hide');
                $('#commision_modal select[name="ville"]').selectpicker('val', cityId);
                $('#commision_modal input[name="commision"]').val(commision);
                $('#commision_modal input[name="commision_refuse"]').val(commision_refuse);
            }
        });


        $('#commision_modal button[group="submit"]').on('click',function(){

            $('#submit').removeAttr('disabled');


        })
    }

    $('body').on('change', 'select[name="department"]', function() {
        console.log('try it')
        var clients = $('select[name="department"]').selectpicker('val');
        $('#department .bloc-department-selected').html('');
        console.log(clients);
        $('input[name="departments[]"]').val(clients);
        if(clients.length > 0) {
            for(var i=0; i < clients.length; i++) {
                var clientValue = clients[i];
                var clientText = $("#department option[value='"+clientValue+"']").text();
                if($('#department-added-' + clientValue).length === 0) {
                    $('#department .selectpicker.bs-select-hidden').append('<label id="department-added-' + clientValue + '" class="label label-default lineh30 mright5">' + clientText + '"' + '<input type="hidden" name="department[]" value="' + clientValue + '"></label>');

                }else{
                    alert_float('warning', 'Client déjà ajouté à la liste.');
                }
            }
        }
    });
  //  $('select[name="department"]').val(['1', '2']);

    $('input[name="departments[][]"]').attr('name', 'departments[]');
    $('input[name="departments[][][]"]').attr('name', 'departments[]');
    console.log( $('input[name="departments[]"]').val())
    let deps = $('input[name="departments[]"]').val();
    if (deps !== "" && deps != undefined)
    {
        let arrdep =  deps.split(',');
        console.log(arrdep)
        // for (var i = 0 ; i<arrdep.length; i++)
        // {

        $('select[name="department"]').val(arrdep).trigger('change');

    }

    // }
  //  console.log( $('select[name="department"]').val())
 if (livreurId != null) {
        $.post(admin_url + "staff/checkcomission", {type: livreurId}, function (response) {
            var response = $.parseJSON(response);
            red(response.success)
      if (!response.success)
            {
                $('#menu').find('a').each(function(i)
                {
                    $(this).attr("href", window.location.href)
                });
            }

        });
    }


  $(window).on("beforeunload", function() {
        if (livreurId != null) {
            $.post(admin_url + "staff/checkcomission", {type: livreurId}, function (response) {
                var response = $.parseJSON(response);
                red(response.success)


       if (!response.success)
            {
                $('#menu').find('a').each(function(i)
                {
                    $(this).attr("href", window.location.href)
                });
            }

            });
        }
    })






});

// Show password on hidden input field
function showPassword(name) {
    var target = $('input[name="' + name + '"]');
    if ($(target).attr('type') === 'password' && $(target).val() !== '') {
        $(target).queue(function () {
            $(target).attr('type', 'text').dequeue();
        });
    } else {
        $(target).queue(function () {
            $(target).attr('type', 'password').dequeue();
        });
    }
}

// Called when editing member profile
function init_roles_permissions(roleid, user_changed) {

    if (typeof (roleid) === 'undefined') {
        roleid = $('select[name="role"]').val();
    }

    var isedit = $('.member > input[name="isedit"]');
    // Check if user is edit view and user has changed the dropdown permission if not only return
    if (isedit.length > 0 && typeof (roleid) === 'undefined' && typeof (user_changed) === 'undefined') {
        return;
    }
    // Last if the roleid is blank return
    if (roleid === '') {
        return;
    }
    // Get all permissions
    var permissions = $('table.roles').find('tr');
    $('.roles').find('input').prop('checked', false);
    $.get(admin_url + 'misc/get_role_permissions_ajax/' + roleid).done(function (response) {
        response = JSON.parse(response);
        var can_view_st, can_view_own_st;
        $.each(permissions, function () {
            var permissionid = $(this).data('id');
            var row = $(this);
            $.each(response, function (i, obj) {
                if (permissionid == obj.permissionid) {
                    can_view_st = (obj.can_view == 1 ? true : false);
                    can_view_own_st = (obj.can_view_own == 1 ? true : false)
                    row.find('[data-can-view]').prop('checked', can_view_st);
                    if (can_view_st == true) {
                        row.find('[data-can-view]').change();
                    }
                    row.find('[data-can-view-own]').prop('checked', can_view_own_st);
                    if (can_view_own_st == true) {
                        row.find('[data-can-view-own]').change();
                    }
                    row.find('[data-can-edit]').prop('checked', (obj.can_edit == 1 ? true : false));
                    row.find('[data-can-create]').prop('checked', (obj.can_create == 1 ? true : false));
                    row.find('[data-can-download]').prop('checked', (obj.can_download == 1 ? true : false));
                    row.find('[data-can-export]').prop('checked', (obj.can_export == 1 ? true : false));
                    row.find('[data-can-delete]').prop('checked', (obj.can_delete == 1 ? true : false));
                }
            });
            $('.roles').find('input').prop('disabled', true);
        });
    });
}

function manage_point_relais(form)
{
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        alert_float(response.type, response.message);
        if (response.success === true) {
            $('.table-points-relais').DataTable().ajax.reload();
        }
        $('#point_relais_modal').modal('hide');
    });

    return false;
}

function removePointRelais(id)
{
    $.post(admin_url + 'staff/delete_point_relais/' + id).success(function (response) {
        response = $.parseJSON(response);
        alert_float(response.type, response.message);
        if (response.success === true) {
            $('.table-points-relais').DataTable().ajax.reload();
        }
    });

    return false;
}

function manage_commision(form)
{
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        console.log(response)
        response = $.parseJSON(response);
        alert_float(response.type, response.message);
        if (response.success === true) {
            $('.table-commisions').DataTable().ajax.reload();
        }
        $('#commision_modal').modal('hide');

        location.reload();

    });

    return false;
}

function removeCommision(id)
{
    $.post(admin_url + 'commisions/delete/' + id).success(function (response) {
        response = $.parseJSON(response);
        alert_float(response.type, response.message);
        if (response.success === true) {
            $('.table-commisions').DataTable().ajax.reload();
        }
    });

    return false;
}

function show_onglet_permissions() {
    //Show onglet permissions
    if ($('#bloc-onglet-permissions').hasClass('display-none')) {
        $('#bloc-onglet-permissions').removeClass('display-none');
        $('.bloc-permissions').removeClass('display-none');
    }
}

function hide_onglet_permissions() {
    //Hide onglet permissions
    if (!$('#bloc-onglet-permissions').hasClass('display-none')) {
        $('#bloc-onglet-permissions').addClass('display-none');
        $('.bloc-permissions').addClass('display-none');
    }
}

 function  removePointRelais(id)
{

}


function comissionadd(respond,e)
{
    if (!respond)
    {
        $('a[aria-controls="commisions"]' ).trigger('click')

        alert_float("danger", "add commission");
       // e.preventDefault();
        $('#submit').attr('disabled', 'disabled');

    }
}
function red (respond)
{
    if (!respond)
    {
    Swal.fire(
        {
            icon: 'error',
            title: 'Oops...',
            text: 'AJOUTER Commission',

        }
    )
}
}


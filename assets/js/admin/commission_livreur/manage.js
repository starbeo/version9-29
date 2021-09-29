$(document).ready(function() {
    // Init data table for factures
    var headers_factures = $('.table-factures12').find('th');
    var not_sortable_factures = (headers_factures.length - 1);
    var FacturesServerParams = {
        "f-clients": "[name='f-clients']",
        "f-statut": "[name='f-statut']",
        "f-utilisateur": "[name='f-utilisateur']",
        "f-date-created": "[name='f-date-created']"
    };
    initDataTable('.table-factures12', window.location.href, 'factures', [not_sortable_factures], [not_sortable_factures], FacturesServerParams);
    var hidden_columns_factures = [0];
    $.each(hidden_columns_factures,function(i,val){
        var column_factures = $('.table-factures12').DataTable().column(val);
        column_factures.visible(false);
    });
   
    // Init single facture if id exist in url
    init_facture();

    _validate_form($('#comment'), {
        commentaire: 'required'
    }, manage_commentaire_facture);

    $('#commentaire_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#commentaire_modal .add-title').removeClass('hide');
        $('#commentaire_modal .edit-title').addClass('hide');
        $('#commentaire_modal textarea').val('');
        $('#commentaire_modal button[id="submit"]').attr('disabled', false);
        // is from the edit button
        if (typeof(id) !== 'undefined') {
            var comment = $(invoker).data('comment');
            $('#commentaire_modal input[name="id"]').val(id);
            $('#commentaire_modal .add-title').addClass('hide');
            $('#commentaire_modal .edit-title').removeClass('hide');
            $('#commentaire_modal textarea[name="commentaire"]').val(comment);
        }
    });
   
    // Invoices Batch
    $('#batch_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var type = $(invoker).data('type');
        if(type !== '') {
            $('#batch_modal select[name="type"]').selectpicker('val', type);
            if(type === 2) {
                if($('#batch_modal #bloc-type-livraison').hasClass('display-none')) {
                    $('#batch_modal #bloc-type-livraison').removeClass('display-none');
                }
            } else {
                if(!$('#batch_modal #bloc-type-livraison').hasClass('display-none')) {
                    $('#batch_modal #bloc-type-livraison').addClass('display-none');
                }
            }
        } else {
            $('#batch_modal select[name="type"]').attr('disabled', false);
            $('#batch_modal select[name="type"]').selectpicker('refresh');
        }
        $('#batch_modal select[name="type_livraison"]').selectpicker('refresh');
        $('#batch_modal select[name="expediteurs"]').selectpicker('refresh');
        $('#batch_modal .bloc-invoices-created').html('');
        $('#batch_modal .bloc-errors-orders').html('');
        if(! $('#bloc-results-batch-invoices').hasClass('display-none')) {
            $('#bloc-results-batch-invoices').addClass('display-none');
        }
    });
    // On change input start & end date
    $('body').on('change', '#batch_modal select[name="type_livraison"], #batch_modal input[name="start_date"], #batch_modal input[name="end_date"], #batch_modal select[name="type"]', function() {
        var typeLivraison = $('#batch_modal select[name="type_livraison"]').selectpicker('val');
        var type = $('#batch_modal select[name="type"]').selectpicker('val');
        var startDate = $('#batch_modal input[name="start_date"]').val();
        var endDate = $('#batch_modal input[name="end_date"]').val();
        if(typeLivraison !== '') {
            if(type !== '') {
                if(startDate !== '' && endDate !== '') {
                    if(validateDate(startDate) && validateDate(endDate)) {
                        if($('#batch_modal input[name="start_date"]').datepicker('getDate') <= $('#batch_modal input[name="end_date"]').datepicker('getDate')) {
                            getListClients(startDate, endDate, type);

                        } else {
                            alert_float('warning', 'La date de début doit être inférieur à la date de fin');
                        }    
                    } else {
                        alert_float('warning', 'Date invalide');  
                    }
                } else {
                    alert_float('warning', 'Remplissez la date de début et la date de fin');    
                }
            } else {
                alert_float('warning', 'Choississez un type');  
            }
        } else {
            alert_float('warning', 'Choississez un type de livraison');  
        }
    });

 $('body').on('change', '#batch_modal select[name="select_type"]', function() {
//function to all select
     let typeselect =  $('#batch_modal select[name="select_type"]').selectpicker('val');
    if (typeselect === "selectmulti")
          selectall()

    });

    // On change select clients
    $('body').on('change', '#batch_modal select[name="expediteurs"]', function() {

        var clients = $('#batch_modal select[name="expediteurs"]').selectpicker('val');
        $('#batch_modal .bloc-customers-selected').html('');
        if(clients.length > 0) {
            for(var i=0; i < clients.length; i++) {
                var clientValue = clients[i];
                var clientText = $("#batch_modal #expediteurs option[value='"+clientValue+"']").text();
           
                var nbrCustomers = $('.bloc-customers-selected label').length;
                if(nbrCustomers < 40) {
                    if($('#customer-added-' + clientValue).length === 0) {
                        $('#batch_modal .bloc-customers-selected').append('<label id="customer-added-' + clientValue + '" class="label label-default lineh30 mright5">' + clientText + ' <i class="fa fa-trash cF00 curp" onclick="removeCustomerSelected(' + clientValue + ');" title="Supprimer le client de la liste."></i><input type="hidden" name="customers[]" value="' + clientValue + '"></label>');
                    } else {
                        alert_float('warning', 'Client déjà ajouté à la liste.');
                    }  
                } else {
                    alert_float('danger', 'Maximum 10 Clients à ajouter à la fois.');
                    var clientsSelected = $('#batch_modal select[name="expediteurs"]').selectpicker('val');
                    clientsSelected.remove(clientValue.toString());
                    $('#batch_modal select[name="expediteurs"]').selectpicker('val', clients);
                    $('#batch_modal select[name="expediteurs"]').selectpicker('refresh');
                }
            }  
        }
    });
    // Validate form batch invoices
    _validate_form($('#batch-form'), {
        type_livraison: 'required',
        type: 'required',
        start_date: 'required',
        end_date: 'required'
    }, manage_batch_invoices);
   
    _validate_form($('#add-additionnal-line-form'), {
        description_line: {
            "required": true,
            "regex": /^[a-zA-Z0-9-\/] ?([a-zA-Z0-9-\/]|[a-zA-Z0-9-\/] )*[a-zA-Z0-9-\/]$/
        },
        total_line: 'required'
    }, manage_add_additionnal_line);

    $('#add_line_additionnal_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#add_line_additionnal_modal input').val('');
       
        // is from the edit button
        if (typeof(id) !== 'undefined') {
            var description_line = $(invoker).data('description-line');
            var total_line = $(invoker).data('total-line');
            $('#add_line_additionnal_modal input[name="id"]').val(id);
            $('#add_line_additionnal_modal input[name="description_line"]').val(description_line);
            $('#add_line_additionnal_modal input[name="total_line"]').val(total_line);
        }
    });

    //Onclick sent email invoice
    $('body').on('click', '#facture-send-to-client', function() {
        var email = $(this).attr('data-email');
        if(email === ''){
            alert_float("warning", "Ce client n'as pas une adresse mail !!");
        } else {
            var invoice_id = $(this).attr('data-invoiceid');
            window.location.href = admin_url+'factures/send_to_email/'+invoice_id+'/'+email;
        }
    });
   
    $('body').on('click', '.btn-filtre', function() {
    if($('#filtre-table').hasClass('display-none')){
            $('#filtre-table').removeClass('display-none');
    }else{
            $('#filtre-table').addClass('display-none');
    }
    });
   
    $('body').on('click', '#filtre-submit', function() {
        $('.table-factures12').DataTable().ajax.reload();
        $('#filtre-table').addClass('display-none');
    });
   
    $('body').on('click', '#filtre-reset', function() {
        $('#filtre-table select').selectpicker('val', '');
        $('#filtre-table input').val('');
        $('.table-factures12').DataTable().ajax.reload();
    });
   
    Array.prototype.remove = function() {
        var what, a = arguments, L = a.length, ax;
        while (L && this.length) {
            what = a[--L];
            while ((ax = this.indexOf(what)) !== -1) {
                this.splice(ax, 1);
            }
        }
        return this;
    };
});

//Add Comment To Invoice
function manage_commentaire_facture(form) {
    $('#commentaire_modal button[id="submit"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function(response) {
        response = $.parseJSON(response);
        if (response.success === true) {
            $('.table-factures12').DataTable().ajax.reload();
            alert_float('success', response.message);
        }else if(response.success === 'access_denied'){
            alert_float('warning',response.message);
        }
        $('#commentaire_modal').modal('hide');
    });

    return false;
}

//select all function
function selectall()
{
  let arri = [];
    $('#batch_modal select[name="expediteurs"]').find('option').each(function() {
        //  console.log();
        if($(this).val() !="")
        arri.push($(this).val())});
    console.log(arri)
    $('#batch_modal select[name="expediteurs"]').val(arri).change();
}



//Add Additionnal Line To Invoice
function manage_add_additionnal_line(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function(response) {
        response = $.parseJSON(response);
        if (response.success === true) {
            alert_float('success', response.message);
            var idFacture = $('#add-additionnal-line-form input[name="id"]').val();
            if($.isNumeric(idFacture)) {
                init_facture(idFacture);
                $('.table-factures12').DataTable().ajax.reload();
            }
        } else if(response.success === 'access_denied'){
            alert_float('warning',response.message);
        }
        $('#add_line_additionnal_modal').modal('hide');
    });

    return false;
}

// Init single facture
function init_facture(id) {
    var _invoiceid = $('input[name="invoiceid"]').val();
    // Check if invoice passed from url
    if (_invoiceid !== '') {
        id = _invoiceid;
        // Clear the current invoice value in case user click on the left sidebar invoices
        $('input[name="invoiceid"]').val('');
    } else {
        if (typeof(id) === 'undefined' || id === '') {
            return;
        }
    }
    if (!$('body').hasClass('small-table')) {
        toggle_small_view('.table-factures12', '#facture');
    }
    $('#facture').load(admin_url + 'commission_livreur/get_facture_data_ajax/' + id);

    if (is_mobile()) {
        $('html, body').animate({
            scrollTop: $('#facture').offset().top + 150
        }, 600);
    } else {
        $('html, body').animate({
            scrollTop: $('#facture').offset().top
        }, 400);
    }
}

// Remove customer Selected
function removeCustomerSelected(customerId) {
    if($('#customer-added-' + customerId).length > 0) {
        $('#customer-added-' + customerId).remove();
        var clients = $('#batch_modal select[name="expediteurs"]').selectpicker('val');
        clients.remove(customerId.toString());
        $('#batch_modal select[name="expediteurs"]').selectpicker('val', clients);
        $('#batch_modal select[name="expediteurs"]').selectpicker('refresh');
    }
}
//Batch invoices
function manage_batch_invoices(form) {
    $(".wait").css("display", "block");
    $('#batch_modal select[name="type"]').attr('disabled', false);
    $('#batch_modal button[id="submit-batch-invoices"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function(response) {
        response = $.parseJSON(response);
        if (response.message !== '') {
            alert_float(response.type, response.message);
        }
        //Show Errors
        if($('#bloc-results-batch-invoices').hasClass('display-none')) {
            $('#bloc-results-batch-invoices').removeClass('display-none');
        }
        $('#batch_modal .bloc-invoices-created').html(response.messageSuccess);
        $('#batch_modal .bloc-errors-orders').html(response.messageErrors);
        //Delete customer to list customers selected
        if(response.idsCustomers.length > 0) {
            //Load tables invoices
            $('.table-factures12').DataTable().ajax.reload();
            //Remove customer to list customers selected
            $.map(response.idsCustomers, function (idsCustomer) {
                $('#customer-added-' + idsCustomer).remove();
            });
        }
        $('#batch_modal button[id="submit-batch-invoices"]').attr('disabled', false);
        $(".wait").css("display", "none");
    });

    return false;
}
function validateDate(date)
{
    // regular expression to match required date format
    re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;

    if(date !== '' && !date.match(re)) {
      return false;
    }

    // regular expression to match required time format
    //re = /^\d{1,2}:\d{2}([ap]m)?$/;

    //if(form.starttime.value != '' && !form.starttime.value.match(re)) {
      //return false;
    //}

    return true;
}

function getListClients(startDate, endDate, type)
{
    $.post(admin_url + 'factures/get_clients_batch', {start_date: startDate, end_date: endDate, type: type}).success(function(response) {
        response = $.parseJSON(response);
        alert_float(response.type, response.message);
        $('select[name="expediteurs"]').html('<option value=""></option>');
        if(response.clients !== null && typeof(response.clients) !== 'undefined' && response.clients.length > 0){
            for (var i = 0; i < response.clients.length; i++) {
                $('select[name="expediteurs"]').append('<option value="'+response.clients[i].id+'">'+response.clients[i].nom+'</option>');
            }
        }
        $('select[name="expediteurs"]').selectpicker('refresh');
    });

    return false;
}

function initModalBatchInvoices() {
    $('#batch_modal input').val('');
    $('#batch_modal select[name="expediteurs"]').html('<option value=""></option>');
    $('#batch_modal select[name="expediteurs"]').selectpicker('refresh');
    $('#batch_modal .bloc-customers-selected').html('');
    $('#batch_modal .bloc-invoices-created').html('');
    $('#batch_modal .bloc-errors-orders').html('');
    if(! $('#bloc-results-batch-invoices').hasClass('display-none')) {
        $('#bloc-results-batch-invoices').addClass('display-none');
    }
}


function change_status_etat(toType) {
    if (toType === 'noregle' || toType === 'regle') {
     var   data1 = [];
        var   data =
        $('input[name="ids[]"]').each(function () {
            if (this.checked)
                data1.push(this.value);
        });
        var status = 0;
        if (toType === 'noregle') {
            status = 1;
        } else if (toType === 'regle') {
            status = 2;
        }
          data1 = JSON.stringify(data1)
        $.post(admin_url + 'commission_livreur/change_status/'+status, {data1 :data1,dt :12 }).success(function (response) {
            response = $.parseJSON(response);
            alert_float(response.type, response.message);
            if (response.success === true) {
                $('.table-etat-colis-livrer').DataTable().ajax.reload();
            }
        });
    }

    return false;
}

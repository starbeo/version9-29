$(document).ready(function(){

   $.validator.setDefaults({
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

    //Init Data Table Colis
    var headers_colis = $('.table-colis').find('th');
    var not_sortable_colis = (headers_colis.length - 1);
	initDataTable('.table-colis',window.location.href,'colis',[not_sortable_colis],[not_sortable_colis]);
    
    //Init Data Table Factures
    var headers_factures = $('.table-factures').find('th');
    var not_sortable_factures = (headers_factures.length - 1);
    initDataTable('.table-factures',window.location.href,'factures',[not_sortable_factures],[not_sortable_factures]);
    
    //Init Data Table Reclamations
    var headers_reclamations = $('.table-reclamations').find('th');
    var not_sortable_reclamations = (headers_reclamations.length - 1);
    initDataTable('.table-reclamations',window.location.href,'reclamations',[not_sortable_reclamations],[not_sortable_reclamations]);

    //Form import colis
    _validate_form($('form'),{
        file_xls:{
                  required:true, 
                  extension: "xlsx"
                 }
    });

    //Click btn Form import colis
    $('.btn-import-submit').on('click',function(){
        if($(this).hasClass('import')){
            $('form').append(hidden_input('import', true));
        }
        $('form').submit();
    });
});

// Generate hidden input field
function hidden_input(name, val) {
    return '<input type="hidden" name="' + name + '" value="' + val + '">';
}

function initDataTable(table,url,item_name,notsearchable,notsortable,defaultorder){

	if($(table).length == 0){
		return;
	}

    if (typeof(defaultorder) == 'undefined') {
        defaultorder = [0, 'ASC'];
    }

   var buttons = [
                    {
                        extend: 'colvis',
                        postfixButtons: ['colvisRestore'],
                        className: 'btn btn-default dt-column-visibility',
                        text: dt_button_column_visibility
                    }, 
                    {
                        text: dt_button_reload,
                        className: 'btn btn-default',
                        action: function(e, dt, node, config) {
                            dt.ajax.reload();
                        }
                    }
                  ];

	var table = $(table).dataTable( {
        "language": {
            "emptyTable": dt_emptyTable.format(item_name),
            "info": dt_info.format(item_name),
            "infoEmpty": dt_infoEmpty.format(item_name),
            "infoFiltered": dt_infoFiltered.format(item_name),
            "lengthMenu": dt_lengthMenu.format(item_name),
            "loadingRecords": dt_loadingRecords,
            "processing": '<div class="dt-loader"></div>',
            "search": dt_search,
            "zeroRecords": dt_zeroRecords,
            "paginate": {
                "first": dt_paginate_first,
                "last": dt_paginate_last,
                "next": dt_paginate_next,
                "previous": dt_paginate_previous
            },
            "aria": {
                "sortAscending": ": " +dt_sortAscending,
                "sortDescending": ": " + dt_sortDescending
            }
        },
		"processing": true,
		"serverSide": true,
		'paginate':true,
		'searchDelay':300,
		'responsive':true,
		"bLengthChange": false,
		"pageLength": tables_pagination_limit,
        "order": [defaultorder],
        buttons: buttons,
		"columnDefs": [
		{ "searchable": false, "targets": notsearchable },
		{ "sortable": false, "targets": notsortable }
		],
		"ajax": {
			"url":url,
			"type":"POST",
		}
	} );
}

// General validate form function
function _validate_form(form, form_rules, submithandler) {    
    $.validator.setDefaults({
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

    $(form).validate({
        rules: form_rules,
        messages: {
            email: {
                remote: 'Email already exists'
            },
            reference_product: {
                remote: 'Reference already exists'
            }
        },
        ignore: [],
        submitHandler: function(form) {
            if (typeof(submithandler) !== 'undefined') {
                submithandler(form);
            } else {
                return true;
            }
        }
    });

    setTimeout(function() {
        var custom_required_fields = $('[data-custom-field-required]');
        if (custom_required_fields.length > 0) {
            $.each(custom_required_fields, function() {
                $(this).rules("add", {
                    required: true
                });
            });
        }

    }, 10);
    return false;
}   


// JS files used for delivery notes
$(document).ready(function() {
	
    //Init Data Table Delivery notes
    var headers_delivery_notes = $('.table-delivery-notes').find('th');
    var not_sortable_delivery_notes = (headers_delivery_notes.length - 1);
	initDataTable('.table-delivery-notes',window.location.href,'bons livraison',[not_sortable_delivery_notes],[not_sortable_delivery_notes]);
    
	_validate_form($('#bon-livraison-form'), {
	    nom : 'required'
	  });


	var bonlivraison_id = $('input[name="bonlivraison_id"]').val();
	if(typeof(bonlivraison_id) !== 'undefined'){
	  	initDataTable('.table-colis-bon-livraison', site_url +'expediteurs/init_colis_bon_livraison', 'colis-bon-livraison');
		initDataTable('.table-historique-colis-bon-livraison', site_url +'expediteurs/init_historique_colis_bon_livraison/'+bonlivraison_id, 'historique-colis-bon-livraison');
	}

  	$('body').on('click', '.colis_added', function() {
	    var bonlivraison_id = $('input[name="bonlivraison_id"]').val();
	    var colis_id        = $(this).attr('data-id');
	    if(colis_id !== '' && bonlivraison_id !== ''){
	      	$.when(
		        $.post(site_url+"expediteurs/add_colis_to_bon_livraison", {bonlivraison_id: bonlivraison_id, colis_id: colis_id}, function(response) {
		            var response = jQuery.parseJSON(response);
		            if(response.success == true){
		              alert_float(response.type, response.message);
		            }
		        })
	      	).then(
	        	hide_colis($(this))
	      	);
	    }
  	});

  	$('body').on('click', '.colis_remove', function() {
	    var colisbonlivraison_id = $(this).attr('data-colisbonlivraison-id');
	    if(colisbonlivraison_id !== ''){
	      	$.when(
		        $.post(site_url+"expediteurs/remove_colis_to_bon_livraison", {colisbonlivraison_id: colisbonlivraison_id}, function(response) {
		            var response = jQuery.parseJSON(response);
		            if(response.success == true){
		              alert_float(response.type, response.message);
		              window.scrollTo(0, 1000);
		            }
		        })
	      	).then(
	        	hide_colis($(this))
	      	);
	    }
  	});

	function hide_colis(row){
	  	$(row).parents('tr').addClass('animated fadeOut', function() {
	      	setTimeout(function() {
	          	$(row).parents('tr').remove();
	      	}, 200)
	  	});
	  	$('.table-colis-bon-livraison').DataTable().ajax.reload();
	  	$('.table-historique-colis-bon-livraison').DataTable().ajax.reload();
	}
});
// JS files used for status colis
$(document).ready(function() {
	
	$('.kan-ban-status-colis-modal').on('show.bs.modal', function(event) {
        // Set default tab profiel
        var button = $(event.relatedTarget);
        var id = button.data('colis-id');
        if (typeof(id) == 'undefined') {
            return;
        }
        init_status_colis_modal_data(id);
    });

	function init_status_colis_modal_data(id) {
	    // clean the modal
	    $('.kan-ban-status-colis-modal .modal-body').html('');
	    $.get(site_url + 'expediteurs/get_status_colis_kan_ban_content/' + id, function(data) {
	        $('.kan-ban-status-colis-modal .modal-body').html(data);
	    });
	}

	// Show modal status if colis id exist
	var colis_id = $('#colis_id').val();
	if(typeof(colis_id) != 'undefined' && colis_id != ''){
		init_status_colis_modal_data(colis_id);
		$('.kan-ban-status-colis-modal').modal('show');
	}
});
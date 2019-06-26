jQuery(document).ready(function() {

	jQuery(".simpleEventAjaxForm").on("submit", function(e) {
		e.preventDefault()
		var selectedNode = jQuery(this)
		selectedNode.find("button").html("Submiting...")
		var datastring = jQuery(this).serialize();
		jQuery.ajax({
			url: simple_events_js_var.ajax_url,
			method: 'POST',
			data: datastring,
			success: function(response) {
				selectedNode.find("button").html("Submited")
				console.log(response, 'success')
			},
			error: function(error) {				
				console.log(error, 'error')
			}
		})
	})	

})
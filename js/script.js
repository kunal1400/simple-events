jQuery(document).ready(function() {

	jQuery(".simpleEventAjaxForm").on("submit", function(e) {
		e.preventDefault()
		var selectedNode = jQuery(this)
		selectedNode.find(".successMsg").text("")
		selectedNode.find("button").html("Submiting...")
		var datastring = jQuery(this).serialize();
		jQuery.ajax({
			url: simple_events_js_var.ajax_url,
			method: 'POST',
			data: datastring,
			success: function(response) {
				selectedNode.find("button").html("Submited")
				selectedNode.find(".successMsg").text("You have successfully registered to this event")
				
				selectedNode.find("input").text("")
				selectedNode.find("input").val("")
				selectedNode.find("textarea").text("")
				selectedNode.find("textarea").val("")
				selectedNode.find("input").prop('checked', false);

				console.log(response, 'success')
			},
			error: function(error) {
				selectedNode.find(".successMsg").text("")
				console.log(error, 'error')
			}
		})
	})	

})
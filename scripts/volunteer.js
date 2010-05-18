/* Javascript functions for Volunteer Dashboard
 *
 * Trevor Fountain, 14 January 2010
 */
 
function handle_review(id,action) {
	new Ajax.Request('handle_pending_review.php?action='+action+'&id='+id,
		{
			method:'get',
			requestHeaders: {Accept: 'text/html'},
			onSuccess: function(xml) { $('pending_'+id).style.display='none'; }
		});
	return false;
}

function reject(id) {
	handle_review(id,"reject");
}

function approve(id) {
	handle_review(id,"accept");
}

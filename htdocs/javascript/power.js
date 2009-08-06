var POWER_URL = SITE_URL+'/ajax/power.php';

function previewPower(id) {
	var p_id = id.substring(1);			
	$.post(POWER_URL,
		{
			id: CHAR_ID,
			p_id: p_id
		},
		function(data) {
			var response = data.split(MESSSAGE_DELIMITER);
			var result = response[0];
			if( PROCESS_FAILURE != result ) {
				$('#PowerPreview').slideUp(function() {
					$('#PowerPreview').html(result);
					$('#PowerPreview').slideDown();
				});
			}
			
			if( response.length > 1 ) {
				printMessage(new Array(response[1],response[2]));
			}
		}
	);
}

$(document).ready(function() {
	$('.power_preview').click(function() { previewPower(this.id) });
});
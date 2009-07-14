var PLAYER_PROCESS = SITE_URL+"/ajax/playerProcess.php";

function usePower(id) {
	var powerID = '#power'+id+' .description';
	
	$.post(SITE_URL+"/ajax/playerProcess.php",
		{
			id: CHAR_ID,
			p_id: id,
			action: 'togglePower'
		},
		function(data) {
			if( data ) {
				$(powerID).toggle();
			}
		}
	);
}

function addSurge() {	
	$.post(PLAYER_PROCESS,
		{
			id: CHAR_ID,
			action: 'addSurge'
		},
		function(data) {
			if( data ) {
				$('#surges_cur').text(data);
			}
		}
	);
}
function subtractSurge() {	
	$.post(PLAYER_PROCESS,
		{
			id: CHAR_ID,
			action: 'subtractSurge'
		},
		function(data) {
			if( data ) {
				$('#surges_cur').text(data);
			}
		}
	);
}


$(document).ready(function() {
	$(".power .titleBar").click(function() { usePower(this.id); });
	$("#surgePlus").click(function() { addSurge() });
	$("#surgeMinus").click(function() { subtractSurge() });

	
});
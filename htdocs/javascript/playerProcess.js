var PLAYER_PROCESS = SITE_URL+"/ajax/playerProcess.php";

function usePower(divID) {
	var id = divID.substring(1);
	var powerIDstring = '#powerBox'+id+' .description';
	
	$.post(SITE_URL+"/ajax/playerProcess.php",
		{
			id: CHAR_ID,
			p_id: id,
			action: 'togglePower'
		},
		function(data) {
			if( data ) {
				$(powerIDstring).toggle();
			}
		}
	);
}

function updateSurges(op) {
	var op_string;
	if( 'add' == op ) {
		op_string = 'addSurge';
	}
	else {
		op_string = 'subtractSurge';
	}
	
	$.post(PLAYER_PROCESS,
		{
			id: CHAR_ID,
			action: op_string
		},
		function(data) {
			if( data ) {
				$('#surges_cur').text(data);
			}
		}
	);
}

function updateActionPoints(op) {
	var op_string;
	if( 'subtract' == op ) {
		op_string = 'subtractActionPoint';
	}
	else {
		op_string = 'addActionPoint';
	}

	$.post(PLAYER_PROCESS,
		{
			id: CHAR_ID,
			action: op_string
		},
		function(data) {
			if( data ) {
				$('#action_points').text(data);
			}
		}
	);
}


$(document).ready(function() {
	$(".power .titleBar").click(function() { usePower(this.id); });
	$("#surgePlus").click(function() { updateSurges('add') });
	$("#surgeMinus").click(function() { updateSurges('subtract') });
	$('#apPlus').click(function() { updateActionPoints('add') });
	$('#apMinus').click(function() { updateActionPoints('subtract') });
		
});
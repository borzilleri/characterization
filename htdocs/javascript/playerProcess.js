var PLAYER_PROCESS = SITE_URL+"/ajax/playerProcess.php";
var PROCESS_FAILURE = 'FALSE';
var RESULT_DELIMITER = ':';
var MESSSAGE_DELIMITER = '|';
var STATUS_DEAD = 'Dead';
var STATUS_UNCONSCIOUS = 'Unconscious';
var STATUS_BLOODIED = 'Bloodied';

var notes_tmp = '';

function updateCurrentHealth(health_cur) {
	$('#health_cur').text(health_cur);
	var health_max = $('#health_max').text();
	var bloodied_val = Math.floor(health_max/2);
	
	$('#health_cur').removeClass();
	$('#health_status').removeClass();
	$('#health_status').text('');
	
	// Are we Dead?
	if( health_cur < -1*bloodied_val ) {
		$('#health_cur').addClass(STATUS_DEAD);
		$('#health_status').addClass(STATUS_DEAD);
		$('#health_status').text(STATUS_DEAD);
	}
	else if( health_cur < 1 ) {
		$('#health_cur').addClass(STATUS_UNCONSCIOUS);
		$('#health_status').addClass(STATUS_UNCONSCIOUS);
		$('#health_status').text(STATUS_UNCONSCIOUS);
	}
	else if( health_cur < bloodied_val ) {
		$('#health_cur').addClass(STATUS_BLOODIED);
		$('#health_status').addClass(STATUS_BLOODIED);
		$('#health_status').text(STATUS_BLOODIED);
	}	
}
function updateTempHealth(health_tmp) {
	if( health_tmp > 0 ) {
		$('#health_tmp').text('('+health_tmp+')');
	}
	else {
		$('#health_tmp').text('');
	}
}

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
			var response = data.split(MESSSAGE_DELIMITER);
			var result = response[0];
			
			if( PROCESS_FAILURE != response ) {
				$(powerIDstring).toggle();
			}
			
			if( response.length > 1 ) {
				printMessage(new Array(response[1],response[2]));
			}
		}
	);
}

function updateSurges(op) {
	var op_string = op+'Surge';
	
	$.post(PLAYER_PROCESS,
		{
			id: CHAR_ID,
			action: op_string
		},
		function(data) {
			var response = data.split(MESSSAGE_DELIMITER);
			var result = response[0];

			if( PROCESS_FAILURE != result ) {
				$('#surges_cur').text(result);
			}
			
			if( response.length > 1 ) {
				printMessage(new Array(response[1],response[2]));
			}
		}
	);
}

function spendSurge() {
	var surge_bonus = $('#surge_bonus').val();
	var response;
	
	$.post(PLAYER_PROCESS,
		{
			id: CHAR_ID,
			action: 'spendSurge',
			surge_bonus: surge_bonus
		},
		function(data) {
			var response = data.split(MESSSAGE_DELIMITER);
			var result = response[0];
			
			if( PROCESS_FAILURE != result ) {
				var info = result.split(RESULT_DELIMITER);
				
				$('#surge_bonus').val(0);
				$('#surges_cur').text(info[0]);
				updateCurrentHealth(info[1]);				
			}
			
			if( response.length > 1 ) {
				printMessage(new Array(response[1],response[2]));
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
			var response = data.split(MESSSAGE_DELIMITER);
			var result = response[0];
			
			if( PROCESS_FAILURE != result ) {
				$('#action_points').text(result);
			}
			
			if( response.length > 1 ) {
				printMessage(new Array(response[1],response[2]));
			}
		}
	);
}

function doRest(restType) {
	var divClass = 'short'==restType?'Encounter':'titleBar';
	
	$.post(PLAYER_PROCESS,
		{
			id: CHAR_ID,
			action: 'rest',
			rest_type: restType
		},
		function(data) {
			var response = data.split(MESSSAGE_DELIMITER);
			var result = response[0];
			
			if( PROCESS_FAILURE != result ) {
				// Reset temp HP.
				updateTempHealth(0);
								
				if( 'extended' == restType ) {
					var subText;
					// If we're an Extended Rest,
					// Set Current Health to Maximum Health
					updateCurrentHealth($('#health_max').text());
					// Set Current Surges to Maximum Surges
					$('#surges_cur').text($('#surges_max').text());
					// Reset Action Points
					$('#action_points').text(result);
				}
				
				// Un-hide powers
				$('div.power div.'+divClass+' ~ div.description:hidden').show();
			}
			
			if( response.length > 1 ) {
				printMessage(new Array(response[1],response[2]));
			}
		}
	);
}

function adjustHealth() {
	var damage = $('#damage_value').val();
	
	$.post(PLAYER_PROCESS,
		{
			id: CHAR_ID,
			action: 'damage',
			health: damage
		},
		function(data) {
			var response = data.split(MESSSAGE_DELIMITER);
			var result = response[0];
			
			if( PROCESS_FAILURE != result ) {
				var info = result.split(RESULT_DELIMITER);
				// Adjust Current Health
				updateCurrentHealth(info[0]);
				// Adjust Temporary Health
				updateTempHealth(info[1]);
				$('#damage_value').val(0);
			}
			
			if( response.length > 1 ) {
				printMessage(new Array(response[1],response[2]));
			}
		}
	);
}

function addTempHealth() {
	var health = $('#health').val();
	
	$.post(PLAYER_PROCESS,
		{
			id: CHAR_ID,
			action: 'tempHealth',
			health: health
		},
		function(data) {
			var response = data.split(MESSSAGE_DELIMITER);
			var result = response[0];
			
			if( PROCESS_FAILURE != result ) {
				updateTempHealth(result);
				$('#health').val(0);
			}
			if( response.length > 1 ) {
				printMessage(new Array(response[1],response[2]));
			}
		}
	);
}

function updateNotes() {
	var notesText = $('#player_notes').val();
	
	$.post(PLAYER_PROCESS,
		{
			id: CHAR_ID,
			action: 'updateNotes',
			notes: notesText
		},
		function(data) {
			var response = data.split(MESSSAGE_DELIMITER);
			var result = response[0];
			
			if( PROCESS_FAILURE != result ) {
				// At this point result stores the current value of the notes field.
				// However, since we're just dumping the content of the textarea into
				// the field, we don't actually HAVE to push this update back out.
				//
				// Still, we're going to anyway, to make sure that post-update we're
				// displaying the current content. This allows us to be sure we're
				// correct when setting the dirty/clean notification to clean
				$('#player_notes').val(result);
			
				// Now, set the dirty state to 'clean'
				$('#notes_dirty').fadeOut();
			}
			
			if( response.length > 1 ) {
				printMessage(new Array(response[1],response[2]));
			}
		});
}

function notesDirtyCheck() {
	var notes_cur = $('#player_notes').val()
	
	if( notes_tmp != notes_cur ) {
		$('#notes_dirty').fadeIn();
	}
}

$(document).ready(function() {
	// Fill the notes_tmp variable
	notes_tmp = $('#player_notes').val();
	// Handle dirty notification for player notes
	$('#player_notes').keyup(function(k) { notesDirtyCheck() });
	
	// Power Usage
	$(".power .titleBar").click(function() { usePower(this.id); });
	
	// Healing Surges
	$("#surgePlus").click(function() { updateSurges('add') });
	$("#surgeMinus").click(function() { updateSurges('subtract') });
	$("#spendSurge").click(function() { spendSurge() });

	// Action Points
	$('#apPlus').click(function() { updateActionPoints('add') });
	$('#apMinus').click(function() { updateActionPoints('subtract') });

	// Rest Actions
	$('#shortRest').click(function() { doRest('short') });
	$('#extendedRest').click(function() { doRest('extended') });
	
	// Damage/Health/Temp Health
	$('#takeDamage').click(function() { adjustHealth() });
	$('#tempHealth').click(function() { addTempHealth() });
	
	// Player notes
	$('#updateNotes').click(function() { updateNotes() });
	
	// Enable form fields on pressing enter/return
	$('#damage_value').keyup(function(e) { if(e.keyCode==13) adjustHealth(); });
	$('#health').keyup(function(e) { if(e.keyCode==13) addTempHealth(); });
	$('#surge_bonus').keyup(function(e) { if(e.keyCode==13) spendSurge(); });
});
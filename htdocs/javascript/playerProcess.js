var PLAYER_PROCESS = SITE_URL+"/ajax/playerProcess.php";
var PROCESS_FAILURE = 'FALSE';
var RESULT_DELIMITER = ':';
var STATUS_DEAD = 'Dead';
var STATUS_UNCONSCIOUS = 'Unconscious';
var STATUS_BLOODIED = 'Bloodied';

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
			if( PROCESS_FAILURE != data ) {
				$(powerIDstring).toggle();
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
			if( PROCESS_FAILURE != data ) {
				$('#surges_cur').text(data);
			}
		}
	);
}

function spendSurge() {
	var surge_bonus = $('#surge_bonus').val();
	
	$.post(PLAYER_PROCESS,
		{
			id: CHAR_ID,
			action: 'spendSurge',
			surge_bonus: surge_bonus
		},
		function(data) {
			if( PROCESS_FAILURE != data ) {
				var result = data.split(RESULT_DELIMITER);
				
				$('#surge_bonus').val(0);
				$('#surges_cur').text(result[0]);
				updateCurrentHealth(result[1]);
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
			if( PROCESS_FAILURE != data ) {
				$('#action_points').text(data);
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
			if( PROCESS_FAILURE != data ) {
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
					$('#action_points').text(data);
				}
				
				// Un-hide powers
				$('div.power div.'+divClass+' ~ div.description:hidden').show();
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
			if( PROCESS_FAILURE != data ) {
				var result = data.split(RESULT_DELIMITER);
				// Adjust Current Health
				updateCurrentHealth(result[0]);
				// Adjust Temporary Health
				updateTempHealth(result[1]);
				$('#damage_value').val(0);
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
			if( PROCESS_FAILURE != data ) {
				updateTempHealth(data);
				$('#health').val(0);
			}
		}
	);
}

$(document).ready(function() {
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
	$('#ShortRest').click(function() { doRest('short') });
	$('#ExtendedRest').click(function() { doRest('extended') });
	
	// Damage/Health/Temp Health
	$('#takeDamage').click(function() { adjustHealth() });
	$('#tempHealth').click(function() { addTempHealth() });
});
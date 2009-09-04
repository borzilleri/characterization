var PLAYER_PROCESS_URI = SITE_URL+"/ajax/playerProcess.php";
var POWER_PREVIEW_URI = SITE_URL+'/ajax/power.php';
var STATUS_DEAD = 'Dead';
var STATUS_UNCONSCIOUS = 'Unconscious';
var STATUS_BLOODIED = 'Bloodied';
var POWER_ICON = MEDIA_URL+'/images/icon_use.png';
var POWER_ICON_USED = MEDIA_URL+'/images/icon_refresh.png';
var POWER_ICON_DISABLED = MEDIA_URL+'/images/icon_delete.png';

var notes_tmp = '';

function playerProcessRequest(action, args) {
	$.ajax({
		url: PLAYER_PROCESS_URI,
		type: "post",
		success: parseProcessResult,
		// error: errorHandler,
		dataType: 'json',
		data: {
			action: action,
			id: CHAR_ID,
			data: JSON.stringify(args)
		}
	});
}

function parseProcessResult(data, textStatus) {
	$('#surge_bonus,#damage_value,#health').val('');
	
	for(var k in data) {
		switch(k) {
			case 'refreshPowers':
				animatePower('#PowerTable tr.Encounter');
				if( data[k] ) {
					animatePower('#PowerTable tr.Daily');
					animatePower('#PowerTable tr.Healing-Surge');
				}
				break;
			case 'power':
				animatePower('#r'+data[k].pID, data[k].status);
				break;
			case 'player_notes':
				notes_tmp = data[k];
				$('#player_notes').val(data[k]);
				$('#notes_dirty').fadeOut();
				break;
			case 'health_tmp':
				updateTempHealth(data[k]);
				break;
			case 'health_cur':
				updateCurrentHealth(data[k]);
				break;
			case 'errors':
				playerErrorHandler(data[k]);
				break;
			default:
				updateText('#'+k, data[k]);
				break;
		}
	}
}

function playerErrorHandler(errors) {
	var msg = '';
	var lvl;
	for(var i in errors) {
		if( 'level' == i ) lvl = errors[i];
		else msg = msg+errors[i];
	}
	if( '' != msg ) {
		printMessage(new Array(lvl, msg));
	}
}


function updateText(id, value) {
	$(id).fadeOut(function() {
		$(id).text(value)
		$(id).fadeIn();
	});
}


function updateCurrentHealth(health_cur) {
	updateText('#health_cur', health_cur)

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
		updateText('#health_tmp', '('+health_tmp+')');
	}
	else {
		updateText('#health_tmp', '');
	}
}

function animatePower(row, status) {
	var icon = POWER_ICON;
	var opacity = 1;
	switch(status) {
		case 'Used':
			icon = POWER_ICON_USED;
			opacity = .5;
			break;
		case 'Disabled':
			icon = POWER_ICON_DISABLED;
			break;
	}
	
	$(row+' img.power_icon').fadeOut('fast', function() {
		$(this).attr('src', icon);
		$(row).fadeTo('fast', opacity, function() {
			$(row+' img.power_icon').fadeIn('fast');
		});
	});
}

function notesDirtyCheck() {
	var notes_cur = $('#player_notes').val();
	if( notes_tmp != notes_cur ) $('#notes_dirty').fadeIn();
	else $('#notes_dirty').fadeOut();
}

$(window).load(function() {
	// Preload Images
	var img_disabld = $('<img />').attr('src', POWER_ICON_DISABLED);
	var img_used = $('<img />').attr('src', POWER_ICON_USED);
	var img_usable = $('<img />').attr('src', POWER_ICON);
	
	// Fill the notes_tmp variable
	notes_tmp = $('#player_notes').val();
	// Handle dirty notification for player notes
	$('#player_notes').keyup(function(k) { notesDirtyCheck() });
	
	// Healing Surges
	$('#surgePlus').click(function(){ playerProcessRequest('addSurge'); });
	$('#surgeMinus').click(function(){ playerProcessRequest('subtractSurge'); });
	$('#spendSurge').click(function(){ playerProcessRequest('spendSurge', {
		surge_bonus: $('#surge_bonus').val() }); });
	// Enable Enter/Return activating the click fcn.
	$('#surge_bonus').keyup(function(e){ if(e.keyCode==13) $('#spendSurge').click(); });
	
	// Taking Damage
	$('#takeDamage').click(function(){ playerProcessRequest('damage', {
		health: $('#damage_value').val() }); });
	// Enable Enter/Return activating the click fcn.
	$('#damage_value').keyup(function(e){ if(e.keyCode==13) $('#takeDamage').click(); });
	
	// Temporary Health
	$('#tempHealth').click(function(){ playerProcessRequest('tempHealth', {
		health: $('#health').val() }); });
	// Enable Enter/Return activating the click fcn.
	$('#health').keyup(function(e){ if(e.keyCode==13) $('#tempHealth').click(); });

	// Action Points
	$('#apPlus').click(function(){ playerProcessRequest('addActionPoint') });
	$('#apMinus').click(function(){ playerProcessRequest('subtractActionPoint') });

	// Magic Item Uses
	$('#miPlus').click(function(){ playerProcessRequest('addMagicItemUse') });
	$('#miMinus').click(function(){ playerProcessRequest('subtractMagicItemUse') });
	
	// Rest Actions
	$('#shortRest').click(function(){ playerProcessRequest('shortRest') });
	$('#extendedRest').click(function(){ playerProcessRequest('extendedRest') });	
	
	// Player notes
	$('#updateNotes').click(function() { playerProcessRequest('updateNotes', {
		notes: $('#player_notes').val() }); });
	
	// Power Usage
	$('#PowerTable img.power_icon').click(function() {
		playerProcessRequest('togglePower', {p_id: this.id.substring(1)}) });
		
	// Power Tooltips
	$('img.power_view').each(function(i) {
		var id = this.id.substring(1);
		$(this).qtip({
			content: {
				url: POWER_PREVIEW_URI,
				method: 'post',
				data: {
					id: CHAR_ID,
					p_id: id
				}
			},
			position: { 
				adjust: { 
					screen: true
				},
				corner: {
					target: 'leftMiddle',
					tooltip: 'topRight'
				}
			},
			show: {
				solo: true,
				when: {
					event: 'click'
				}
			},
			hide: {
				when: {
					event: 'click'
				}
			},
			style: {
				width: 308,
				padding: 0,
				background: '#FFFFFF',
				border: { 
					width: 1,
					radius: 3
				}
			}
		}); // End qTip()
	}); //end each()
}); //end ready()
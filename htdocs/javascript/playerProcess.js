var PLAYER_PROCESS = SITE_URL+"/ajax/playerProcess.php";
var POWER_PREVIEW_URI = SITE_URL+'/ajax/power.php';
var STATUS_DEAD = 'Dead';
var STATUS_UNCONSCIOUS = 'Unconscious';
var STATUS_BLOODIED = 'Bloodied';

var notes_tmp = '';

function playerProcessRequest(action, args) {
	$.ajax({
		url: PLAYER_PROCESS,
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

//updateTempHealth(info[1]);
function parseProcessResult(data, textStatus) {
	$('#surge_bonus,#damage_value,#health').val('');
	
	for(var k in data) {
		switch(k) {
			case 'player_notes':
				$('#player_notes').val(data[k]);
				$('#notes_dirty').fadeOut();
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

function togglePower(pID, action) {
	action = 'refresh' == action ? action : 'use';	
	$.post(PLAYER_PROCESS,
		{
			id: CHAR_ID,
			p_id: pID,
			action: action+'Power'
		},
		function(data) {
			var response = data.split(MESSSAGE_DELIMITER);
			var result = response[0];
			
			if( PROCESS_FAILURE != result ) {
				animatePower(pID, action);
			}
			
			if( response.length > 1 ) {
				printMessage(new Array(response[1],response[2]));
			}
		}
	);
}

function animatePower(pID, action) {
	var rowID = '#i'+pID;
	var hideIcon, showIcon, rowOpacity;
	if( 'refresh' == action ) {
		hideIcon = '#r'+pID;
		showIcon = '#u'+pID;
		rowOpacity = 1;
	}
	else {
		action = 'use';
		hideIcon = '#u'+pID;
		showIcon = '#r'+pID;
		rowOpacity = .5;
	}
	
	$(hideIcon+':visible').fadeOut('fast', function() {
		$(rowID).fadeTo('fast', rowOpacity, function() {
			$(showIcon).fadeIn('fast');
		});
	});
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
	var notes_cur = $('#player_notes').val();
	if( notes_tmp != notes_cur ) $('#notes_dirty').fadeIn();
	else $('#notes_dirty').fadeOut();
}

$(window).load(function() {
	// Preload Images
	var img1 = $('<img />').attr('src', MEDIA_URL+'/images/icon_refresh.png');
	var img2 = $('<img />').attr('src', MEDIA_URL+'/images/icon_use.png');
	
	
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
	$('#PowerTable img.power_use').click(function() { 
		togglePower(this.id.substring(1), 'use'); });
	$('#PowerTable img.power_refresh').click(function() { 
		togglePower(this.id.substring(1), 'refresh'); });
	
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
			},
			api: {
				//beforeShow: function() { $('.power_view').qtip('hide'); }
			}
		}); // End qTip()
	}); //end each()
}); //end ready()
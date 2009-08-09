var POWER_URL = SITE_URL+'/ajax/power.php';

jQuery.fn.sort = function() {
	return this.pushStack( [].sort.apply( this, arguments ), []);
}

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

function sortByUsage(a, b) {
	var a_usage = $(a).find('span.use_type').text();
	var a_level = $(a).find('span.level').text();
	var a_name = $(a).find('span.power_name').text();
	
	var b_usage = $(b).find('span.use_type').text();
	var b_level = $(b).find('span.level').text();
	var b_name = $(b).find('span.power_name').text();
	
	if( a_usage == b_usage ) {
		if( a_level == b_level ) {
			return a_name > b_name ? 1 : -1;
		}
		else {
			return a_level > b_level ? 1 : -1;
		}
	}
	else {
		return a_usage > b_usage ? 1 : -1;
	}
	return $(a).find('span.use_type').text() > $(b).find('span.use_type').text() ? 1 : -1;
}
function sortByLevel(a, b) {
	var a_usage = $(a).find('span.use_type').text();
	var a_level = $(a).find('span.level').text();
	var a_name = $(a).find('span.power_name').text();
	
	var b_usage = $(b).find('span.use_type').text();
	var b_level = $(b).find('span.level').text();
	var b_name = $(b).find('span.power_name').text();
	
	if( a_level == b_level ) {
		if( a_usage == b_usage ) {
			return a_name > b_name ? 1 : -1;
		}
		else {
			return a_usage > b_usage ? 1 : -1;
		}
	}
	else {
		return a_level > b_level ? 1 : -1;
	}	
	
	return $(a).find('span.level').text() > $(b).find('span.level').text() ? 1 : -1;
}
function sortByName(a, b) {
	return $(a).find('span.power_name').text() > $(b).find('span.power_name').text() ? 1 : -1;
}

function sortPowerList(sortBy) {
	switch(sortBy) {
		case 'power_name':
			$('li.powerItem').sort(sortByName).appendTo('ul.list');
			break;
		case 'level':
			$('li.powerItem').sort(sortByLevel).appendTo('ul.list');
			break;
		case 'use_type':
			$('li.powerItem').sort(sortByUsage).appendTo('ul.list');
			break;
	}
}

$(document).ready(function() {
	$('.power_preview').click(function() { previewPower(this.id) });	
	$('.sort').click(function() { sortPowerList(this.id) });
});
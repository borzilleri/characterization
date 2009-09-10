var POWER_URL = SITE_URL+'/ajax/power.php';

jQuery.fn.sort = function() {
	return this.pushStack( [].sort.apply( this, arguments ), []);
}

function getSortableUsage(usage) {
	switch(usage) {
		case 'At-Will':
			return '1_'+usage;
			break;
		case 'Encounter':
			return '2_'+usage;
			break;
		case 'Daily':
			return '3_'+usage;
			break;
		default:
			return usage;
			break;
	}
}

function sortByUsage(a, b, asc) {
	var a_usage = getSortableUsage($(a).find('span.use_type').text());
	var a_level = parseInt($(a).find('span.level').text());
	var a_name = $(a).find('span.power_name').text();
	
	var b_usage = getSortableUsage($(b).find('span.use_type').text());
	var b_level = parseInt($(b).find('span.level').text());
	var b_name = $(b).find('span.power_name').text();
	
	if( a_usage == b_usage ) {
		if( a_level == b_level ) {
			if( asc ) return a_name > b_name ? 1 : -1;
			else return a_name < b_name ? 1 : -1;
		}
		else {
			if( asc ) return a_level > b_level ? 1 : -1;
			else return a_level < b_level ? 1 : -1;
		}
	}
	else {
		if( asc ) return a_usage > b_usage ? 1 : -1;
		else return a_usage < b_usage ? 1 : -1;
	}
}
function sortByUsageAsc(a, b) {
	return sortByUsage(a, b, true);
}
function sortByUsageDesc(a, b) {
	return sortByUsage(a, b, false);
}


function sortByLevel(a, b, asc) {
	var a_usage = getSortableUsage($(a).find('span.use_type').text());
	var a_level = parseInt($(a).find('span.level').text());
	var a_name = $(a).find('span.power_name').text();
	
	var b_usage = getSortableUsage($(b).find('span.use_type').text());
	var b_level = parseInt($(b).find('span.level').text());
	var b_name = $(b).find('span.power_name').text();
	
	if( a_level == b_level ) {
		if( a_usage == b_usage ) {
			if( asc ) return a_name > b_name ? 1 : -1;
			else return a_name < b_name ? 1 : -1;
		}
		else {
			if( asc ) return a_usage > b_usage ? 1 : -1;
			else return a_usage < b_usage ? 1 : -1;
		}
	}
	else {
		if( asc ) return a_level > b_level ? 1 : -1;
		else return a_level < b_level ? 1 : -1;		
	}	
}
function sortByLevelAsc(a, b) {
	return sortByLevel(a, b, true);
}
function sortByLevelDesc(a, b) {
	return sortByLevel(a, b, false);
}

function sortByName(a, b, asc) {
	var a_name = $(a).find('span.power_name').text();
	var b_name = $(b).find('span.power_name').text();
	if( asc ) return a_name > b_name ? 1 : -1;
	else return a_name < b_name ? 1 : -1
}
function sortByNameAsc(a, b) {
	return sortByName(a, b, true);
}
function sortByNameDesc(a, b) {
	return sortByName(a, b, false);
}

function updateSortLinks(sortBy, asc) {
	// Name sort link
	if( 'level' == sortBy || 'use_type' == sortBy ) {
		$('#power_name').removeClass('desc').addClass('asc');
		$('#name_arr').html('');
	}
	else if( 'power_name' == sortBy ) {
		if( asc ) {
			$('#power_name').removeClass('asc').addClass('desc');
			$('#name_arr').html('&uarr;');
		}
		else {
			$('#power_name').removeClass('desc').addClass('asc');
			$('#name_arr').html('&darr;');
		}
	}
	
	// Level Sort link
	if( 'power_name' == sortBy || 'use_type' == sortBy ) {
		$('#level').removeClass('desc').addClass('asc');
		$('#level_arr').html('');
	}
	else if( 'level' == sortBy ) {
		if( asc ) {
			$('#level').removeClass('asc').addClass('desc');
			$('#level_arr').html('&uarr;');
		}
		else {
			$('#level').removeClass('desc').addClass('asc');
			$('#level_arr').html('&darr;');
		}
	}
	
	// Usage Sort Link
	if( 'power_name' == sortBy || 'level' == sortBy ) {
		$('#use_type').removeClass('desc').addClass('asc');
		$('#usage_arr').html('');
	}
	else if( 'use_type' == sortBy ) {
		if( asc ) {
			$('#use_type').removeClass('asc').addClass('desc');
			$('#usage_arr').html('&uarr;');
		}
		else {
			$('#use_type').removeClass('desc').addClass('asc');
			$('#usage_arr').html('&darr;');
		}
	}
}

function sortPowerList(link) {
	var sortBy = link.id;
	var asc = $(link).hasClass('asc');
	
	switch(sortBy) {
		case 'power_name':
			if( asc ) $('li.powerItem').sort(sortByNameAsc).appendTo('ul.list');
			else $('li.powerItem').sort(sortByNameDesc).appendTo('ul.list');
			updateSortLinks(sortBy, asc)
			break;
		case 'level':
			if( asc ) $('li.powerItem').sort(sortByLevelAsc).appendTo('ul.list');
			else $('li.powerItem').sort(sortByLevelDesc).appendTo('ul.list');
			updateSortLinks(sortBy, asc)
			break;
		case 'use_type':
			if( asc ) $('li.powerItem').sort(sortByUsageAsc).appendTo('ul.list');
			else $('li.powerItem').sort(sortByUsageDesc).appendTo('ul.list');
			updateSortLinks(sortBy, asc)
			break;
	}
	
	var i = 1;
	$('li.powerItem').removeClass('row0');
	$('li.powerItem').removeClass('row1');

	$('li.powerItem').each(function() {
		var rowColor = "row"+i;
		i = (i+1)%2;
		$(this).addClass(rowColor);
	});
}

$(window).load(function() {
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
			},
			show: {
				solo: true,
				when: {
					event: 'click'
				}
			},
			hide: {
				when: {
					event: 'unfocus'
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

	$('.sort').click(function() { sortPowerList(this) });
});
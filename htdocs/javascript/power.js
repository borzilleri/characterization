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
		case 'Healing-Surge':
			return '4_'+usage;
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
		$('#name_arrow').html('&nbsp;');
	}
	else if( 'power_name' == sortBy ) {
		if( asc ) {
			$('#power_name').removeClass('asc').addClass('desc');
			$('#name_arrow').html('&uarr;');
		}
		else {
			$('#power_name').removeClass('desc').addClass('asc');
			$('#name_arrow').html('&darr;');
		}
	}
	
	// Level Sort link
	if( 'power_name' == sortBy || 'use_type' == sortBy ) {
		$('#level').removeClass('desc').addClass('asc');
		$('#level_arrow').html('&nbsp;');
	}
	else if( 'level' == sortBy ) {
		if( asc ) {
			$('#level').removeClass('asc').addClass('desc');
			$('#level_arrow').html('&uarr;');
		}
		else {
			$('#level').removeClass('desc').addClass('asc');
			$('#level_arrow').html('&darr;');
		}
	}
	
	// Usage Sort Link
	if( 'power_name' == sortBy || 'level' == sortBy ) {
		$('#use_type').removeClass('desc').addClass('asc');
		$('#usage_arrow').html('&nbsp;');
	}
	else if( 'use_type' == sortBy ) {
		if( asc ) {
			$('#use_type').removeClass('asc').addClass('desc');
			$('#usage_arrow').html('&uarr;');
		}
		else {
			$('#use_type').removeClass('desc').addClass('asc');
			$('#usage_arrow').html('&darr;');
		}
	}
}

function sortPowerList(link) {
	var sortBy = link.id;
	var asc = $(link).hasClass('asc');
	
	switch(sortBy) {
		case 'power_name':
			if( asc ) $('#PowerList tr.powerItem').sort(sortByNameAsc).appendTo('#PowerList tbody');
			else $('#PowerList tr.powerItem').sort(sortByNameDesc).appendTo('#PowerList tbody');
			updateSortLinks(sortBy, asc)
			break;
		case 'level':
			if( asc ) $('#PowerList tr.powerItem').sort(sortByLevelAsc).appendTo('#PowerList tbody');
			else $('#PowerList tr.powerItem').sort(sortByLevelDesc).appendTo('#PowerList tbody');
			updateSortLinks(sortBy, asc)
			break;
		case 'use_type':
			if( asc ) $('#PowerList tr.powerItem').sort(sortByUsageAsc).appendTo('#PowerList tbody');
			else $('#PowerList tr.powerItem').sort(sortByUsageDesc).appendTo('#PowerList tbody');
			updateSortLinks(sortBy, asc)
			break;
	}
	
	var i = 1;
	$('#PowerList tr.powerItem').removeClass('row0');
	$('#PowerList tr.powerItem').removeClass('row1');

	$('#PowerList tr.powerItem').each(function() {
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
function editSkill(id) {
	id = id.substring(5);
	var tr = $('#s'+id);	
	var form = $('#blankFormRow');
	
	$('#SkillForm #name').val($(tr).find('td.skillName').text());
	$('#SkillForm #bonus').val($(tr).find('td.skillBonus').text());
	$('#SkillForm #ability').val($(tr).find('td.skillAbility span.skillAbilityName').text());
	$('#SkillForm #trained').attr('checked', $(tr).find('td.skillTrained').text()==5);
	$('#SkillForm #form_key').val($('#SkillForm #form_key').val().substring(0,11)+id);
	$('#s_id').val(id);	
}

function editFeat(id) {
	id = id.substring(5);
	var tr = $('#f'+id);

	$('#feat_id').val(id);
	$('#feat_name').val($(tr).find('td.featName').text());
	$('#feat_description').text($(tr).find('td.featDescription img').attr('title'));	
}

$(window).load(function() {
	$('#SkillTable a.skillEditLink').click(function() { editSkill(this.id); });
	$('#NewSkillButton').click(function() {
		$('#SkillForm #name').val('');
		$('#SkillForm #bonus').val('');
		$('#SkillForm #ability').val('');
		$('#SkillForm #trained').attr('checked',false);
		$('#SkillForm #form_key').val($('#SkillForm #form_key').val().substring(0,11)+'0');
		$('#s_id').val('');
	});
	
	$('#FeatTable a.featEditLink').click(function() { editFeat(this.id); });
	$('#NewFeatButton').click(function() { 
		$('#feat_id').val(''); 
		$('#feat_name').val('');
		$('#feat_description').text('');
	});

	$('img.feat_view').each(function(i) {
		$(this).qtip({
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
			}
		});
	});
});
function editSkill(id) {
	id = id.substring(1);
	var tr = $('#s'+id);	
	var form = $('#blankFormRow');
	
	$(form).find('td.skillName input').val($(tr).find('td.skillName').text());
	$(form).find('td.skillBonus input').val($(tr).find('td.skillBonus').text());
	$(form).find('td.skillAbility select').val($(tr).find('td.skillAbility span.skillAbilityName').text());
	$(form).find('td.skillTrained input').attr('checked', $(tr).find('td.skillTrained').text()==5);
	$('#s_id').val(id);	
}

$(window).load(function() {
	$('#SkillTable a.skillEditLink').click(function() { editSkill(this.id); });			
});
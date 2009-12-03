function toggleWeaponOffFields(enable) {
	$('#weapon_off_name_label,#weapon_off_bonus_label,#weapon_off_attack_label,#weapon_off_damage_label,#weapon_off_dice_label')
		.toggleClass('disabled',!enable);
		
	if( enable ) {
		$('#weapon_off_name,#weapon_off_attack,#weapon_off_damage,#weapon_off_dice').
			removeAttr('disabled');
	}
	else {
		$('#weapon_off_name,#weapon_off_attack,#weapon_off_damage,#weapon_off_dice').
			attr('disabled','disabled');
	}
}

function toggleChargeFields(enable) {
	if( enable ) {
		$('#charges,#charge_type').removeAttr('disabled');
	}
	else {
		$('#charges,#charge_type').attr('disabled','disabled');		
	}
}
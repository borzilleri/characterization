define([
	'collections/characters',
],
function(Character_Collection) {
	return Backbone.Model.extend({
		validation: {
			name: { required: true },
			level: { range: [1,30] },
			raceName: { required: true },
			className: { required: true},
			classSurges: { min: 1 },
			classHPFirst: { min: 1},
			classHPLevel: { min: 1},
			classFortBonus: { min: 0 },
			classRefBonus: { min: 0 },
			classWillBonus: { min: 0 },
			str: { min: 1 },
			con: { min: 1 },
			dex: { min: 1 },
			int: { min: 1 },
			wis: { min: 1 },
			cha: { min: 1 },
		},
		initialize: function() {
			_(this).bindAll();
			this.on('change:name', this.setSlug);
		},
		setSlug: function() {
			var slug = this.get('name')
				.replace(/[^-a-zA-Z0-9,&\s]+/ig,'')
				.replace(/-/gi,'_')
				.replace(/\s/gi,'-');
			this.set({slug:slug});
		}
	});
});

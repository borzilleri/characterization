define([
	'collections/characters',
],
function(Character_Collection) {
	return Backbone.Model.extend({
		defaults: {
			baseSpeed: 6,
			saveBonus: 0,
			currentHP: 0,
			currentSurges: 0,
			surgeValue: 0
		},
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

			// AC Handling
			this.on('change:dex', this.setAC);
			this.on('change:int', this.setAC);
			this.on('change:level', this.setAC);
			this.on('stat-change:ac', this.setAC);
			// Fort Handling
			this.on('change:str', this.setFort);
			this.on('change:con', this.setFort);
			this.on('change:level', this.setFort);
			this.on('change:classFortBonus', this.setFort);
			this.on('stat-change:fort', this.setFort);
			// Reflex Handling
			this.on('change:dex', this.setFort);
			this.on('change:int', this.setFort);
			this.on('change:level', this.setFort);
			this.on('change:classRefBonus', this.setFort);
			this.on('stat-change:ref', this.setFort);
			// Will Handling
			this.on('change:wis', this.setFort);
			this.on('change:cha', this.setFort);
			this.on('change:level', this.setFort);
			this.on('change:classWillBonus', this.setFort);
			this.on('stat-change:will', this.setFort);

			// HP
			this.on('change:con', this.setHP);
			this.on('change:classHPFirst', this.setHP);
			this.on('change:classHPLevel', this.setHP);
			this.on('change:level', this.setHP);
			this.on('stat-change:hp', this.setHP);

			// Surge Value
			this.on('stat-change:surgeValue', this.setSurgeValue);

			// Surges
			this.on('change:con', this.setSurges);
			this.on('change:classSurges', this.setSurges);
			this.on('stat-change:surges', this.setSurges);

			// Initiative
			this.on('change:dex', this.setInitiative);
			this.on('change:level', this.setInitiative);
			this.on('stat-change:init', this.setInitiative);

			// Speed
			this.on('stat-change:speed', this.setSpeed);
		},
		getLevelBonus: function() {
			return 10 + Math.floor(this.get('level')/2);
		},
		getStatBonus: function(stat) {
			return Math.floor(this.get(stat)/2)-5;
		},
		setSlug: function() {
			var slug = this.get('name')
				.replace(/[^-a-zA-Z0-9,&\s]+/ig,'')
				.replace(/-/gi,'_')
				.replace(/\s/gi,'-');
			this.set({slug:slug});
		},
		setAC: function() {
			var armor = this.getLevelBonus() +
				Math.max(this.getStatBonus('dex'),this.getStatBonus('int'));
			// ^ Only if Light armor --> check that?
			// add ac effects
			// add all def effects
			this.set({ac: armor});
		},
		setFort: function() {
			var fort = this.getLevelBonus() + this.get('classFortBonus') +
				Math.max(this.getStatBonus('str'),this.getStatBonus('con'));
			// all fort def bonuses
			this.set({fort:fort});
		},
		setReflex: function() {
			var ref = this.getLevelBonus() + this.get('classRefBonus') +
				Math.max(this.getStatBonus('dex'),this.getStatBonus('int'));
			// all ref def bonuses
			this.set({ref:ref});
		},
		setWill: function() {
			var will = this.getLevelBonus() + this.get('classWillBonus') +
				Math.max(this.getStatBonus('wis'),this.getStatBonus('cha'));
			// all will def bonuses
			this.set({will:will});
		},
		setHP: function() {
			var hp = this.get('classHPFirst') + this.get('con') +
							 (this.get('level') * this.get('classHPLevel'));
			// all hp bonuses
			this.set({hp:hp});
			this.setSurgeValue(hp);
		},
		setSurgeValue: function() {
			var surgeValue = Math.floor(this.get('hp')/4);
			// add surge value bonuses
			this.set({surgeValue:surgeValue});
		},
		setSurges: function() {
			var surges = this.get('classSurges') + this.getStatBonus('con');
			// all surge num bonuses
			this.set({surges:surges});
		},
		setInitiative: function() {
			var init = this.getLevelBonus() + this.getStatBonus('dex');
			// init bonuses;
			this.set({init:init});
		},
		setSpeed: function() {
			var speed = this.get('baseSpeed');
			// speed effects
			// Maybe check for heavy armor?
			this.set({speed:speed});
		}
	});
});

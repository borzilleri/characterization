define([
	'tpl!template/characterConsole.html',
	// Console sub views
	'views/console/stats',
	'views/console/skills',
	'views/console/powers',

	'bootstrap/dropdown',
	'bootstrap/modal'
],
function(Page_Tpl,
	Stats, Skills, Powers
) {
	return Backbone.View.extend({
		character: null,
		collection: null,
		subViewNames: [
			'stats','skills','powers',
		],
		subViewObjects: {},
		events: {
		},
		initialize:function() {
			var self = this;
			_(this).bindAll();
			this.character = this.options.character;
			this.collection = this.options.collection;
			this.render();

			_(this.subViewNames).each(function(name) {
				require(['views/console/'+name], function(View) {
					self.subViewObjects[name] = new View({
						el: self.$('#character-'+name),
						character: self.character
					});
				});
			});
		},
		render: function() {
			var self = this;
			$(this.el).html(Page_Tpl({
				chars: this.collection.toJSON(),
				data: this.character.toJSON()
			}));
			return this;
		}
	});
});

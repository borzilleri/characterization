define([
	'collections/characters',
	'tpl!template/page.html',
	'bootstrap/dropdown',
	'backbone'
],
function(Character_Collection, Page_Tpl) {
	return Backbone.View.extend({
		characters: null,
		bodyView: null,
		el: $('body'),
		events: {
			'route:charCreate': 'createCharacter',
		},
		initialize: function() {
			_(this).bindAll();
			var self = this;

			this.render();
			this.characters = new Character_Collection();
			this.characters.fetch({
				success: function(collection, resp) {
					if( 0 >= collection.length ) {
						// Display character creation screen
						self.createCharacter();
					}
					else if( 1 === collection.lenth ) {
						// Display the single character
					}
					else {
						// Display a list of characters to load
					}
				},
				error: function(collection, resp) {
					// fatal error
				}
			});
		},
		createCharacter: function() {
			var self = this;
			if( this.bodyView ) {
				this.bodyView.remove();
				delete this.bodyView;
			}
			require(['views/createCharacter'], function(View) {
				self.bodyView = new View({
					el: this.$('.page-content')
				});
				self.bodyView.render();
			});

		},
		render: function() {
			$(this.el).html(Page_Tpl());
		}
	});
});

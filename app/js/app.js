define([
	'libs/backbone.modelbinding',
	'backbone'
],
function(ModelBinding) {
	return Backbone.Router.extend({
		characters: null,
		pageView: null,
		routes: {
			'character/create': 'createCharacter',
			'character/list': 'listCharacters',
			'character/:name': 'loadCharacter',
			'*path': 'defaultRoute'
		},
		initialize: function(options) {
			_(this).bindAll();
			ModelBinding.Configuration.configureAllBindingAttributes("name");
			this.characters = options.characters;
			Backbone.history.start();
		},
		loadPageView: function(viewPath, params) {
			var self = this;
			require([viewPath], function(View) {
				if( self.pageView ) {
					self.pageView.remove();
					delete self.pageView;
					// The pageView is always bound to the body,
					// so removing it actually removes the body element.
					// Here, we recreate it and add it to the document
				}
				if( $('body').length === 0 ) {
					$('<body></body>').appendTo('html');
				}
				params.el = $('body');
				self.pageView = new View(params);
			});
		},
		createCharacter: function() {
			this.loadPageView('views/createCharacter', {
					collection: self.characters
			});
		},
		loadCharacter: function(name) {
			var self = this
			var model = this.characters.find(function(m) {
				return name === m.get('slug');
			})
			if( model ) {
				this.loadPageView('views/characterConsole', {
						character: model
				});
			}
			else {
				alert('char not found');
				// 404 - List Chars?
			}
		},
		listCharacters: function() {
			alert('listing characters');
		},
		defaultRoute: function(charName) {
			if( 0 === this.characters.length ) {
				this.createCharacter();
			}
			else if( 1 === this.characters.length ) {
				this.loadCharacter(this.characters.at(0).get('slug'));
			}
			else {
				this.listCharacters();
			}
		}
	});
});

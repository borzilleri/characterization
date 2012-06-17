define([
	'libs/backbone.modelbinding',
	'backbone'
],
function(ModelBinding) {
	return Backbone.Router.extend({
		characters: null,
		pageId: 'page-content',
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
				if( $('#'+self.pageId).length === 0 ) {
					$('<div id="'+self.pageId+'"></body>').appendTo('body');
				}
				params.el = $('#'+self.pageId);
				self.pageView = new View(params);
			});
		},
		createCharacter: function() {
			this.loadPageView('views/createCharacter', {
					collection: this.characters
			});
		},
		loadCharacter: function(name) {
			var model = this.characters.find(function(m) {
				return name === m.get('slug');
			})
			if( model ) {
				this.loadPageView('views/characterConsole', {
					character: model,
					collection: this.characters
				});
			}
			else {
				alert('char not found');
				// 404 - List Chars?
			}
		},
		listCharacters: function() {
			this.loadPageView('views/characterList', {
				collection: this.characters
			});
		},
		defaultRoute: function(charName) {
			if( 0 === this.characters.length ) {
				this.createCharacter();
			}
			else if( 1 === this.characters.length ) {
				this.navigate('character/'+this.characters.at(0).get('slug'),
					{trigger: true, replace: true});
				//this.loadCharacter(this.characters.at(0).get('slug'));
			}
			else {
				this.listCharacters();
			}
		}
	});
});

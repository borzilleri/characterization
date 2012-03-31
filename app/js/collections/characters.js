define([
	'models/character',
	'backbone',
	'libs/backbone.localStorage'
],
function(Character_Model) {
	return Backbone.Collection.extend({
		localStorage: new Store('Characters'),
		model: Character_Model
	});
})

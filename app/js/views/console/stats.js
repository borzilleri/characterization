define([
	'tpl!template/console/stats.html',
],
function(Template) {
	return Backbone.View.extend({
		character: null,
		events: {},
		initialize: function() {
			_(this).bindAll();
			this.character = this.options.character;
			this.render();
		},
		render: function() {
			$(this.el).html(Template({
				data: this.character.toJSON()
			}));
			return this;
		}
	});
});

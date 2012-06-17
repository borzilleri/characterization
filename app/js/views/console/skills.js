define([
	'tpl!template/console/skills.html',
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
			}));
			return this;
		}
	});
});

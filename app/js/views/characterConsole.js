define([
	'tpl!template/characterConsole.html'
],
function(Page_Tpl) {
	return Backbone.View.extend({
		character: null,
		loading: false,
		events: {
		},
		initialize:function() {
			_(this).bindAll();
			this.character = this.options.character;
			this.render();
		},
		render: function() {
			$(this.el).html(Page_Tpl());
			return this;
		}
	});
});

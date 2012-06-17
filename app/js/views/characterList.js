define([
	'tpl!template/characterList.html'
],
function(Page_Tpl) {
	return Backbone.View.extend({
		collection: null,
		events: {
			'click .delete-character': 'deleteCharacter',
		},
		initialize: function() {
			this.collection = this.options.collection;
			this.render();
		},
		deleteCharacter: function(e) {
			var self = this,
					model = this.collection.get($(e.target).data('id'));

			// TODO improve this confirmation?
			if( confirm("Really delete character: "+model.get('name')) ) {
				model.destroy({
					wait: true,
					success: function() {
						self.render();
					},
					error: function() {
						alert('error deleting character');
					}
				});
			}
		},
		render: function() {
			$(this.el).html(Page_Tpl({
				chars: this.collection.toJSON()
			}));
			return this;
		}
	});
});

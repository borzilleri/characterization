require([
	'collections/characters',
	'app'
],
function(Char_Collecion, Router) {
	$(function() {
		var chars = new Char_Collecion();
		chars.fetch({
			success: function(coll,resp) {
				window.app = new Router({
					characters: coll
				});
			},
			error: function(coll,resp) {
				alert('Error fetching character colltion.');
			}
		});
	});
});


$(document).ready(function() { 
	$("#submit-delete-album").on('click', function(e) {
		if (!confirm('Are you sure you want to delete this album?')) {
			e.preventDefault();
		}
	});

	$("#submit-delete-image").on('click', function(e) {
		if (!confirm('Are you sure you want to delete this image?')) {
			e.preventDefault();
		}
	});
});

jQuery(function() {
	jQuery('.nested-archive > li:not(:first-child) > ul').hide();

	jQuery('.nested-archive > li > a').on('click', function(event) {
		event.preventDefault();
		jQuery(this).siblings('ul').slideToggle(200);
	});
});
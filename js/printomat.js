/*!
 * Print-O-Matic JavaScript v2.0
 * http://plugins.twinpictures.de/plugins/print-o-matic/
*/

jQuery(document).ready(function() {
	jQuery('.printomatic, .printomatictext').click(function() {
		var id = jQuery(this).attr('id');
		if ( 'pom_do_not_print' in print_data[id] && print_data[id]['pom_do_not_print'] ){
				jQuery(print_data[id]['pom_do_not_print']).addClass('pe-no-print');
		}

		jQuery(this).data('print_target');
		var target = jQuery(this).data('print_target');
		if (target == '%prev%') {
			target = jQuery(this).prev();
		}
		if (target == '%next%') {
			target = jQuery(this).next();
		}
		var target_arr = target.split(", ");
		var targets = [];
		jQuery.each( target_arr, function( key, value ) {
			targets.push(jQuery(value)[0]);
		});
		PrintElements.print(targets);
	});
});

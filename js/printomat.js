/*!
 * Print-O-Matic JavaScript v2.0
 * http://plugins.twinpictures.de/plugins/print-o-matic/
*/

jQuery(document).ready(function() {
	jQuery('.printomatic, .printomatictext').click(function() {
		var id = jQuery(this).attr('id');
		var pause_time = 0;
		if('pom_pause_time' in print_data[id] && print_data[id]['pom_pause_time'] > 0){
        pause_time = print_data[id]['pom_pause_time'];
    }

		if ( 'pom_do_not_print' in print_data[id] && print_data[id]['pom_do_not_print'] ){
				jQuery(print_data[id]['pom_do_not_print']).addClass('pe-no-print');
		}

		jQuery(this).data('print_target');
		var trigger = jQuery(this);
		var target = trigger.data('print_target');
		var target_arr = target.split(", ");
		var targets = [];
		var targ;
		jQuery.each( target_arr, function( key, value ) {
			if (value == '%prev%') {
				targ = trigger.prev();
			}
			else if (target == '%next%') {
				targ = trigger.next();
			}
			else{
				targ = jQuery(value);
			}
			targets.push(targ[0]);
		});
		PrintElements.print(targets, pause_time);
	});
});

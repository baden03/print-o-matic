/*!
 * Print-O-Matic JavaScript v2.0.3
 * http://plugins.twinpictures.de/plugins/print-o-matic/
*/

jQuery(document).ready(function() {
	jQuery(document).on( 'click', '.printomatic, .printomatictext', function() {
		var id = jQuery(this).attr('id');

		var pause_time = 0;
		if(typeof print_data !== 'undefined' && 'pom_pause_time' in print_data[id] && print_data[id]['pom_pause_time'] > 0){
			pause_time = print_data[id]['pom_pause_time'];
		}

		if (typeof print_data !== 'undefined' && 'pom_do_not_print' in print_data[id] && print_data[id]['pom_do_not_print'] ){
				jQuery(print_data[id]['pom_do_not_print']).addClass('pe-no-print');
		}

		//add any html top or bottom
		var has_top_html = false;
		if (typeof print_data !== 'undefined' && 'pom_html_top' in print_data[id] && print_data[id]['pom_html_top']){
			  jQuery( 'body' ).prepend( '<div id="pom_top_html" class="pe-preserve-ancestor">' + print_data[id]['pom_html_top'] + '</div>' );
				has_top_html = true;
		}
		var has_bot_html = false;
		if (typeof print_data !== 'undefined' && 'pom_html_bottom' in print_data[id] && print_data[id]['pom_html_bottom']){
			  jQuery( 'body' ).append( '<div id="pom_bot_html" class="pe-preserve-ancestor">' + print_data[id]['pom_html_bottom'] + '</div>' );
				has_bot_html = true;
		}

		var trigger = jQuery(this);
		var target = trigger.data('print_target');
	
		if(!target){
			classes = trigger.attr("class").split(/\s+/);
			for(i=0; i<classes.length; i++){
				if(classes[i].substring(0, 12) == "printtarget-"){
					target = classes[i].substring(12, classes[i].length);
				}
			}
		}
		if(!target || !jQuery(target).length ){
			return;
		}

		var target_arr = target.split(", ");
		var targets = [];
		var targ;
		jQuery.each( target_arr, function( key, value ) {
			if (value == '%prev%') {
				targ = trigger.prev();
			}
			else if (value == '%next%') {
				targ = trigger.next();
			}
			else{
				targ = jQuery(value);
			}
			//only add target if found on page
			if(targ.length){
				targets.push(targ[0]);
			}
			
		});


    	setTimeout(function () {
			if(targets){
				PrintElements.print(targets);
			}
			
			if ( has_top_html ){
					jQuery( '#pom_top_html' ).remove();
			}
			if ( has_bot_html ){
					jQuery( '#pom_bot_html' ).remove();
			}
		}, pause_time);

	});
});

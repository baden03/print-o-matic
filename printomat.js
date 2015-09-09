/*!
 * Print-O-Matic JavaScript v1.6.4
 * http://plugins.twinpictures.de/plugins/print-o-matic/
 *
 * Copyright 2015, Twinpictures
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, blend, trade,
 * bake, hack, scramble, difiburlate, digest and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
* detect IE
* returns version of IE or false, if browser is not Internet Explorer
*/
function detectIE() {
	var ua = window.navigator.userAgent;

	var msie = ua.indexOf('MSIE ');
	if (msie > 0) {
		// IE 10 or older => return version number
		return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
	}

	var trident = ua.indexOf('Trident/');
	if (trident > 0) {
		// IE 11 => return version number
		var rv = ua.indexOf('rv:');
		return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
	}

	var edge = ua.indexOf('Edge/');
	if (edge > 0) {
	// IE 12 => return version number
	return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
	}

	// other browser
	return false;
}

jQuery(document).ready(function() {
	jQuery('.printomatic, .printomatictext').click(function() {
		var id = jQuery(this).attr('id');
		//var target = jQuery('#target-' + id).val();
		var target = jQuery(this).attr('data-print_target');
		if(!target){
			target = jQuery('#target-' + id).val();
		}
		if (target == '%prev%') {
			target = jQuery(this).prev();
		}
		if (target == '%next%') {
			target = jQuery(this).next();
		}

		var w = window.open( "", "printomatic", "scrollbars=yes");
		w.document.write("<!DOCTYPE html><html><head></head><body></body></html>");

		//title
		//rot in hell, Internet Explorer
		if ( detectIE() ){
			w.document.title = "PrintOMatic";
		}
		else{
			jQuery(w.document.head).append("<title>"+ document.title +"</title>");
		}

		if ( typeof print_data != 'undefined' && typeof print_data[id] != 'undefined'){
			if ( 'pom_site_css' in print_data[id] ){
				jQuery(w.document.body).append('<link rel="stylesheet" type="text/css" href="' + print_data[id]['pom_site_css'] + '" />');
			}

			if ( 'pom_custom_css' in print_data[id] ){
				jQuery(w.document.body).append("<style>"+ print_data[id]['pom_custom_css'] +"</style>");
			}

			if ( 'pom_do_not_print' in print_data[id] ){
				jQuery(print_data[id]['pom_do_not_print']).hide();
			}

			if ( 'pom_html_top' in print_data[id] ){
				jQuery(w.document.body).append( print_data[id]['pom_html_top'] );
			}
		}

		//rot in hell, Internet Explorer
		if ( detectIE() ){
			//jQuery(w.document.body).append( jQuery( target ).clone().html() );
			//input text area workaound
			/*
			jQuery( target  + ' input[type=text]').each(function() {
				//it knows the value here...
				console.log( jQuery(this).val() );
			});
			*/

			jQuery(w.document.body).append( function() {
				var ieID = target.substr(1);
				var ieOutput = jQuery( w.document.createElement( 'div' ) );
				if ( target.substr(0,1) == '#' ){
					ieOutput.attr('id', ieID);
				} else if ( target.substr(0,1) == '.' ){
					ieOutput.addClass( ieID );
				}
				var clony = jQuery( target ).clone();
				return ieOutput.append( clony.html() );
			});

			//update the print version with the user entered values because IE is hacky like that
			jQuery( target  + ' input[type=text]').each(function() {
				var user_val = jQuery(this).val();
				var elem_id = jQuery(this).attr('id');
				w.document.getElementById(elem_id).value = user_val;
			});
		}
		else{
			jQuery(w.document.body).append( jQuery( target ).clone() );
		}

		//if ( typeof pom_do_not_print != 'undefined' && pom_do_not_print ) {
		if( typeof print_data != 'undefined' ){
			if ( typeof print_data[id]['pom_do_not_print'] != 'undefined' && print_data[id]['pom_do_not_print'] ){
				jQuery(print_data[id]['pom_do_not_print']).show();
			}

			//if ( typeof pom_html_bottom != 'undefined' && pom_html_bottom ){
			if ( typeof print_data[id]['pom_html_bottom'] != 'undefined' && print_data[id]['pom_html_bottom'] ){
				jQuery(w.document.body).append( print_data[id]['pom_html_bottom'] );
			}
		}

		/* hardcodeed iframe and if so, force a pause... pro version offers more options */
		iframe = jQuery(w.document).find('iframe');
		if (iframe.length && print_data[id]['pom_pause_time'] < 3000 ) {
			print_data[id]['pom_pause_time'] = 3000;
		}

		if ( typeof print_data[id]['pom_pause_time'] != 'undefined' && print_data[id]['pom_pause_time'] > 0 ){
			//console.log( print_data[id]['pom_pause_time'] );
			pause_time = w.setTimeout(printIt, print_data[id]['pom_pause_time']);
		}
		else{
			printIt();
		}

		function printIt(){
			w.print();
			w.document.close();
		}

	});

});

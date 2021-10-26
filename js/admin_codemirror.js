/*
 * * CodeMirrorJavaScript v0.2.0
*/
jQuery(document).ready(function($) {
	wp.codeEditor.initialize($('#custom_page_css'), cm_settings.ce_css);
    wp.codeEditor.initialize($('#custom_css'), cm_settings.ce_css);
    wp.codeEditor.initialize($('#html_top'), cm_settings.ce_html);
    wp.codeEditor.initialize($('#html_bottom'), cm_settings.ce_html);
})
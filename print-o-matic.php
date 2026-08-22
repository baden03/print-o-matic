<?php
/*
Plugin Name: Print-O-Matic
Text Domain: print-o-matic
Domain Path: /languages
Plugin URI: https://pluginoven.com/plugins/print-o-matic/
Description: Shortcode that adds a printer icon, allowing the user to print the post or a specified HTML element in the post.
Version: 2.1.13
Author: twinpictures
Author URI: https://twinpictures.de
License: GPL2
Requires at least: 5.0
Requires PHP: 7.4
*/

// no direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WP_Print_O_Matic
 * @package WP_Print_O_Matic
 * @category WordPress Plugins
 */
class WP_Print_O_Matic {

	var $version = '2.1.13';
	var $domain = 'printomat';
	var $options_name = 'WP_Print_O_Matic_options';
	var $options = array(
		'print_target' => 'article',
		'print_title' => '',
		'custom_page_css' => '',
		'custom_css' => '',
		'do_not_print' => '',
		'printicon' => 'true',
		'printstyle' => 'pom-default',
		'html_top' => '',
		'html_bottom' => '',
		'script_check' => '',
		'pause_time' => '',
	);

	/**
	 * PHP5 constructor
	 */
	function __construct() {
		// set option values
		$this->_set_options();

		//load the script and style if not viewing the dashboard
		add_action('wp_enqueue_scripts', array( $this, 'printMaticInit' ) );
		add_action('admin_enqueue_scripts', array( $this, 'codemirror_enqueue_scripts') );
 
		// add actions
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );

		add_shortcode( 'print-me', array($this, 'shortcode') );
		// Add shortcode support for widgets
		add_filter('widget_text', 'do_shortcode');
	}

	/**
	 * Register the bundled languages directory.
	 *
	 * Translations installed site-wide (wp-content/languages/plugins) always win;
	 * this registers ./languages as the fallback so the .l10n.php / .mo files
	 * shipped with the plugin are used when nothing else provides the locale.
	 */
	function load_textdomain() {
		load_plugin_textdomain( 'print-o-matic', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Callback init
	 */
	function printMaticInit() {
		//script
		wp_register_script('printomatic-js', plugins_url('js/printomat.js', __FILE__), array('jquery'), '2.0.14', true);
		wp_register_script('pe-js', plugins_url('js/print_elements.js', __FILE__), array('printomatic-js'), '1.1', true);
		
		//prep options for injection
		$print_data = [
			'pom_html_top' => do_shortcode(wp_kses_post($this->options['html_top'])),
			'pom_html_bottom' => do_shortcode(wp_kses_post($this->options['html_bottom'])),
			'pom_do_not_print' => sanitize_text_field($this->options['do_not_print']),
			'pom_pause_time' => absint($this->options['pause_time']),
		];
		wp_add_inline_script( 'printomatic-js', 'var print_data = ' . wp_json_encode( $print_data ) . ';', 'before' );

		//css
		wp_register_style( 'printomatic-css', plugins_url('/css/style.css', __FILE__) , array (), '2.0' );
		if( !empty( $this->options['custom_page_css'] ) ){
			wp_add_inline_style( 'printomatic-css', wp_strip_all_tags($this->options['custom_page_css']) );
		}
		if( !empty( $this->options['custom_css'] ) ){
			$print_css = "@media print {\n".wp_strip_all_tags($this->options['custom_css'])."\n}\n";
			wp_add_inline_style( 'printomatic-css', $print_css );
		}

		//load always or only when shortcode is present
		if( empty($this->options['script_check']) ){
			wp_enqueue_style( 'printomatic-css' );
			wp_enqueue_script('printomatic-js');
			wp_enqueue_script('pe-js');
		}
	}

	/**
	 * Callback admin_menu
	 */
	function admin_menu() {
		if ( function_exists( 'add_options_page' ) AND current_user_can( 'manage_options' ) ) {
			// add options page
			$page = add_options_page(
				__( 'Print-O-Matic Options', 'print-o-matic' ),
				__( 'Print-O-Matic', 'print-o-matic' ),
				'manage_options',
				'print-o-matic-options',
				array( $this, 'options_page' )
			);
		}
	}

	/**
	 * Callback admin_init
	 */
	function admin_init() {
		// register settings
		register_setting( $this->domain, $this->options_name );
	}

	// enque codemirror
	function codemirror_enqueue_scripts($hook) {
		if($hook == 'settings_page_print-o-matic-options'){
			wp_register_script('cm_js', plugins_url('js/admin_codemirror.js', __FILE__), array('jquery'), '0.2.0', true);
			$cm_settings = [
				'ce_css' => wp_enqueue_code_editor(
					[
						'type' => 'text/css',
						'codemirror' => [
							'lineNumbers' => true,
							'autoRefresh' => true
						]
					]
				),
				'ce_html' => wp_enqueue_code_editor(
					[
						'type' => 'text/html',
						'codemirror' => [
							'lineNumbers' => true,
							'autoRefresh' => true
						]
					]
				)
			];

			wp_localize_script('cm_js', 'cm_settings', $cm_settings);
			wp_enqueue_script( 'cm_js' );
			wp_enqueue_script( 'wp-theme-plugin-editor' );
			wp_enqueue_style( 'wp-codemirror' );
			wp_register_style( 'pom-admin-css', plugins_url('css/admin_style.css', __FILE__) , array (), '1.0.0' );
			wp_enqueue_style( 'pom-admin-css' );
		}
	}

	/**
	 * Callback shortcode
	 */
	function shortcode($atts, $content = null){
		$ran = rand(1, 10000);
		$options = $this->options;

		if( !empty($this->options['script_check']) ){
			wp_enqueue_style('printomatic-css');
			wp_enqueue_script('printomatic-js');
			wp_enqueue_script('pe-js');
		}

		$atts = shortcode_atts(array(
			'id' => 'id'.$ran,
			'class' => '',
			'tag' => 'div',
			'alt' => '',
			'target' => $options['print_target'],
			'do_not_print' => '',
			'printicon' => $options['printicon'],
			'printstyle' => $options['printstyle'],
			'html_top' => '',
			'html_bottom' => '',
			'pause_before_print' => '',
			'title' => $options['print_title'],
		), $atts);

		// Sanitize the shortcode attributes
		// the id doubles as a JS object key in the inline script below, so restrict
		// it to safe identifier characters and fall back to a generated id
		$id = preg_replace( '/[^A-Za-z0-9_-]/', '', $atts['id'] );
		if( empty($id) ){
			$id = 'id'.$ran;
		}
		$class = sanitize_html_class($atts['class']);

		// only allow a safe, known set of wrapper tags
		$tag = strtolower( tag_escape($atts['tag']) );
		$allowed_tags = array( 'div', 'span', 'p', 'a', 'button', 'i', 'em', 'strong', 'li' );
		if( !in_array( $tag, $allowed_tags, true ) ){
			$tag = 'div';
		}
		$alt = esc_attr($atts['alt']);
		$target = esc_attr($atts['target']);
		$do_not_print = esc_attr($atts['do_not_print']);
		$printicon = esc_attr($atts['printicon']);
		$printstyle = esc_attr($atts['printstyle']);
		$html_top = wp_kses_post($atts['html_top']);
		$html_bottom = wp_kses_post($atts['html_bottom']);
		$pause_before_print = absint($atts['pause_before_print']);
		$title = esc_html($atts['title']);

		//if no printstyle, force-set to default
		if( empty( $printstyle ) ){
			$printstyle = 'pom-default';
		}

		//swap target placeholders out for the real deal
		if( get_the_ID() ){
			$target = str_replace('%ID%', get_the_ID(), $target);
		}

		//pass on any shortcode attributes that override default options
		$print_data = [];
		if( !empty( $html_top ) ){
			$print_data['pom_html_top'] = do_shortcode($html_top);
		}
		if( !empty( $html_bottom ) ){
			$print_data['pom_html_bottom'] = do_shortcode($html_bottom);
		}		
		if( !empty( $do_not_print ) ){
			$print_data['pom_do_not_print'] = $do_not_print;
		}
		if( !empty( $pause_before_print ) ){
			$print_data['pom_pause_time'] = $pause_before_print;
		}
		if( !empty($print_data) ){
			wp_add_inline_script( 'printomatic-js', 'window["print_data_'.$id.'"] = ' . wp_json_encode( $print_data ) . ';' );
		}

		//return nothing if using an external button
		if($printstyle == "external"){
			return;
		}

		if($printicon == "false"){
			$printicon = 0;
		}
		if( empty($alt) ){
			if( empty($title) ){
				$alt_tag = '';
			}
			else{
				$alt_tag = "alt='".esc_attr(strip_tags($title))."' title='".esc_attr(strip_tags($title))."'";
			}
		}
		else{
			$alt_tag = "alt='".esc_attr($alt)."' title='".esc_attr($alt)."'";
		}
		if($printicon && $title){
			$output = "<div class='printomatic ".esc_attr($printstyle)." ".esc_attr($class)."' id='".esc_attr($id)."' ".$alt_tag." data-print_target='".esc_attr($target)."'></div> <div class='printomatictext' id='".esc_attr($id)."' ".$alt_tag." data-print_target='".esc_attr($target)."'>".$title."</div><div style='clear: both;'></div>";
		}
		else if($printicon){
			$output = "<".$tag." class='printomatic ".esc_attr($printstyle)." ".esc_attr($class)."' id='".esc_attr($id)."' ".$alt_tag." data-print_target='".esc_attr($target)."'></".$tag.">";
		}
		else if($title){
			$output = "<".$tag." class='printomatictext ".esc_attr($class)."' id='".esc_attr($id)."' ".$alt_tag." data-print_target='".esc_attr($target)."'>".$title."</".$tag.">";
		}
		else{
			$output = esc_html__('Please update the Print-O-Matic options', 'print-o-matic');
		}
		return  $output;
	}

	/**
	 * Admin options page
	 */
	function options_page() {
	?>

		<div class="wrap">
			<h2>Print-O-Matic</h2>
		</div>

		<div class="postbox-container metabox-holder meta-box-sortables" style="width: 69%">
			<div style="margin:0 5px;">
				<div class="postbox">
					<div class="handlediv" title="<?php _e( 'Click to toggle', 'print-o-matic' ) ?>"><br/></div>
					<h3 class="handle"><?php _e( 'Print-O-Matic Settings', 'print-o-matic' ) ?></h3>
					<div class="inside">
						<form method="post" action="options.php">
							<?php
								settings_fields( $this->domain );
								$options = $this->options;
							?>
							<fieldset class="options">
								<table class="form-table">
								<tr>
									<th><?php _e( 'Default Target Attribute' , 'print-o-matic'  ) ?></th>
									<td><label><input type="text" id="WP_Print_O_Matic_options[print_target]" name="WP_Print_O_Matic_options[print_target]" value="<?php echo esc_attr($options['print_target']); ?>" />
										<br /><span class="description"><?php /* translators: %1$s: opening link tag, %2$s: closing link tag */
										printf(__('Print target. See %1$sTarget Attribute%2$s in the documentation for more info.', 'print-o-matic'), '<a href="https://pluginoven.com/plugins/print-o-matic/documentation/shortcode/#target-attribute" target="_blank">', '</a>'); ?></span></label>
									</td>
								</tr>
								<tr>
									<th><?php _e( 'Default Print Title' , 'print-o-matic'  ) ?></th>
									<td><label>
										<textarea id="print_title" name="WP_Print_O_Matic_options[print_title]" style="width: 100%;"><?php echo esc_textarea($options['print_title']); ?></textarea>
									</label></td>
								</tr>
								<tr>
									<th><?php _e( 'Use Print Icon', 'print-o-matic' ) ?></th>
									<td><label><select id="printicon" name="WP_Print_O_Matic_options[printicon]">
										<?php
											$se_array = array(
												__('Yes', 'print-o-matic') => true,
												__('No', 'print-o-matic') => false
											);
											foreach( $se_array as $key => $value){
												$selected = '';
												if($options['printicon'] == $value){
													$selected = 'SELECTED';
												}
												echo '<option value="'.esc_attr($value).'" '.esc_attr($selected).'>'.esc_attr($key).'</option>';
											}
										?>
										</select>
										<br /><span class="description"><?php /* translators: %1$s: opening link tag, %2$s: closing link tag */
										printf(__('Use printer icon. See %1$sPrinticon Attribute%2$s in the documentation for more info.', 'print-o-matic'), '<a href="https://pluginoven.com/plugins/print-o-matic/documentation/shortcode/#printicon-attribute" target="_blank">', '</a>'); ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Printer Icon', 'print-o-matic') ?></th>
									<td>
										<?php
											if( empty($options['printstyle']) ){
												$options['printstyle']	= 'pom-default';
											}
											$si_array = array(
												__('Default', 'print-o-matic') => 'pom-default',
												__('Small', 'print-o-matic') => 'pom-small',
												__('Small Black', 'print-o-matic') => 'pom-small-black',
												__('Small Grey', 'print-o-matic') => 'pom-small-grey',
												__('Small White', 'print-o-matic') => 'pom-small-white'
											);
											$icon_array = array(
												'pom-default' => 'print-icon.png',
												'pom-small' => 'print-icon-small.png',
												'pom-small-black' => 'print-icon-small-black.png',
												'pom-small-grey' => 'print-icon-small-grey.png',
												'pom-small-white' => 'print-icon-small-white.png'
											);
											foreach( $si_array as $key => $value){
												$selected = '';
												if($options['printstyle'] == $value){
													$selected = 'checked';
												}
												?>
												<label><input type="radio" name="WP_Print_O_Matic_options[printstyle]" value="<?php echo esc_attr($value); ?>" <?php echo esc_attr($selected); ?>> &nbsp;<?php echo esc_html($key); ?>
												<img src="<?php echo plugins_url( 'css/'.$icon_array[$value], __FILE__ ) ?>"/>
												</label><br/>
												<?php
											}
										?>
										<span class="description"><?php /* translators: %1$s: opening link tag, %2$s: closing link tag */
										printf(__('If using a printer icon, which printer icon should be used? See %1$sPrintstyle Attribute%2$s in the documentation for more info.', 'print-o-matic'), '<a href="https://pluginoven.com/plugins/print-o-matic/documentation/shortcode/#printstyle-attribute" target="_blank">', '</a>'); ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Custom Style', 'print-o-matic' ) ?></th>
									<td><label><textarea id="custom_page_css" name="WP_Print_O_Matic_options[custom_page_css]" style="width: 100%; height: 150px;"><?php echo esc_textarea($options['custom_page_css']); ?></textarea>
										<br /><span class="description"><?php _e('Custom CSS for the display page.', 'print-o-matic' ); ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Custom Print Page Style', 'print-o-matic' ) ?></th>
									<td><label><textarea id="custom_css" name="WP_Print_O_Matic_options[custom_css]" style="width: 100%; height: 150px;"><?php echo esc_textarea($options['custom_css']); ?></textarea>
										<br /><span class="description"><?php /* translators: %1$s: opening link tag, %2$s: closing link tag */
										printf(__('Custom CSS for the display page. Here are some helpful %1$scustom CSS samples%2$s', 'print-o-matic' ), '<a href="https://pluginoven.com/premium-plugins/print-pro-matic/documentation/plugin-settings/#custom-css" target="_blank">', '</a>'); ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Do Not Print Elements', 'print-o-matic' ) ?></th>
									<td><label><input type="text" id="do_not_print" name="WP_Print_O_Matic_options[do_not_print]" value="<?php echo esc_attr($options['do_not_print']); ?>" />
										<br /><span class="description"><?php /* translators: %1$s: opening link tag, %2$s: closing link tag */
										printf(__('Content elements to exclude from the print page. See %1$sDo Not Print Attribute%2$s in the documentation for more info.', 'print-o-matic'), '<a href="https://pluginoven.com/plugins/print-o-matic/documentation/shortcode/#do-not-print-attribute" target="_blank">', '</a>'); ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Print Page Top HTML', 'print-o-matic' ) ?></th>
									<td><label><textarea id="html_top" name="WP_Print_O_Matic_options[html_top]" style="width: 100%; height: 150px;"><?php echo esc_textarea($options['html_top']); ?></textarea>
										<br /><span class="description"><?php /* translators: %1$s: opening link tag, %2$s: closing link tag */
										printf(__('HTML to be inserted at the top of the print page. See %1$sHTML Top Attribute%2$s in the documentation for more info.', 'print-o-matic' ), '<a href="https://pluginoven.com/plugins/print-o-matic/documentation/shortcode/#html_top-attribute" target="_blank">', '</a>'); ?></span></label>
									</td>
								</tr>
								<tr>
									<th><?php _e( 'Print Page Bottom HTML', 'print-o-matic' ) ?></th>
									<td><label><textarea id="html_bottom" name="WP_Print_O_Matic_options[html_bottom]" style="width: 100%; height: 150px;"><?php echo esc_textarea($options['html_bottom']); ?></textarea>
										<br /><span class="description"><?php /* translators: %1$s: opening link tag, %2$s: closing link tag */
										printf(__('HTML to be inserted at the bottom of the print page. See %1$sHTML Bottom Attribute%2$s in the documentation for more info.', 'print-o-matic' ), '<a href="https://pluginoven.com/plugins/print-o-matic/documentation/shortcode/#html_bottom-attribute" target="_blank">', '</a>'); ?></span></label>
									</td>
								</tr>
								<tr>
									<th><?php _e( 'Pause Before Print', 'print-o-matic' ) ?></th>
									<td><label><input type="text" id="pause_time" name="WP_Print_O_Matic_options[pause_time]" value="<?php echo esc_attr($options['pause_time']); ?>" />
										<br /><span class="description"><?php _e('Amount of time in milliseconds to pause, allowing the print preview to render correclty.', 'print-o-matic'); ?></span></label>
									</td>
								</tr>
								
								<tr>
									<th><?php _e( 'Shortcode Loads Scripts & CSS', 'print-o-matic' ) ?></th>
									<td><label><input type="checkbox" id="script_check" name="WP_Print_O_Matic_options[script_check]" value="1"  <?php checked( $options['script_check'], 1 ); ?> /> <?php _e('Only load scripts with shortcode.', 'print-o-matic'); ?>
										<br /><span class="description"><?php _e('Only load Print-O-Matic JS and CSS files if [print-me] shortcode is used.', 'print-o-matic'); ?></span></label>
									</td>
								</tr>

								</table>
							</fieldset>

							<p class="submit">
								<input class="button-primary" type="submit" style="float:right" value="<?php esc_attr_e( 'Save Changes', 'print-o-matic' ) ?>" />
							</p>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="postbox-container side metabox-holder meta-box-sortables" style="width:29%;">
			<div style="margin:0 5px;">
				<div class="postbox">
					<div class="handlediv" title="<?php _e( 'Click to toggle', 'print-o-matic' ) ?>"><br/></div>
					<h3 class="handle"><?php _e( 'About', 'print-o-matic' ) ?></h3>
					<div class="inside">
						<h4><img src="<?php echo plugins_url( 'css/print-icon-small.png', __FILE__ ) ?>" /> Print-O-Matic <?php echo esc_attr($this->version); ?></h4>
						<p><?php _e( 'Print-O-Matic adds a shortcode to target-print specific elements in a post or page.', 'print-o-matic') ?></p>
						<ul>
							<li><?php /* translators: %1$s: opening link tag, %2$s: closing link tag */
							printf( __( '%1$sDetailed documentation%2$s, complete with working demonstrations of all shortcode attributes, is available for your instructional enjoyment.', 'print-o-matic'), '<a href="https://pluginoven.com/plugins/print-o-matic/documentation/shortcode/" target="_blank">', '</a>'); ?></li>
							<li><?php /* translators: %1$s: opening link tag, %2$s: closing link tag */
							printf( __( 'Free, Open Source %1$sSupport%2$s', 'print-o-matic'), '<a href="https://github.com/baden03/print-o-matic/issues" target="_blank">', '</a>'); ?></li>
							<li><a href="https://github.com/baden03/print-o-matic" target="_blank">GitHub</a> | <a href="https://pluginoven.com/plugins/print-o-matic/" target="_blank">Twinpictures Plugin Oven</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>

		<div class="postbox-container side metabox-holder meta-box-sortables" style="width:29%;">
			<div style="margin:0 5px;">
				<div class="postbox">
					<div class="handlediv" title="<?php _e( 'Click to toggle', 'print-o-matic' ) ?>"><br/></div>
					<h3 class="handle"><?php _e( 'Level Up!', 'print-o-matic' ) ?></h3>
					<div class="inside">
						<p><?php /* translators: %1$s: opening link tag, %2$s: closing link tag */
						printf(__( '%1$sPrint-Pro-Matic%2$s is our premium plugin that offers a few additional attributes and features for <i>ultimate</i> flexibility.', 'print-o-matic' ), '<a href="https://pluginoven.com/premium-plugins/print-pro-matic/?utm_source=print-o-matic&utm_medium=plugin-settings-page&utm_content=print-pro-matic&utm_campaign=print-pro-level-up">', '</a>'); ?></p>
						<h4><?php _e('Reasons To Go Pro', 'print-o-matic'); ?></h4>
						<ol>
							<li><?php _e("You are an advanced user with advanced needs and want some tasty advanced features", "print-o-matic"); ?></li>
							<li><?php _e("Print-Pro-Matic was just what you needed and you'd like to drop some coins in our jar", "print-o-matic"); ?></li>
						</ol>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	<?php
	}

	/**
	 * Set options from save values or defaults
	 */
	function _set_options() {
		// set options
		$saved_options = get_option( $this->options_name );

		// backwards compatible (old values)
		if ( empty( $saved_options ) ) {
			$saved_options = get_option( $this->domain . 'options' );
		}

		// set all options
		if ( ! empty( $saved_options ) ) {
			foreach ( $this->options AS $key => $option ) {
				$this->options[ $key ] = ( empty( $saved_options[ $key ] ) ) ? '' : $saved_options[ $key ];
			}
		}
	}

} // end class WP_Print_O_Matic

/**
 * Create instance
 */
$WP_Print_O_Matic = new WP_Print_O_Matic;

?>

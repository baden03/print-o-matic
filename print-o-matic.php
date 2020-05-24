<?php
/*
Plugin Name: Print-O-Matic
Text Domain: print-o-matic
Plugin URI: https://plugins.twinpictures.de/plugins/print-o-matic/
Description: Shortcode that adds a printer icon, allowing the user to print the post or a specified HTML element in the post.
Version: 2.0-alpha-05242
Author: twinpictures
Author URI: https://twinpictures.de
License: GPL2
*/

/**
 * Class WP_Print_O_Matic
 * @package WP_Print_O_Matic
 * @category WordPress Plugins
 */
class WP_Print_O_Matic {

	var $plugin_name = 'Print-O-Matic';
	var $version = '2.0-alpha-05242';
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

	var $add_print_script = array();

	/**
	 * PHP5 constructor
	 */
	function __construct() {
		// set option values
		$this->_set_options();

		//load the script and style if not viewing the dashboard
		add_action('wp_enqueue_scripts', array( $this, 'printMaticInit' ) );

		// add actions
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		//add_action( 'wp_head', array( $this, 'printomat_style' ) );
		add_action( 'wp_footer', array($this, 'printer_scripts') );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		add_shortcode( 'print-me', array($this, 'shortcode') );
		// Add shortcode support for widgets
		add_filter('widget_text', 'do_shortcode');
	}

	// load text domain for translations
	function load_textdomain() {
		load_plugin_textdomain( 'print-o-matic' );
	}

	/**
	 * Callback init
	 */
	function printMaticInit() {
		//script
		wp_register_script('printomatic-js', plugins_url('js/printomat.js', __FILE__), array('jquery'), '2.0', true);
		wp_register_script('pe-js', plugins_url('js/print_elements.js', __FILE__), array('printomatic-js'), '1.0', true);

		//css
		wp_register_style( 'printomatic-css', plugins_url('/css/style.css', __FILE__) , array (), '2.0' );
		wp_enqueue_style( 'printomatic-css' );
	}

	/**
	 * Callback admin_menu
	 */
	function admin_menu() {
		if ( function_exists( 'add_options_page' ) AND current_user_can( 'manage_options' ) ) {
			// add options page
			$page = add_options_page('Print-O-Matic Options', 'Print-O-Matic', 'manage_options', 'print-o-matic-options', array( $this, 'options_page' ));
		}
	}

	/**
	 * Callback admin_init
	 */
	function admin_init() {
		// register settings
		register_setting( $this->domain, $this->options_name );
	}

	/**
	 * Callback shortcode
	 */
	function shortcode($atts, $content = null){
		$ran = rand(1, 10000);
		$options = $this->options;

		extract( shortcode_atts(array(
			'id' => 'id'.$ran,
			'class' => '',
			'tag' => 'div',
			'alt' => '',
			'target' => $options['print_target'],
			'do_not_print' => $options['do_not_print'],
			'printicon' => $options['printicon'],
			'printstyle' => $options['printstyle'],
			'html_top' => $options['html_top'],
			'html_bottom' => $options['html_bottom'],
			'pause_before_print' => $options['pause_time'],
			'title' => $options['print_title'],

		), $atts));

		//if no printstyle, force-set to default
		if( empty( $printstyle ) ){
			$printstyle = 'pom-default';
		}

		//swap target placeholders out for the real deal
		$target = str_replace('%ID%', get_the_ID(), $target);

		if( empty( $html_top ) ){
			$pom_html_top = '';
		}
		else{
			//$pom_html_top = apply_filters('the_content', $html_top);
			//switching to do_shortcode to avoid conflicts with social sharing plugins
			$pom_html_top = do_shortcode($html_top);
		}
		if( empty( $html_bottom ) ){
			$pom_html_bottom = '';
		}
		else{
			//$pom_html_bottom = apply_filters('the_content', $html_bottom);
			//switching to do_shortcode to avoid conflicts with social sharing plugins
			$pom_html_bottom = do_shortcode($html_bottom);
		}
		if( empty( $do_not_print ) ){
			$pom_do_not_print = '';
		}
		else{
			$pom_do_not_print = $do_not_print;
		}

		$this->add_print_script[$id] = array(
			'pom_html_top' => $pom_html_top,
			'pom_html_bottom' => $pom_html_bottom,
			'pom_do_not_print' => $pom_do_not_print,
			'pom_pause_time' => $pause_before_print,
		);

		//return nothing if usign an external button
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
				$alt_tag = "alt='".strip_tags($title)."' title='".strip_tags($title)."'";
			}
		}
		else{
			$alt_tag = "alt='".$alt."' title='".$alt."'";
		}
		if($printicon && $title){
			$output = "<div class='printomatic ".$printstyle." ".$class."' id='".$id."' ".$alt_tag." data-print_target='".$target."'></div> <div class='printomatictext' id='".$id."' ".$alt_tag." data-print_target='".$target."'>".$title."</div><div style='clear: both;'></div>";
		}
		else if($printicon){
			$output = "<".$tag." class='printomatic ".$printstyle." ".$class."' id='".$id."' ".$alt_tag." data-print_target='".$target."'></".$tag.">";
		}
		else if($title){
			$output = "<".$tag." class='printomatictext ".$class."' id='".$id."' ".$alt_tag." data-print_target='".$target."'>".$title."</".$tag.">";
		}
		return  $output;
	}

	function printer_scripts() {
		if ( !empty( $this->add_print_script ) ){
			wp_localize_script( 'printomatic-js', 'print_data', $this->add_print_script );
		}
		wp_enqueue_script('printomatic-js');
		wp_enqueue_script('pe-js');
	}

	/**
	 * Admin options page
	 */
	function options_page() {
		$like_it_arr = array(
						__('really tied the room together', 'print-o-matic'),
						__('made you feel all warm and fuzzy on the inside', 'print-o-matic'),
						__('restored your faith in humanity... even if only for a fleeting second', 'print-o-matic'),
						__('rocked your world', 'provided a positive vision of future living', 'print-o-matic'),
						__('inspired you to commit a random act of kindness', 'print-o-matic'),
						__('encouraged more regular flossing of the teeth', 'print-o-matic'),
						__('helped organize your life in the small ways that matter', 'print-o-matic'),
						__('saved your minutes--if not tens of minutes--writing your own solution', 'print-o-matic'),
						__('brightened your day... or darkened if if you are trying to sleep in', 'print-o-matic'),
						__('caused you to dance a little jig of joy and joyousness', 'print-o-matic'),
						__('inspired you to tweet a little @twinpictues social love', 'print-o-matic'),
						__('tasted great, while also being less filling', 'print-o-matic'),
						__('caused you to shout: "everybody spread love, give me some mo!"', 'print-o-matic'),
						__('helped you keep the funk alive', 'print-o-matic'),
						__('<a href="https://www.youtube.com/watch?v=dvQ28F5fOdU" target="_blank">soften hands while you do dishes</a>', 'print-o-matic'),
						__('helped that little old lady <a href="https://www.youtube.com/watch?v=Ug75diEyiA0" target="_blank">find the beef</a>', 'print-o-matic')
					);
		$rand_key = array_rand($like_it_arr);
		$like_it = $like_it_arr[$rand_key];
	?>

		<div class="wrap">
			<h2><?php echo $this->plugin_name; ?></h2>
		</div>

		<div class="postbox-container metabox-holder meta-box-sortables" style="width: 69%">
			<div style="margin:0 5px;">
				<div class="postbox">
					<div class="handlediv" title="<?php _e( 'Click to toggle' ) ?>"><br/></div>
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
									<td><label><input type="text" id="<?php echo $this->options_name ?>[print_target]" name="<?php echo $this->options_name ?>[print_target]" value="<?php echo $options['print_target']; ?>" />
										<br /><span class="description"><?php printf(__('Print target. See %sTarget Attribute%s in the documentation for more info.', 'print-o-matic'), '<a href="https://plugins.twinpictures.de/plugins/print-o-matic/documentation/#target" target="_blank">', '</a>'); ?></span></label>
									</td>
								</tr>
								<tr>
									<th><?php _e( 'Default Print Title' , 'print-o-matic'  ) ?></th>
									<td><label>
										<textarea id="<?php echo $this->options_name ?>[print_title]" name="<?php echo $this->options_name ?>[print_title]" style="width: 100%;"><?php echo $options['print_title']; ?></textarea>
									</label></td>
								</tr>
								<tr>
									<th><?php _e( 'Use Print Icon', 'print-o-matic' ) ?></th>
									<td><label><select id="<?php echo $this->options_name ?>[printicon]" name="<?php echo $this->options_name ?>[printicon]">
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
												echo '<option value="'.$value.'" '.$selected.'>'.$key.'</option>';
											}
										?>
										</select>
										<br /><span class="description"><?php printf(__('Use printer icon. See %sPrinticon Attribute%s in the documentation for more info.', 'print-o-matic'), '<a href="https://plugins.twinpictures.de/plugins/print-o-matic/documentation/#printicon" target="_blank">', '</a>'); ?></span></label>
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
												<label><input type="radio" name="<?php echo $this->options_name ?>[printstyle]" value="<?php echo $value; ?>" <?php echo $selected; ?>> &nbsp;<?php echo $key; ?>
												<img src="<?php echo plugins_url( 'css/'.$icon_array[$value], __FILE__ ) ?>"/>
												</label><br/>
												<?php
											}
										?>
										<span class="description"><?php printf(__('If using a printer icon, which printer icon should be used? See %sPrintstyle Attribute%s in the documentation for more info.', 'print-o-matic'), '<a href="https://plugins.twinpictures.de/plugins/print-o-matic/documentation/#printstyle" target="_blank">', '</a>'); ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Custom Style', 'print-o-matic' ) ?></th>
									<td><label><textarea id="<?php echo $this->options_name ?>[custom_page_css]" name="<?php echo $this->options_name ?>[custom_page_css]" style="width: 100%; height: 150px;"><?php echo $options['custom_page_css']; ?></textarea>
										<br /><span class="description"><?php _e('this will be removed in 2.0 - there are better places to add custom css to your theme', 'print-o-matic' ); ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Custom Print Page Style', 'print-o-matic' ) ?></th>
									<td><label><textarea id="<?php echo $this->options_name ?>[custom_css]" name="<?php echo $this->options_name ?>[custom_css]" style="width: 100%; height: 150px;"><?php echo $options['custom_css']; ?></textarea>
										<br /><span class="description"><?php _e( 'this will be removed in 2.0 - there are better places to add custom css to your theme', 'print-o-matic' ) ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Do Not Print Elements', 'print-o-matic' ) ?></th>
									<td><label><input type="text" id="<?php echo $this->options_name ?>[do_not_print]" name="<?php echo $this->options_name ?>[do_not_print]" value="<?php echo $options['do_not_print']; ?>" />
										<br /><span class="description"><?php printf(__('Content elements to exclude from the print page. See %sDo Not Print Attribute%s in the documentation for more info.', 'print-o-matic'), '<a href="https://plugins.twinpictures.de/plugins/print-o-matic/documentation/#do-no-print" target="_blank">', '</a>'); ?></span></label>
									</td>
								</tr>

								<tr>
									<th><?php _e( 'Print Page Top HTML', 'print-o-matic' ) ?></th>
									<td><label><textarea id="<?php echo $this->options_name ?>[html_top]" name="<?php echo $this->options_name ?>[html_top]" style="width: 100%; height: 150px;"><?php echo $options['html_top']; ?></textarea>
										<br /><span class="description"><?php printf(__('HTML to be inserted at the top of the print page. See %sHTML Top Attribute%s in the documentation for more info.', 'print-o-matic' ), '<a href="https://plugins.twinpictures.de/plugins/print-o-matic/documentation/#html-top" target="_blank">', '</a>'); ?></span></label>
									</td>
								</tr>
								<tr>
									<th><?php _e( 'Print Page Bottom HTML', 'print-o-matic' ) ?></th>
									<td><label><textarea id="<?php echo $this->options_name ?>[html_bottom]" name="<?php echo $this->options_name ?>[html_bottom]" style="width: 100%; height: 150px;"><?php echo $options['html_bottom']; ?></textarea>
										<br /><span class="description"><?php printf(__('HTML to be inserted at the bottom of the print page. See %sHTML Bottom Attribute%s in the documentation for more info.', 'print-o-matic' ), '<a href="https://plugins.twinpictures.de/plugins/print-o-matic/documentation/#html-bottom" target="_blank">', '</a>'); ?></span></label>
									</td>
								</tr>
								<tr>
									<th><?php _e( 'Pause Before Print', 'print-o-matic' ) ?></th>
									<td><label><input type="text" id="<?php echo $this->options_name ?>[pause_time]" name="<?php echo $this->options_name ?>[pause_time]" value="<?php echo $options['pause_time']; ?>" />
										<br /><span class="description"><?php _e('Amount of time in milliseconds to pause and let the page fully load before triggering the print dialogue box', 'print-o-matic'); ?></span></label>
									</td>
								</tr>
								<tr>
									<th><?php _e( 'Shortcode Loads Scripts', 'print-o-matic' ) ?></th>
									<td><label><input type="checkbox" id="<?php echo $this->options_name ?>[script_check]" name="<?php echo $this->options_name ?>[script_check]" value="1"  <?php echo checked( $options['script_check'], 1 ); ?> /> <?php _e('Only load scripts with shortcode.', 'print-o-matic'); ?>
										<br /><span class="description"><?php _e('Only load  Print-O-Matic scripts if [print-me] shortcode is used.', 'print-o-matic'); ?></span></label>
									</td>
								</tr>

								</table>
							</fieldset>

							<p class="submit">
								<input class="button-primary" type="submit" style="float:right" value="<?php _e( 'Save Changes' ) ?>" />
							</p>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="postbox-container side metabox-holder meta-box-sortables" style="width:29%;">
			<div style="margin:0 5px;">
				<div class="postbox">
					<div class="handlediv" title="<?php _e( 'Click to toggle' ) ?>"><br/></div>
					<h3 class="handle"><?php _e( 'About' ) ?></h3>
					<div class="inside">
						<h4><img src="<?php echo plugins_url( 'css/print-icon-small.png', __FILE__ ) ?>" /> <?php echo $this->plugin_name; ?> <?php echo $this->version; ?></h4>
						<p><?php _e( 'Shortcode that adds a printer icon, allowing the user to print the post or a specified HTML element in the post.', 'print-o-matic') ?></p>
						<ul>
							<li><?php printf( __( '%sDetailed documentation%s, complete with working demonstrations of all shortcode attributes, is available for your instructional enjoyment.', 'print-o-matic'), '<a href="https://plugins.twinpictures.de/plugins/print-o-matic/documentation/" target="_blank">', '</a>'); ?></li>
							<li><?php printf( __( 'Free, Open Source %sSupport%s', 'print-o-matic'), '<a href="https://wordpress.org/support/plugin/print-o-matic/" target="_blank">', '</a>'); ?></li>
							<li><?php printf( __('If Print-O-Matic %s, please consider %sreviewing it at WordPress.org%s to better help others make informed plugin choices.', 'print-o-matic'), $like_it, '<a href="https://wordpress.org/support/view/plugin-reviews/print-o-matic" target="_blank">', '</a>' ) ?></li>
							<li><a href="https://wordpress.org/plugins/print-o-matic/" target="_blank">WordPress.org</a> | <a href="https://plugins.twinpictures.de/plugins/print-o-matic/" target="_blank">Twinpictues Plugin Oven</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>

		<div class="postbox-container side metabox-holder meta-box-sortables" style="width:29%;">
			<div style="margin:0 5px;">
				<div class="postbox">
					<div class="handlediv" title="<?php _e( 'Click to toggle' ) ?>"><br/></div>
					<h3 class="handle"><?php _e( 'Level Up!' ) ?></h3>
					<div class="inside">
						<p><?php printf(__( '%sPrint-Pro-Matic%s is our premium plugin that offers a few additional attributes and features for <i>ultimate</i> flexibility.', 'print-o-mat' ), '<a href="https://plugins.twinpictures.de/premium-plugins/print-pro-matic/?utm_source=print-o-matic&utm_medium=plugin-settings-page&utm_content=print-pro-matic&utm_campaign=print-pro-level-up">', '</a>'); ?></p>
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

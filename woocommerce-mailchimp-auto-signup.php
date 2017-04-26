<?php
/**
 * Plugin Name: Woocommerce Mailchimp auto signup
 * Plugin URI:  http://www.digital8.com.au
 * Description: A plugin that automatically signs up users to a mailing list when they buy a particular product.
 * Version:     0.0.4
 * Author:      Matthew Neal, Digital8
 * Author URI:  http://www.digital8.com.au/
 * Donate link: http://www.digital8.com.au
 * License:     GPLv2
 * Text Domain: woocommerce-mailchimp-auto-signup
 * Domain Path: /languages
 *
 * @link    http://www.digital8.com.au
 *
 * @package Woocommerce_Mailchimp_Auto_Signup
 * @version 0.0.4
 *
 * Built using generator-plugin-wp (https://github.com/WebDevStudios/generator-plugin-wp)
 */

/**
 * Copyright (c) 2017 Matt, Digital8 (email : matt@digital8.com.au)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


/**
 * Autoloads files with classes when needed.
 *
 * @since  0.0.1
 * @param  string $class_name Name of the class being requested.
 */
function woocommerce_mailchimp_auto_signup_autoload_classes( $class_name ) {

	// If our class doesn't have our prefix, don't load it.
	if ( 0 !== strpos( $class_name, 'WMAS_' ) ) {
		return;
	}

	// Set up our filename.
	$filename = strtolower( str_replace( '_', '-', substr( $class_name, strlen( 'WMAS_' ) ) ) );

	// Include our file.
	Woocommerce_Mailchimp_Auto_Signup::include_file( 'includes/class-' . $filename );
}
spl_autoload_register( 'woocommerce_mailchimp_auto_signup_autoload_classes' );

/**
 * Main initiation class.
 *
 * @since  0.0.1
 */
final class Woocommerce_Mailchimp_Auto_Signup {

	/**
	 * Current version.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	const VERSION = '0.0.1';

	/**
	 * URL of plugin directory.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected $path = '';

	/**
	 * Plugin basename.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected $basename = '';

	/**
	 * Detailed activation error messages.
	 *
	 * @var    array
	 * @since  0.0.1
	 */
	protected $activation_errors = array();

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    Woocommerce_Mailchimp_Auto_Signup
	 * @since  0.0.1
	 */
	protected static $single_instance = null;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   0.0.1
	 * @return  Woocommerce_Mailchimp_Auto_Signup A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since  0.0.1
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  0.0.1
	 */
	public function plugin_classes() {
		// $this->plugin_class = new WMAS_Plugin_Class( $this );

	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters.
	 * Priority needs to be
	 * < 10 for CPT_Core,
	 * < 5 for Taxonomy_Core,
	 * and 0 for Widgets because widgets_init runs at init priority 1.
	 *
	 * @since  0.0.1
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Activate the plugin.
	 *
	 * @since  0.0.1
	 */
	public function _activate() {
		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin.
	 * Uninstall routines should be in uninstall.php.
	 *
	 * @since  0.0.1
	 */
	public function _deactivate() {
		// Add deactivation cleanup functionality here.
	}

	/**
	 * Init hooks
	 *
	 * @since  0.0.1
	 */
	public function init() {

		// Bail early if requirements aren't met.
		if ( ! $this->check_requirements() ) {
			return;
		}

		// Load translated strings for plugin.
		load_plugin_textdomain( 'woocommerce-mailchimp-auto-signup', false, dirname( $this->basename ) . '/languages/' );

		// Initialize plugin classes.
		$this->plugin_classes();
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 *
	 * @since  0.0.1
	 *
	 * @return boolean True if requirements met, false if not.
	 */
	public function check_requirements() {

		// Bail early if plugin meets requirements.
		if ( $this->meets_requirements() ) {
			return true;
		}

		// Add a dashboard notice.
		add_action( 'all_admin_notices', array( $this, 'requirements_not_met_notice' ) );

		// Deactivate our plugin.
		add_action( 'admin_init', array( $this, 'deactivate_me' ) );

		// Didn't meet the requirements.
		return false;
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  0.0.1
	 */
	public function deactivate_me() {

		// We do a check for deactivate_plugins before calling it, to protect
		// any developers from accidentally calling it too early and breaking things.
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->basename );
		}
	}

	/**
	 * Check that all plugin requirements are met.
	 *
	 * @since  0.0.1
	 *
	 * @return boolean True if requirements are met.
	 */
	public function meets_requirements() {

		// Do checks for required classes / functions or similar.
		// Add detailed messages to $this->activation_errors array.
		return true;
	}

	/**
	 * Adds a notice to the dashboard if the plugin requirements are not met.
	 *
	 * @since  0.0.1
	 */
	public function requirements_not_met_notice() {

		// Compile default message.
		$default_message = sprintf( __( 'Woocommerce Mailchimp auto signup is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'woocommerce-mailchimp-auto-signup' ), admin_url( 'plugins.php' ) );

		// Default details to null.
		$details = null;

		// Add details if any exist.
		if ( $this->activation_errors && is_array( $this->activation_errors ) ) {
			$details = '<small>' . implode( '</small><br /><small>', $this->activation_errors ) . '</small>';
		}

		// Output errors.
		?>
		<div id="message" class="error">
			<p><?php echo wp_kses_post( $default_message ); ?></p>
			<?php echo wp_kses_post( $details ); ?>
		</div>
		<?php
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  0.0.1
	 *
	 * @param  string $field Field to get.
	 * @throws Exception     Throws an exception if the field is invalid.
	 * @return mixed         Value of the field.
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory.
	 *
	 * @since  0.0.1
	 *
	 * @param  string $filename Name of the file to be included.
	 * @return boolean          Result of include call.
	 */
	public static function include_file( $filename ) {
		$file = self::dir( $filename . '.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
		return false;
	}

	/**
	 * This plugin's directory.
	 *
	 * @since  0.0.1
	 *
	 * @param  string $path (optional) appended path.
	 * @return string       Directory and path.
	 */
	public static function dir( $path = '' ) {
		static $dir;
		$dir = $dir ? $dir : trailingslashit( dirname( __FILE__ ) );
		return $dir . $path;
	}

	/**
	 * This plugin's url.
	 *
	 * @since  0.0.1
	 *
	 * @param  string $path (optional) appended path.
	 * @return string       URL and path.
	 */
	public static function url( $path = '' ) {
		static $url;
		$url = $url ? $url : trailingslashit( plugin_dir_url( __FILE__ ) );
		return $url . $path;
	}
}

/**
 * Grab the Woocommerce_Mailchimp_Auto_Signup object and return it.
 * Wrapper for Woocommerce_Mailchimp_Auto_Signup::get_instance().
 *
 * @since  0.0.1
 * @return Woocommerce_Mailchimp_Auto_Signup  Singleton instance of plugin class.
 */
function woocommerce_mailchimp_auto_signup() {
	return Woocommerce_Mailchimp_Auto_Signup::get_instance();
}

// Add settings link in Plugins page
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links' );

function add_action_links ( $links ) {
 $mylinks = array(
 '<a href="' . admin_url( 'admin.php?page=woocommerce_mailchimp_auto_signup' ) . '">Settings</a>',
 );
return array_merge( $links, $mylinks );
}

// Kick it off.
add_action( 'plugins_loaded', array( woocommerce_mailchimp_auto_signup(), 'hooks' ) );

// Activation and deactivation.
register_activation_hook( __FILE__, array( woocommerce_mailchimp_auto_signup(), '_activate' ) );
register_deactivation_hook( __FILE__, array( woocommerce_mailchimp_auto_signup(), '_deactivate' ) );

add_action( 'woocommerce_thankyou', 'bbloomer_check_order_product_id');


//start function - chucks in the order id.
function bbloomer_check_order_product_id( $order_id ){
	// creates a new class based off the order ID
	$order = new WC_Order( $order_id );
	$options = get_option( 'WMas_settings' );
	// gets the item ID that was ordered
	$items = $order->get_items(); 
	// gets the email address from the class / order being processd
	$newuseremail = $order->get_billing_email();
	$userfirstname = $order->get_billing_first_name();
	// loop through each item and...
	foreach ( $items as $item ) {
		//set the id for item in the loop
	   $product_id = $item['product_id'];
			// check if it matches the product ID we want to send emails for, then...
	      if ( $product_id == $options['WMas_text_field_2'] ) {

				function add_user_to_list($newuseremail, $userfirstname) {
					
					$options = get_option( 'WMas_settings' );
					$apikey = $options['WMas_text_field_0'];
					$auth = base64_encode( 'user:'.$apikey );
					$data = array(
					    'apikey'        => $apikey,
					    'email_address' => $newuseremail,
					    'status'        => 'subscribed',
					    'merge_fields'  => array(
					        'FNAME' => $userfirstname
					    )
					);
					$json_data = json_encode($data);
					$ch = curl_init();
				
					    curl_setopt($ch, CURLOPT_URL, 'https://us12.api.mailchimp.com/3.0/lists/' . $options['WMas_text_field_1'] . '/members/');
					    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '. $auth));
					    curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
					    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
					    curl_setopt($ch, CURLOPT_POST, true);
					    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);                                                      				                                                     
					    
					    $result = curl_exec($ch);
					            // var_dump($result);
					            // die('Mailchimp executed');
				}

				add_user_to_list($newuseremail, $userfirstname);

	      }
	}
}

// Add menu items to Admin panel
add_action( 'admin_menu', 'WMas_add_admin_menu' );
add_action( 'admin_init', 'WMas_settings_init' );


function WMas_add_admin_menu(  ) { 

	add_menu_page( 'WMAS', 'WMAS', 'manage_options', 'woocommerce_mailchimp_auto_signup', 'WMas_options_page' );

}


function WMas_settings_init(  ) { 

	register_setting( 'pluginPage', 'WMas_settings' );

	add_settings_section(
		'WMas_pluginPage_section', 
		__( 'Modify settings of the auto signup plugin.', 'wordpress' ), 
		'WMas_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'WMas_text_field_0', 
		__( 'Enter your mailchimp API key here', 'wordpress' ), 
		'WMas_text_field_0_render', 
		'pluginPage', 
		'WMas_pluginPage_section' 
	);

	add_settings_field( 
		'WMas_text_field_1', 
		__( 'Enter your mailchimp list ID here', 'wordpress' ), 
		'WMas_text_field_1_render', 
		'pluginPage', 
		'WMas_pluginPage_section' 
	);

		add_settings_field( 
		'WMas_text_field_2', 
		__( 'Enter the ID of the product you want to trigger the signup function here', 'wordpress' ), 
		'WMas_text_field_2_render', 
		'pluginPage', 
		'WMas_pluginPage_section' 
	);


}


function WMas_text_field_0_render(  ) { 

	$options = get_option( 'WMas_settings' );
	?>
	<input type='text' style="width:400px;" name='WMas_settings[WMas_text_field_0]' value='<?php echo $options['WMas_text_field_0']; ?>'>
	<?php 

}

function WMas_text_field_2_render(  ) { 

	$options = get_option( 'WMas_settings' );
	?>
	<input type='text' style="width:100px;" name='WMas_settings[WMas_text_field_2]' value='<?php echo $options['WMas_text_field_2']; ?>'>
	<?php

}

function WMas_text_field_1_render(  ) { 

	$options = get_option( 'WMas_settings' );
	?>
	<input type='text' style="width:150px;" name='WMas_settings[WMas_text_field_1]' value='<?php echo $options['WMas_text_field_1']; ?>'>
	<?php

}


function WMas_settings_section_callback(  ) { 

	echo __( 'This is a small plugin for Woocommerce, that allows you to automatically sign up clients to a Mailchimp list when they purchase a specific product from your store. <a href="http://kb.mailchimp.com/integrations/api-integrations/about-api-keys">To find or create your Mailchimp API key, click here.</a> Once you have your API key, find the ID of your list <a href="http://kb.mailchimp.com/lists/manage-contacts/find-your-list-id"> by following these instructions.</a> Lastly, From the Woocommerce products page, find the ID of the product you want to trigger the auto signup, and save this form. Now you are done!', 'wordpress' );

}


function WMas_options_page(  ) { 

	?>
	<form action='options.php' method='post'>

		<h2>Woocommerce Mailchimp auto signup</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>

<?php } ?>
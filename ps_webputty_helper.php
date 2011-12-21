<?php
/*
Plugin Name: WebPutty Helper
Plugin URI: http://soderlind.no/archives/2011/12/19/wordpress-and-webputty/
Description: Helps you to add the needed style and javascript to enable <a href="http://www.webputty.net/">WebPutty</a>
Version: 0.0.1
Author: Per S
Author URI: http://soderlind.no/
*/
/*

Changelog:
v1.0: Initial release

*/
/*
Credits: 
	This template is based on the template at http://pressography.com/plugins/wordpress-plugin-template/ 
	My changes are documented at http://soderlind.no/archives/2010/03/04/wordpress-plugin-template/
*/

if (!class_exists('ps_webputty_helper')) {
	class ps_webputty_helper {
		/**
		* @var string The options string name for this plugin
		*/
		var $optionsName = 'ps_webputty_helper_options';

		/**
		* @var array $options Stores the options for this plugin
		*/
		var $options = array();
		/**
		* @var string $localizationDomain Domain used for localization
		*/
		var $localizationDomain = "ps_webputty_helper";

		/**
		* @var string $url The url to this plugin
		*/ 
		var $url = '';
		/**
		* @var string $urlpath The path to this plugin
		*/
		var $urlpath = '';

		//Class Functions
		/**
		* PHP 4 Compatible Constructor
		*/
		function ps_webputty_helper(){$this->__construct();}

		/**
		* PHP 5 Constructor
		*/		
		function __construct(){
			//Language Setup
			$locale = get_locale();
			$mo = plugins_url("/languages/" . $this->localizationDomain . "-".$locale.".mo", __FILE__);	
			load_textdomain($this->localizationDomain, $mo);

			//"Constants" setup
			$this->url = plugins_url(basename(__FILE__), __FILE__);
			$this->urlpath = plugins_url('', __FILE__);	
			//Initialize the options
			$this->getOptions();
			//Admin menu
			add_action("admin_menu", array(&$this,"admin_menu_link"));

			//Actions
			add_action('wp_print_scripts', array(&$this,'ps_webputty_helper_script'));
			add_action("wp_head", array(&$this,"ps_webputty_helper_wp_head"),999);
		}

		function ps_webputty_helper_wp_head() {
			if ($this->options) {
				echo stripslashes($this->options['ps_webputty_helper_option1']);
			}
		}

		function ps_webputty_helper_script() {
			if (is_admin()){ // only run when in wp-admin, other conditional tags at http://codex.wordpress.org/Conditional_Tags
				wp_enqueue_script('jquery'); // other scripts included with Wordpress: http://tinyurl.com/y875age
				wp_enqueue_script('jquery-validate', 'http://ajax.microsoft.com/ajax/jquery.validate/1.6/jquery.validate.min.js', array('jquery'));
				wp_enqueue_script('ps_webputty_helper_script', $this->url.'?ps_webputty_helper_javascript'); // embed javascript, see end of this file
				wp_localize_script( 'ps_webputty_helper_script', 'ps_webputty_helper_lang', array(
					'required' => __('Please enter the WebPutty embed code.', $this->localizationDomain),
				));
			}
		}
		/**
		* @desc Retrieves the plugin options from the database.
		* @return array
		*/
		function getOptions() {
			/*
			if (!$theOptions = get_option($this->optionsName)) {
				$theOptions = array('ps_webputty_helper_option1'=> 1, 'ps_webputty_helper_option2' => 'value');
				update_option($this->optionsName, $theOptions);
			}
			*/
			$this->options = get_option($this->optionsName);
		}
		/**
		* Saves the admin options to the database.
		*/
		function saveAdminOptions(){
			return update_option($this->optionsName, $this->options);
		}

		/**
		* @desc Adds the options subpanel
		*/
		function admin_menu_link() {
			add_options_page('WebPutty Helper', 'webputty helper', 10, basename(__FILE__), array(&$this,'admin_options_page'));
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'filter_plugin_actions'), 10, 2 );
		}

		/**
		* @desc Adds the Settings link to the plugin activate/deactivate page
		*/
		function filter_plugin_actions($links, $file) {
		   $settings_link = '<a href="options-general.php?page=' . basename(__FILE__) . '">' . __('Settings') . '</a>';
		   array_unshift( $links, $settings_link ); // before other links

		   return $links;
		}

		/**
		* Adds settings/options page
		*/
		function admin_options_page() { 
			if($_POST['ps_webputty_helper_save']){
				if (! wp_verify_nonce($_POST['_wpnonce'], 'ps_webputty_helper-update-options') ) die('Whoops! There was a problem with the data you posted. Please go back and try again.'); 
				$this->options['ps_webputty_helper_option1'] = $_POST['ps_webputty_helper_option1'];				   				   

				$this->saveAdminOptions();

				echo '<div class="updated"><p>Success! Your changes were sucessfully saved!</p></div>';
			}
?>								   
			<div class="wrap">
			<h2>webputty helper</h2>
			<p>
			<?php _e('WebPutty helper', $this->localizationDomain); ?>
			</p>
			<form method="post" id="ps_webputty_helper_options">
			<?php wp_nonce_field('ps_webputty_helper-update-options'); ?>
				<table width="100%" cellspacing="2" cellpadding="5" class="form-table"> 
					<tr valign="top"> 
						<th width="33%" scope="row"><?php _e('WebPutty embed code:', $this->localizationDomain); ?></th> 
						<td>
							<textarea name="ps_webputty_helper_option1" type="text" id="ps_webputty_helper_option1" style="width:70%;height:300px;font-face:console;"><?php echo stripslashes($this->options['ps_webputty_helper_option1']) ;?></textarea>
							<br /><span class="setting-description"><?php _e('Add the embed code from <a href="http://www.webputty.net/">WebPutty</a>', $this->localizationDomain); ?>
						</td> 
					</tr>
				</table>
				<p class="submit"> 
					<input type="submit" name="ps_webputty_helper_save" class="button-primary" value="<?php _e('Save Changes', $this->localizationDomain); ?>" />
				</p>
			</form>				
			<?php
		}
	} //End Class
} //End if class exists statement



if (isset($_GET['ps_webputty_helper_javascript'])) {
	//embed javascript
	Header("content-type: application/x-javascript");
	echo<<<ENDJS
/**
* @desc webputty helper
* @author Per S - http://soderlind.no/
*/

jQuery(document).ready(function(){
	// add your jquery code here


	//validate plugin option form
  	jQuery("#ps_webputty_helper_options").validate({
		rules: {
			ps_webputty_helper_option1: {
				required: true
			}
		},
		messages: {
			ps_webputty_helper_option1: {
				// the ps_webputty_helper_lang object is define using wp_localize_script() in function ps_webputty_helper_script() 
				required: ps_webputty_helper_lang.required
			}
		}
	});
});

ENDJS;

} else {
	if (class_exists('ps_webputty_helper')) { 
		$ps_webputty_helper_var = new ps_webputty_helper();
	}
}
?>
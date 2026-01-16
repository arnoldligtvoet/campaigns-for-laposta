<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/arnoldligtvoet/campaigns-for-laposta
 * @since      1.0.0
 *
 * @package    Campaigns_For_Laposta
 * @subpackage Campaigns_For_Laposta/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Campaigns_For_Laposta
 * @subpackage Campaigns_For_Laposta/admin
 * @author     Arnold Ligtvoet <arnold@ligtvoet.org>
 */
class Campaigns_For_Laposta_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Campaigns_For_Laposta_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Campaigns_For_Laposta_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/campaigns-for-laposta-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Campaigns_For_Laposta_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Campaigns_For_Laposta_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/campaigns-for-laposta-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/**
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
		 *
		 * @link https://codex.wordpress.org/Function_Reference/add_options_page
		 *
		 * If you want to list plugin options page under a custom post type, then change 'plugin.php' to e.g. 'edit.php?post_type=your_custom_post_type'
		 */
		add_submenu_page( 'options-general.php', 'Campaigns for Laposta Settings', 'Campaigns for Laposta', 'manage_options', $this->plugin_name, array( $this, 'display_plugin_setup_page' ) );

	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		/**
		 * Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
		 * The "plugins.php" must match with the previously added add_submenu_page first option.
		 * For custom post type you have to change 'plugins.php?page=' to 'edit.php?post_type=your_custom_post_type&page='
		 */
		
		$settings_link = array( '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __( 'Settings', $this->plugin_name ) . '</a>', );

		return array_merge(  $settings_link, $links );

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {

		include_once( 'partials/' . $this->plugin_name . '-admin-display.php' );

	}

	/**
	 * Validate fields from admin area plugin settings form ('exopite-lazy-load-xt-admin-display.php')
	 * @param  mixed $input as field form settings form
	 * @return mixed as validated fields
	 */
	public function validate($input) {

		$options = get_option( $this->plugin_name );


		$options['api_key_text'] = ( isset( $input['api_key_text'] ) && ! empty( $input['api_key_text'] ) ) ? esc_attr( $input['api_key_text'] ) : 'api key';
		$options['selected_campaigns'] = ( isset($input['selected_campaigns'] ) && ! empty( $input['selected_campaigns'] ) ) ? esc_attr(implode(",",$input['selected_campaigns'])) : [];


		return $options;

	}

	public function options_update() {

		register_setting( $this->plugin_name, $this->plugin_name, array(
		'sanitize_callback' => array( $this, 'validate' ),
		) );

	}

	public function laposta_campaigns_function( $atts ) {

		// get options
		$options = get_option( $this->plugin_name );
		$api_key_text = ( isset( $options['api_key_text'] ) && ! empty( $options['api_key_text'] ) ) ? esc_attr( $options['api_key_text'] ) : 'api key';
        $selected_campaigns = ( isset( $options['selected_campaigns'] ) && ! empty( $options['selected_campaigns'] ) ) ? esc_attr($options['selected_campaigns']) : [];
        $campaigns = (isset($selected_campaigns) && ! empty($selected_campaigns)) ? explode(",", $selected_campaigns) : [];

	
		$args = shortcode_atts(
			array(
				'arg1'   => 'arg1',
				'arg2'   => 'arg2',
			),
			$atts
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.laposta.org/v2/campaign");
		curl_setopt($ch, CURLOPT_USERPWD, $api_key_text . ":");  
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		$count = 0;   // result counter
		$limit = 10;  // max results
		$html = "";
		
		if(!curl_errno($ch)){
			$data = json_decode($response, true);

			if (isset($data['error'])) {
				$html = $data['error']['message'] ?? null;
			} else {
				$html .= "<div class=\"display_archive\">";

				foreach ($data['data'] as $item) {

					// Stop if limit reached
					if ($count >= $limit) {
						break;
					}
					$campaign = $item['campaign'];

					// Must have delivery_ended
					if (empty($campaign['delivery_ended'])) {
						continue;
					}

					// Get list ids from campaign (keys of the object)
					$campaign_lists = array_keys($campaign['list_ids']);

					// Check for intersection
					$hasMatch = count(array_intersect($campaign_lists, $campaigns)) > 0;

						if (!$hasMatch) {
						continue;
					}

					// Format date dd/mm/yyyy
					$date = date('d/m/Y', strtotime($campaign['delivery_ended']));

					$subject = htmlspecialchars($campaign['subject']);
					$link = htmlspecialchars($campaign['web']);

					$html .= "<div class=\"campaign\">{$date} — <a href=\"{$link}\" target=\"_blank\">{$subject}</a></div>\n";

					$count++; // increment
				} 
				$html .=  "</div>";

				if ($count < 1) {
					$html = "No published campaigns could be found.";
				}
			}		
		}
		return $html;
	}

}



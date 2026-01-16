<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/arnoldligtvoet/campaigns-for-laposta
 * @since      1.0.0
 *
 * @package    Campaigns_For_Laposta
 * @subpackage Campaigns_For_Laposta/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Campaigns_For_Laposta
 * @subpackage Campaigns_For_Laposta/public
 * @author     Arnold Ligtvoet <arnold@ligtvoet.org>
 */
class Campaigns_For_Laposta_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/campaigns-for-laposta-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/campaigns-for-laposta-public.js', array( 'jquery' ), $this->version, false );

	}

	function laposta_campaigns_function( $atts ) {

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

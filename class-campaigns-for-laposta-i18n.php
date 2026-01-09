<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/arnoldligtvoet/campaigns-for-laposta
 * @since      1.0.0
 *
 * @package    Campaigns_For_Laposta
 * @subpackage Campaigns_For_Laposta/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Campaigns_For_Laposta
 * @subpackage Campaigns_For_Laposta/includes
 * @author     Arnold Ligtvoet <arnold@ligtvoet.org>
 */
class Campaigns_For_Laposta_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'campaigns-for-laposta',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}

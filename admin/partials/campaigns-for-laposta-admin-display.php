<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/arnoldligtvoet/campaigns-for-laposta
 * @since      1.0.0
 *
 * @package    Campaigns_For_Laposta
 * @subpackage Campaigns_For_Laposta/admin/partials
 */

// Set/Get defaults
$default_tab = null;
$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) die;
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2>Campaigns for Laposta <?php esc_attr_e('Settings', 'plugin_name' ); ?></h2>

    <form method="post" name="<?php echo $this->plugin_name; ?>" action="options.php">
    <?php
        //Grab all options
        $options = get_option( $this->plugin_name );
       
        $api_key_text = ( isset( $options['api_key_text'] ) && ! empty( $options['api_key_text'] ) ) ? esc_attr( $options['api_key_text'] ) : 'api key';
        $selected_campaigns = ( isset( $options['selected_campaigns'] ) && ! empty( $options['selected_campaigns'] ) ) ? esc_attr($options['selected_campaigns']) : [];
        $campaigns = (isset($selected_campaigns) && ! empty($selected_campaigns)) ? explode(",", $selected_campaigns) : [];

        settings_fields($this->plugin_name);
        do_settings_sections($this->plugin_name);

    ?>

    <!-- Here are our tabs -->
    <nav class="nav-tab-wrapper">
        <a href="?page=<?php echo $this->plugin_name; ?>" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">Settings</a>
        <a href="?page=<?php echo $this->plugin_name; ?>&tab=integrations" class="nav-tab <?php if($tab==='integrations'):?>nav-tab-active<?php endif; ?>">Integration</a>
    </nav>

    <div class="tab-content">
		<?php switch($tab) :
			case 'integrations':
                echo '<h4>Example data:</h4>';
				echo $this->laposta_campaigns_function('');

				?>
				<h4>Shortcode</h4>
				Use shortcode <b>[laposta-campaigns]</b> to show the sent campaigns in a page or post
				<?php
                break;
            default:
            ?>
            <!-- Text -->
            <fieldset>
                <p><h4><?php esc_attr_e( 'API Key.', 'plugin_name' ); ?></h4></p>
                <legend class="screen-reader-text">
                    <span><?php esc_attr_e( 'Enter API key from Laposta', 'plugin_name' ); ?></span>
                </legend>
                <input type="text" class="api_key_text" id="<?php echo $this->plugin_name; ?>-api_key_text" name="<?php echo $this->plugin_name; ?>[api_key_text]" value="<?php if( ! empty( $api_key_text ) ) echo $api_key_text; else echo 'api key'; ?>"/>
                <br>
				Create and manage your API keys at <a href="https://app.laposta.nl/config/c.connect/s.api/" target="_blank">the Laposta page</a>
            </fieldset>

            <!-- Checkboxes -->
            <fieldset>
                <?php
                if ($api_key_text != "api key") {
                    // call Laposta API to get all lists 
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://api.laposta.org/v2/list");
                    curl_setopt($ch, CURLOPT_USERPWD, $api_key_text . ":");  
                    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    if (($response) && (!curl_errno($ch))) {
                    
                        $data = json_decode($response, true);

                        if (!empty($data['data'])) {
                            ?> <h4>Select the lists you want to include</h4> <?php
                            foreach ($data['data'] as $item) {
                                $list = $item['list'];
                                echo '<label>';
                                if (in_array(htmlspecialchars($list['list_id']), $campaigns)) {
                                    // if the list was selected before, reselect it
                                    echo '<input type="checkbox" id="' . $this->plugin_name . '-selected_campaigns" name="' . $this->plugin_name . '[selected_campaigns][]" value="' . htmlspecialchars($list['list_id']) . '" checked> ';
                                } else {
                                    // list new or not previously selected
                                    echo '<input type="checkbox" id="' . $this->plugin_name . '-selected_campaigns" name="' . $this->plugin_name . '[selected_campaigns][]" value="' . htmlspecialchars($list['list_id']) . '"> ';
                                }
                                echo htmlspecialchars($list['name']);
                                echo '</label><br>';
                            }
                        }
                    }
                }
				?>
            </fieldset>

            <?php submit_button( __( 'Save all changes', 'plugin_name' ), 'primary','submit', TRUE ); ?>
            </form>
        <?php
		break;
    	endswitch; 
        ?>
    </div>
</div>

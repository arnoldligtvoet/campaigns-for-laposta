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

        /*
        $example_select = ( isset( $options['example_select'] ) && ! empty( $options['example_select'] ) ) ? esc_attr( $options['example_select'] ) : '1';
        $example_textarea = ( isset( $options['example_textarea'] ) && ! empty( $options['example_textarea'] ) ) ? sanitize_textarea_field( $options['example_textarea'] ) : 'default';
        $example_checkbox = ( isset( $options['example_checkbox'] ) && ! empty( $options['example_checkbox'] ) ) ? 1 : 0;
        */

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

           
            <?php
            /*
            <!-- Select -->
            <fieldset>
                <p><?php esc_attr_e( 'Example Select.', 'plugin_name' ); ?></p>
                <legend class="screen-reader-text">
                    <span><?php esc_attr_e( 'Example Select', 'plugin_name' ); ?></span>
                </legend>
                <label for="example_select">
                    <select name="<?php echo $this->plugin_name; ?>[example_select]" id="<?php echo $this->plugin_name; ?>-example_select">
                        <option <?php if ( $example_select == 'first' ) echo 'selected="selected"'; ?> value="first">First</option>
                        <option <?php if ( $example_select == 'second' ) echo 'selected="selected"'; ?> value="second">Second</option>
                    </select>
                </label>
            </fieldset>

             <!-- Textarea -->
            <fieldset>
                <p><?php esc_attr_e( 'Example Text.', 'plugin_name' ); ?></p>
                <legend class="screen-reader-text">
                    <span><?php esc_attr_e( 'Example Text', 'plugin_name' ); ?></span>
                </legend>
                <textarea class="example_textarea" id="<?php echo $this->plugin_name; ?>-example_textarea" name="<?php echo $this->plugin_name; ?>[example_textarea]" rows="4" cols="50">
                    <?php if( ! empty( $example_textarea ) ) echo $example_textarea; else echo 'default'; ?>
                </textarea>
            </fieldset>


            <!-- Checkbox -->
            <fieldset>
                <p><?php esc_attr_e( 'Example Checkbox.', 'plugin_name' ); ?></p>
                <legend class="example-Checkbox">
                    <span><?php esc_attr_e( 'Example Checkbox', 'plugin_name' ); ?></span>
                </legend>
                <label for="<?php echo $this->plugin_name; ?>-example_checkbox">
                    <input type="checkbox" id="<?php echo $this->plugin_name; ?>-example_checkbox" name="<?php echo $this->plugin_name; ?>[example_checkbox]" value="1" <?php checked( $example_checkbox, 1 ); ?> />
                    <span><?php esc_attr_e('Example Checkbox', 'plugin_name' ); ?></span>
                </label>
            </fieldset>
            */
            ?>

            <?php submit_button( __( 'Save all changes', 'plugin_name' ), 'primary','submit', TRUE ); ?>
            </form>
        <?php
		break;
    	endswitch; 
        ?>
    </div>
</div>

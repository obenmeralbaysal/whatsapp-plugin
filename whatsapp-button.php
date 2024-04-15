<?php
/*
Plugin Name: WhatsApp Button
Description: Custom plugin to add a WhatsApp button on your WordPress Site with one click.
Version: 1.0
Author: Oben Meralbaysal
Author URI: https://octoyazilim.com
*/

// Add Plugin Settings
function whatsapp_button_settings_page()
{
?>
    <div class="wrap">
        <h2>WhatsApp Button Settings</h2>
        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php
            settings_fields("whatsapp_button_settings");
            do_settings_sections("whatsapp_button");
            submit_button();
            ?>
        </form>
    </div>
<?php
}

function whatsapp_button_settings()
{
    register_setting("whatsapp_button_settings", "whatsapp_button_options", array(
        'sanitize_callback' => 'whatsapp_button_sanitize_options'
    ));
    add_settings_section("whatsapp_button_section", "General Settings", null, "whatsapp_button");
    add_settings_field("whatsapp_button_number", "WhatsApp Number", "whatsapp_button_number_callback", "whatsapp_button", "whatsapp_button_section");
    add_settings_field("whatsapp_button_image", "WhatsApp Button Image", "whatsapp_button_image_callback", "whatsapp_button", "whatsapp_button_section");
    add_settings_field("whatsapp_button_default_message", "Default Message", "whatsapp_button_default_message_callback", "whatsapp_button", "whatsapp_button_section");
    add_settings_field("whatsapp_button_position", "WhatsApp Button Position", "whatsapp_button_position_callback", "whatsapp_button", "whatsapp_button_section");
}

function whatsapp_button_sanitize_options($input)
{
    $sanitized_input = array();

    if (isset($input['whatsapp_button_number'])) {
        $sanitized_input['whatsapp_button_number'] = sanitize_text_field($input['whatsapp_button_number']);
    }

    if (isset($input['whatsapp_button_image'])) {
        $sanitized_input['whatsapp_button_image'] = esc_url_raw($input['whatsapp_button_image']);
    }

    if (isset($input['whatsapp_button_default_message'])) {
        $sanitized_input['whatsapp_button_default_message'] = sanitize_text_field($input['whatsapp_button_default_message']);
    }

    if (isset($input['whatsapp_button_position_top'])) {
        $sanitized_input['whatsapp_button_position_top'] = sanitize_text_field($input['whatsapp_button_position_top']);
    }

    if (isset($input['whatsapp_button_position_side'])) {
        $sanitized_input['whatsapp_button_position_side'] = sanitize_text_field($input['whatsapp_button_position_side']);
    }

    return $sanitized_input;
}

function whatsapp_button_number_callback()
{
    $options = get_option("whatsapp_button_options");
?>
    <input type="text" name="whatsapp_button_options[whatsapp_button_number]" value="<?php echo esc_attr($options['whatsapp_button_number']); ?>" />
    <p class="description">Enter the number without '+' eg. 15555555555 </p>

<?php
}

function whatsapp_button_image_callback()
{
    $options = get_option("whatsapp_button_options");
    $whatsapp_button_image = esc_url_raw($options['whatsapp_button_image']);
?>
    <div>
        <label for="whatsapp_button_image">Select Image:</label>
        <div class="whatsapp-button-images-container">
            <?php
            $plugin_dir_path = plugin_dir_path(__FILE__);
            $images_dir = $plugin_dir_path . 'images/';
            $images_url = plugins_url('images/', __FILE__);

            $allowed_images = array('whatsapp-button-1.png', 'whatsapp-button-2.png', 'whatsapp-button-3.png', 'whatsapp-button-4.png');

            foreach ($allowed_images as $image) {
                $image_path = $images_dir . $image;
                $image_url = $images_url . $image;
            ?>
                <label>
                    <input type="radio" name="whatsapp_button_options[whatsapp_button_image]" value="<?php echo esc_attr($image_url); ?>" <?php checked($whatsapp_button_image, $image_url); ?> />
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image); ?>" style="max-width: 50px; margin-right: 20px;" />
                </label>
            <?php
            }
            ?>
        </div>
        <br />
        <small>Preview:</small><br />
        <img src="<?php echo esc_attr($whatsapp_button_image); ?>" style="max-width: 100px;" />
    </div>
<?php
}

function whatsapp_button_default_message_callback()
{
    $options = get_option("whatsapp_button_options");
?>
    <input type="text" name="whatsapp_button_options[whatsapp_button_default_message]" value="<?php echo esc_attr($options['whatsapp_button_default_message']); ?>" />
    <p class="description">This message will be sent as the default message when the user clicks on the WhatsApp button.</p>
<?php
}

// Load Media Library
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_media();
});

function whatsapp_button_position_callback()
{
    $options = get_option("whatsapp_button_options");
    $whatsapp_button_position_top = esc_attr($options['whatsapp_button_position_top']);
    $whatsapp_button_position_side = esc_attr($options['whatsapp_button_position_side']);
?>
    <h4>Position Top/Bottom:</h4>
    <label>
        <input type="radio" name="whatsapp_button_options[whatsapp_button_position_top]" value="top" <?php checked($whatsapp_button_position_top, 'top'); ?> />
        Top
    </label><br />

    <label>
        <input type="radio" name="whatsapp_button_options[whatsapp_button_position_top]" value="bottom" <?php checked($whatsapp_button_position_top, 'bottom'); ?> />
        Bottom
    </label><br />

    <h4>Position Left - Right - Middle:</h4>
    <label>
        <input type="radio" name="whatsapp_button_options[whatsapp_button_position_side]" value="left" <?php checked($whatsapp_button_position_side, 'left'); ?> />
        Left
    </label><br />

    <label>
        <input type="radio" name="whatsapp_button_options[whatsapp_button_position_side]" value="right" <?php checked($whatsapp_button_position_side, 'right'); ?> />
        Right
    </label><br />

    <label>
        <input type="radio" name="whatsapp_button_options[whatsapp_button_position_side]" value="middle" <?php checked($whatsapp_button_position_side, 'middle'); ?> />
        Middle
    </label>
<?php
}

function display_whatsapp_button()
{
    $options = get_option("whatsapp_button_options");
    $whatsapp_number = sanitize_text_field($options["whatsapp_button_number"]);
    $whatsapp_button_image = esc_url_raw($options['whatsapp_button_image']);
    $whatsapp_button_default_message = sanitize_text_field($options['whatsapp_button_default_message']);
    $whatsapp_button_position_top = esc_attr($options['whatsapp_button_position_top']);
    $whatsapp_button_position_side = esc_attr($options['whatsapp_button_position_side']);

    $default_image_url = plugins_url('images/whatsapp-button-1.png', __FILE__);

    if (empty($whatsapp_button_image) || $whatsapp_button_image === $default_image_url) {
        $whatsapp_button_image = $default_image_url;
    }

    if (!empty($whatsapp_number)) {
        $style = 'position: fixed; z-index: 999999;';

        if ($whatsapp_button_position_top === 'top') {
            $style .= ' top: 15px;';
        } elseif ($whatsapp_button_position_top === 'bottom') {
            $style .= ' bottom: 15px;';
        }

        if ($whatsapp_button_position_side === 'left') {
            $style .= ' left: 15px;';
        } elseif ($whatsapp_button_position_side === 'right') {
            $style .= ' right: 15px;';
        } elseif ($whatsapp_button_position_side === 'middle') {
            $style .= 'left: 50%; transform: translateX(-50%);';
        }

        $button_html = '<a target="_blank" href="https://wa.me/' . $whatsapp_number . '?text=' . rawurlencode($whatsapp_button_default_message) . '" class="whatsapp-button" style="' . $style . '" target="_blank"><img src="' . $whatsapp_button_image . '" alt="WhatsApp" height="auto" width="50"/></a>';

        echo $button_html;
    }
}

add_action('wp_footer', 'display_whatsapp_button');

add_action("admin_menu", function () {
    add_menu_page("WhatsApp Button", "WhatsApp Button", "manage_options", "whatsapp_button", "whatsapp_button_settings_page");
    add_action("admin_init", "whatsapp_button_settings");
});
?>
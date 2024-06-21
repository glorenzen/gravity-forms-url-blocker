<?php
/*
Plugin Name: Gravity Forms URL Blocker
Description: Prevents form submission if a URL is found in specified textarea fields.
Version: 1.0
Author: Greg Lorenzen
*/

class GF_URL_Blocker {
    public function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('init', array($this, 'register_validation_hooks'));
    }

    public function admin_menu() {
        add_menu_page('GF URL Blocker', 'GF URL Blocker', 'manage_options', 'gf-url-blocker', array($this, 'admin_page'));
    }

    public function admin_scripts($hook) {
        if ($hook != 'toplevel_page_gf-url-blocker') return;
        wp_enqueue_script('gravity-forms-url-blocker-js', plugin_dir_url(__FILE__) . 'gravity-forms-url-blocker.js', array(), '1.0', true);
        wp_enqueue_style('gravity-forms-url-blocker-style', plugin_dir_url(__FILE__) . 'gravity-forms-url-blocker-styles.css');
    }

    public function admin_page() {
        // Check if the form is submitted and the nonce is valid
    if (isset($_POST['submit']) && check_admin_referer('update_gf_url_blocker_settings')) {
        // Check for user capability
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // Sanitize and validate input data
        $form_ids = isset($_POST['form_id']) ? array_map('sanitize_text_field', $_POST['form_id']) : array();
        $textarea_ids = isset($_POST['textarea_id']) ? array_map('sanitize_text_field', $_POST['textarea_id']) : array();

        // Combine form IDs and textarea IDs into an associative array
        $gf_url_blocker_data = array();
        foreach ($form_ids as $key => $form_id) {
            if (!empty($form_id) && !empty($textarea_ids[$key])) {
                $gf_url_blocker_data[] = array(
                    'form_id' => $form_id,
                    'textarea_id' => $textarea_ids[$key],
                );
            }
        }

        // Update the option in the database
        update_option('gf_url_blocker_forms', $gf_url_blocker_data);

        // Save the custom error message
        if (isset($_POST['gf_url_blocker_custom_message'])) {
            update_option('gf_url_blocker_custom_message', sanitize_text_field($_POST['gf_url_blocker_custom_message']));
        }

        // Use wp_redirect to refresh the page and add a query arg to confirm settings were saved
        $redirect_url = add_query_arg('settings-updated', 'true', wp_get_referer());
        wp_redirect($redirect_url);
        exit;
    }

    // Check if settings were updated and display a notice
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>';
        });
    }

    // Retrieve stored settings
    $gf_url_blocker_data = get_option('gf_url_blocker_forms', array());
    // Start of the form HTML
    ?>
    <div class="wrap">
        <h2>Gravity Forms URL Blocker</h2>
        <p>Please enter the Form ID and Textarea Field ID where URLs should be checked and blocked.</p>
        <p>Click the "Add More" button to add other forms to check.</p>
        <form method="post" action="">
            <?php wp_nonce_field('update_gf_url_blocker_settings'); ?>
            <div id="inputs_container">
                <?php foreach ($gf_url_blocker_data as $index => $data): ?>
                    <div class="input-group">
                        <input type="text" name="form_id[]" placeholder="Form ID" value="<?php echo esc_attr($data['form_id']); ?>" />
                        <input type="text" name="textarea_id[]" placeholder="Textarea ID" value="<?php echo esc_attr($data['textarea_id']); ?>" />
                        <button type="button" class="delete-input-group">Delete</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add_more">Add More</button>

            <!-- Custom Error Message Field -->
            <h3>Custom Error Message</h3>
            <?php
            $custom_message = get_option('gf_url_blocker_custom_message', 'Message cannot contain website addresses.');
            ?>
            <textarea name="gf_url_blocker_custom_message" rows="2" cols="50"><?php echo esc_html($custom_message); ?></textarea>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
    }

    public function register_validation_hooks() {
        $gf_url_blocker_data = get_option('gf_url_blocker_forms', array());
        foreach ($gf_url_blocker_data as $data) {
            if (isset($data['form_id']) && isset($data['textarea_id'])) {
                $filter_name = 'gform_field_validation_' . $data['form_id'] . '_' . $data['textarea_id'];
                add_filter($filter_name, array($this, 'validate_input'), 10, 4);
            }
        }
    }

    public function validate_input($result, $value, $form, $field) {
        $nourl_pattern = '/(https?:\/\/[^\s]+)/';
        $custom_message = get_option('gf_url_blocker_custom_message', 'Message cannot contain website addresses.');
        if (preg_match($nourl_pattern, $value)) {
            $result['is_valid'] = false;
            $result['message']  = $custom_message;
        }
        return $result;
    }

    public static function activate() {
        add_option('gf_url_blocker_forms', array());
    }
}

// Initialize the plugin
$gf_url_blocker = new GF_URL_Blocker();

// Activation hook
register_activation_hook(__FILE__, array('GF_URL_Blocker', 'activate'));
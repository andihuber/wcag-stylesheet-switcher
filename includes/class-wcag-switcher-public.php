<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package WCAG_Stylesheet_Switcher
 */

class WCAG_Switcher_Public {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 999);
        add_action('wp_footer', array($this, 'add_contrast_button_to_body'));
        add_action('wp_ajax_save_contrast_state', array($this, 'save_contrast_state'));
        add_action('wp_ajax_nopriv_save_contrast_state', array($this, 'save_contrast_state'));
        add_action('wp_footer', array($this, 'add_debug_panel'));
        add_filter('body_class', array($this, 'add_body_class'));
    }

    /**
     * Enqueue scripts and styles.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        if (!get_option('wcag_switcher_enabled', '1')) {
            return;
        }

        wp_enqueue_style('wcag-switcher', WCAG_SWITCHER_PLUGIN_URL . 'assets/css/wcag-switcher.css', array(), WCAG_SWITCHER_VERSION);
        wp_enqueue_script('wcag-switcher', WCAG_SWITCHER_PLUGIN_URL . 'assets/js/wcag-switcher.js', array('jquery'), WCAG_SWITCHER_VERSION, true);
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4');

        // Füge die JavaScript-Variablen hinzu
        wp_localize_script('wcag-switcher', 'wcagSwitcher', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wcag_switcher_nonce'),
            'pluginUrl' => WCAG_SWITCHER_PLUGIN_URL,
            'isAdmin' => current_user_can('manage_options')
        ));
    }

    /**
     * Add contrast button to body.
     *
     * @since    1.0.0
     */
    public function add_contrast_button_to_body() {
        if (!get_option('wcag_switcher_enabled', '1')) {
            return;
        }

        $position = get_option('wcag_switcher_position', 'menu');
        
        // Wenn die Position "menu" ist, fügen wir den Button nicht zum Body hinzu
        if ($position === 'menu') {
            return;
        }

        $position_class = $position === 'left' ? 'left-position' : 'right-position';
        
        echo '<div class="wcag-contrast-button ' . esc_attr($position_class) . '">';
        echo '<a href="#" class="wcag-contrast-toggle" aria-label="' . esc_attr__('Kontrast-Modus umschalten', 'wcag-switcher') . '">';
        echo '<i class="fas fa-adjust"></i>';
        echo '</a>';
        echo '</div>';
    }

    /**
     * Add debug panel to footer.
     *
     * @since    1.0.0
     */
    public function add_debug_panel() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div id="wcag-debug-panel" style="display: none;">
            <div class="wcag-debug-header">
                <h3>WCAG Switcher Debug</h3>
                <button class="wcag-debug-toggle">Toggle Panel</button>
            </div>
            <div class="wcag-debug-content">
                <div class="wcag-debug-section">
                    <h4>Kontrast-Status</h4>
                    <p>Status: <span id="wcag-debug-status">Inaktiv</span></p>
                    <button class="wcag-debug-reset">Status zurücksetzen</button>
                </div>
                <div class="wcag-debug-section">
                    <h4>LocalStorage</h4>
                    <pre id="wcag-debug-storage"></pre>
                </div>
                <div class="wcag-debug-section">
                    <h4>CSS-Datei</h4>
                    <pre id="wcag-debug-css"></pre>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Save contrast state via AJAX.
     *
     * @since    1.0.0
     */
    public function save_contrast_state() {
        check_ajax_referer('wcag_switcher_nonce', 'nonce');
        
        $state = isset($_POST['state']) ? sanitize_text_field($_POST['state']) : '';
        if ($state) {
            wp_send_json_success(array('state' => $state));
        } else {
            wp_send_json_error();
        }
    }

    /**
     * Add WCAG Switcher class to body.
     *
     * @since    1.0.0
     * @param    array    $classes    The body classes.
     * @return   array    The modified body classes.
     */
    public function add_body_class($classes) {
        if (get_option('wcag_switcher_enabled', '1') === '1') {
            $classes[] = 'wcag-switcher-active';
        }
        return $classes;
    }
} 
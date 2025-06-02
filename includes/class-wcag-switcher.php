<?php
/**
 * The main plugin class.
 *
 * @package WCAG_Stylesheet_Switcher
 */

class WCAG_Switcher {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      WCAG_Switcher_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->plugin_name = 'wcag-stylesheet-switcher';
        $this->version = WCAG_SWITCHER_VERSION;

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        require_once WCAG_SWITCHER_PLUGIN_DIR . 'includes/admin/class-wcag-switcher-admin.php';
        require_once WCAG_SWITCHER_PLUGIN_DIR . 'includes/class-wcag-switcher-public.php';
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new WCAG_Switcher_Admin();
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new WCAG_Switcher_Public();

        // Registriere die Hooks für den Kontrast-Button
        add_filter('wp_nav_menu_items', array($this, 'add_contrast_button'), 10, 2);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        // Plugin is now running
    }

    /**
     * Add contrast button to navigation menu.
     *
     * @since    1.0.0
     * @param    array    $items    The menu items.
     * @param    object   $args     The menu arguments.
     * @return   array              The modified menu items.
     */
    public function add_contrast_button($items, $args) {
        if (!get_option('wcag_switcher_enabled', '1')) {
            return $items;
        }

        $position = get_option('wcag_switcher_position', 'menu');
        
        // Wenn die Position nicht "menu" ist, fügen wir den Button nicht zum Menü hinzu
        if ($position !== 'menu') {
            return $items;
        }

        $button = '<li class="wcag-contrast-button menu-position">';
        $button .= '<a href="#" class="wcag-contrast-toggle" aria-label="' . esc_attr__('Kontrast-Modus umschalten', 'wcag-switcher') . '">';
        $button .= '<i class="fas fa-adjust"></i>';
        $button .= '</a>';
        $button .= '</li>';

        return $items . $button;
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

        // Füge den Button zum Body hinzu, wenn die Position nicht "menu" ist
        add_action('wp_footer', array($this, 'add_contrast_button_to_body'));
    }
} 
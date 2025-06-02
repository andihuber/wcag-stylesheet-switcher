<?php
/**
 * Plugin Name: WCAG Stylesheet Switcher
 * Plugin URI: https://github.com/andihuber/wcag-stylesheet-switcher
 * Description: A WordPress plugin that allows users to switch between different WCAG-compliant stylesheets.
 * Version: 1.0.0
 * Author: Andreas Huber
 * Author URI: https://www.andreas-huber.at
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wcag-stylesheet-switcher
 * Domain Path: /languages
 *
 * @package WCAG_Stylesheet_Switcher
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('WCAG_SWITCHER_VERSION', '1.0.0');
define('WCAG_SWITCHER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WCAG_SWITCHER_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once WCAG_SWITCHER_PLUGIN_DIR . 'includes/class-wcag-switcher.php';

// Initialize the plugin
function run_wcag_switcher() {
    $plugin = new WCAG_Switcher();
    $plugin->run();
}

// Aktivierungsfunktion
function activate_wcag_switcher() {
    // Initialisiere die CSS-Datei mit dem Inhalt aus der Datenbank
    $css_file = WCAG_SWITCHER_PLUGIN_DIR . 'assets/css/wcag-contrast.css';
    $css_content = get_option('wcag_switcher_css');
    
    if (!empty($css_content)) {
        // Stelle sicher, dass das Verzeichnis existiert
        if (!file_exists(dirname($css_file))) {
            wp_mkdir_p(dirname($css_file));
        }
        
        // Schreibe den CSS-Inhalt in die Datei
        file_put_contents($css_file, $css_content);
    }
}
register_activation_hook(__FILE__, 'activate_wcag_switcher');

run_wcag_switcher(); 
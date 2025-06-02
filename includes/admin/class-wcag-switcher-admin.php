<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package WCAG_Stylesheet_Switcher
 */

class WCAG_Switcher_Admin {

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('update_option_wcag_switcher_css', array($this, 'save_css_file'), 10, 3);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_codemirror'));
        add_action('admin_init', array($this, 'sync_css_file'));
    }

    /**
     * Enqueue CodeMirror scripts and styles.
     *
     * @since    1.0.0
     */
    public function enqueue_codemirror() {
        $screen = get_current_screen();
        if ($screen->id !== 'settings_page_wcag-switcher') {
            return;
        }

        // Enqueue WordPress code editor
        wp_enqueue_code_editor(array('type' => 'text/css'));
        
        // Enqueue WordPress admin scripts
        wp_enqueue_script('jquery');
        wp_enqueue_script('wp-codemirror');
        wp_enqueue_style('wp-codemirror');
        
        // Enqueue linting
        wp_enqueue_script('csslint');
        wp_enqueue_script('jshint');
        wp_enqueue_script('htmlhint');
        wp_enqueue_script('htmlhint-kses');
        
        // Enqueue WordPress code editor settings
        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');
    }

    /**
     * Add options page to the admin menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        add_options_page(
            'WCAG Stylesheet Switcher',
            'WCAG Switcher',
            'manage_options',
            'wcag-switcher',
            array($this, 'display_settings_page')
        );
    }

    /**
     * Register plugin settings.
     *
     * @since    1.0.0
     */
    public function register_settings() {
        register_setting('wcag_switcher_options', 'wcag_switcher_css');
        register_setting('wcag_switcher_options', 'wcag_switcher_enabled');
        register_setting('wcag_switcher_options', 'wcag_switcher_position', array(
            'default' => 'menu',
            'sanitize_callback' => array($this, 'sanitize_position')
        ));
    }

    /**
     * Sanitize position value.
     *
     * @since    1.0.0
     * @param    string    $position    The position value.
     * @return   string                 The sanitized position value.
     */
    public function sanitize_position($position) {
        $allowed_positions = array('menu', 'left', 'right');
        return in_array($position, $allowed_positions) ? $position : 'menu';
    }

    /**
     * Save CSS content to file.
     *
     * @since    1.0.0
     * @param    mixed    $old_value    The old option value.
     * @param    mixed    $value        The new option value.
     * @param    string   $option       The option name.
     */
    public function save_css_file($old_value, $value, $option) {
        $css_file = WCAG_SWITCHER_PLUGIN_DIR . 'assets/css/wcag-contrast.css';
        
        // Stelle sicher, dass das Verzeichnis existiert
        if (!file_exists(dirname($css_file))) {
            wp_mkdir_p(dirname($css_file));
        }
        
        // Schreibe den CSS-Inhalt in die Datei
        if (file_put_contents($css_file, $value) === false) {
            // Fehler beim Schreiben der Datei
            add_settings_error(
                'wcag_switcher_css',
                'css_file_error',
                __('Fehler beim Speichern der CSS-Datei. Bitte überprüfen Sie die Dateiberechtigungen.', 'wcag-switcher'),
                'error'
            );
        } else {
            // Erfolgreich gespeichert
            add_settings_error(
                'wcag_switcher_css',
                'css_file_success',
                __('CSS-Datei wurde erfolgreich aktualisiert.', 'wcag-switcher'),
                'success'
            );
        }
    }

    /**
     * Synchronisiere CSS-Datei mit Datenbank-Inhalt.
     *
     * @since    1.0.0
     */
    public function sync_css_file() {
        $css_file = WCAG_SWITCHER_PLUGIN_DIR . 'assets/css/wcag-contrast.css';
        $css_content = get_option('wcag_switcher_css');
        
        // Wenn die Datei nicht existiert oder leer ist, aber Inhalt in der Datenbank vorhanden ist
        if ((!file_exists($css_file) || filesize($css_file) === 0) && !empty($css_content)) {
            // Stelle sicher, dass das Verzeichnis existiert
            if (!file_exists(dirname($css_file))) {
                wp_mkdir_p(dirname($css_file));
            }
            
            // Schreibe den CSS-Inhalt in die Datei
            if (file_put_contents($css_file, $css_content) === false) {
                // Fehler beim Schreiben der Datei
                add_settings_error(
                    'wcag_switcher_css',
                    'css_file_error',
                    __('Fehler beim Synchronisieren der CSS-Datei. Bitte überprüfen Sie die Dateiberechtigungen.', 'wcag-switcher'),
                    'error'
                );
            }
        }
    }

    /**
     * Render the settings page.
     *
     * @since    1.0.0
     */
    public function display_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wcag_switcher_options');
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Switcher aktivieren', 'wcag-switcher'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="wcag_switcher_enabled" value="1" <?php checked(get_option('wcag_switcher_enabled', '1'), '1'); ?>>
                                <?php _e('Kontrast-Switcher im Frontend anzeigen', 'wcag-switcher'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Button Position', 'wcag-switcher'); ?></th>
                        <td>
                            <select name="wcag_switcher_position">
                                <option value="menu" <?php selected(get_option('wcag_switcher_position', 'menu'), 'menu'); ?>>
                                    <?php _e('In der Navigation', 'wcag-switcher'); ?>
                                </option>
                                <option value="left" <?php selected(get_option('wcag_switcher_position', 'menu'), 'left'); ?>>
                                    <?php _e('Links (40% von oben)', 'wcag-switcher'); ?>
                                </option>
                                <option value="right" <?php selected(get_option('wcag_switcher_position', 'menu'), 'right'); ?>>
                                    <?php _e('Rechts (40% von oben)', 'wcag-switcher'); ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Kontrast CSS', 'wcag-switcher'); ?></th>
                        <td>
                            <div class="notice notice-info inline">
                                <p>
                                    <?php _e('Tipp: Falls die CSS-Überschreibungen nicht wie gewünscht funktionieren, können Sie auch die Body-Klasse "wcag-switcher-active" verwenden, um spezifischere Selektoren zu erstellen.', 'wcag-switcher'); ?>
                                </p>
                            </div>
                            <?php
                            $css_content = get_option('wcag_switcher_css');
                            ?>
                            <textarea id="wcag_switcher_css" name="wcag_switcher_css" class="large-text code" rows="20"><?php echo esc_textarea($css_content); ?></textarea>
                            <p class="description">
                                <?php _e('CSS-Regeln für den Kontrast-Modus. Diese werden nur angewendet, wenn der Kontrast-Modus aktiv ist.', 'wcag-switcher'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <script>
        jQuery(document).ready(function($) {
            if (wp.codeEditor) {
                var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
                editorSettings.codemirror = _.extend(
                    {},
                    editorSettings.codemirror,
                    {
                        indentUnit: 4,
                        tabSize: 4,
                        mode: 'css',
                        lineNumbers: true,
                        lineWrapping: true,
                        theme: 'default',
                        lint: true
                    }
                );
                var editor = wp.codeEditor.initialize($('#wcag_switcher_css'), editorSettings);
            }
        });
        </script>
        <?php
    }
} 
<?php
/**
 * Plugin Name:     Me Floating WhatsApp
 * Author:          Fachri Riyanto
 * Author URI:      https://fachririyanto.com
 * Description:     Customizeable floating WhatsApp icon.
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Version:         1.0.0
 */
if ( ! ABSPATH ) {
    return;
}
define( 'ME_FLOATING_WA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin hooks.
 * 
 * @since 1.0.0
 */
class Me_Floating_WhatsApp {
    /**
     * Version.
     * 
     * @var string
     */
    var $version = '1.0.0';

    /**
     * Setup plugin.
     * 
     * @uses register_activation_hook()
     * @uses register_deactivation_hook()
     * @uses add_action()
     * @since 1.0.0
     */
    function init() {
        // activation / deactivation hook
        register_activation_hook( __FILE__, array( $this, 'on_activated' ) );
        register_deactivation_hook( __FILE__, array( $this, 'on_deactivated' ) );

        // register admin menu
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'load_wp_media' ) );

        // render icon
        add_action( 'wp_footer', array( $this, 'render' ) );
    }

    /**
     * Do when plugin is activated.
     * 
     * @since 1.0.0
     */
    function on_activated() {}

    /**
     * Do when plugin is deactivated.
     * 
     * @since 1.0.0
     */
    function on_deactivated() {}

    /**
     * Register admin menu.
     * 
     * @uses add_menu_page()
     * @since 1.0.0
     */
    function register_admin_menu() {
        add_menu_page(
            'Floating WA',
            'Floating WA',
            'manage-options',
            'me-floating-wa',
            array( $this, 'render_admin' )
        );
    }

    /**
     * Load wp.media.
     * 
     * @uses wp_enqueue_media()
     * @since 1.0.0
     */
    function load_wp_media() {
        if ( isset( $_GET['page'] ) && $_GET['page'] == 'me-floating-wa' ) {
            wp_enqueue_media();
        }
    }

    /**
     * Render admin.
     * 
     * @return void
     * @since 1.0.0
     */
    function render_admin() {
        require_once ME_FLOATING_WA_PLUGIN_DIR . 'admin/index.php';
    }

    /**
     * Render icon.
     * 
     * @uses plugins_url()
     * @uses esc_url()
     * @return void
     * @since 1.0.0
     */
    function render() {
        // load settings
        $settings = me_floating_wa_get_settings();

        // extract settings
        $size    = empty( $settings['size'] ) ? 56 : $settings['size'];
        $y_axis  = empty( $settings['y_axis'] ) ? 20 : $settings['y_axis'];
        $x_axis  = empty( $settings['x_axis'] ) ? 20 : $settings['x_axis'];
        $z_index = empty( $settings['z_index'] ) ? 100 : $settings['z_index'];

        // define image
        $image_url = me_floating_wa_image_url( $settings['image'] );

        // define url
        $action_url = 'https://wa.me/' . $settings['phone'] . '?text=' . $settings['message'];
        ?>
        <style type="text/css">
            .me-floating-whatsapp {
                position: fixed;
                bottom: <?php echo $y_axis; ?>px;
                right: <?php echo $x_axis; ?>px;
                z-index: <?php echo $z_index; ?>;
                max-width: <?php echo $size; ?>px;
            }
            .me-floating-whatsapp img {
                display: block;
                width: 100%;
                height: auto;
            }
        </style>
        <div class="me-floating-whatsapp">
            <a href="<?php echo esc_url( $action_url ); ?>" target="<?php echo $settings['open_tab'] == 'yes' ? '_blank' : '_self'; ?>">
                <img src="<?php echo esc_url( $image_url ); ?>" alt="Logo WhatsApp" loading="lazy">
            </a>
        </div>
        <?php
    }
}

/**
 * Get settings.
 * 
 * @uses wp_parse_args()
 * @uses get_option()
 * @return array $settings
 * @since 1.0.0
 */
function me_floating_wa_get_settings() {
    $default_state = array(
        'image'    => '',
        'phone'    => '',
        'message'  => '',
        'size'     => '',
        'position' => 'bottom-right',
        'y_axis'   => '',
        'x_axis'   => '',
        'z_index'  => '',
        'open_tab' => 'no'
    );
    $settings = get_option( 'me_floating_wa_options', array() );
    return wp_parse_args( $settings, $default_state );
}

/**
 * Get default image url.
 * 
 * @uses apply_filters()
 * @uses plugins_url()
 * @return string $default_image
 * @since 1.0.0
 */
function me_floating_wa_default_image() {
    return apply_filters( 'me_floating_wa_default_image', plugins_url( '/images/logo-whatsapp.png', __FILE__ ) );
}

/**
 * Get image URL.
 * 
 * @uses wp_get_attachment_image_src()
 * @uses apply_filters()
 * @param int $image_id
 * @return string $image_url
 * @since 1.0.0
 */
function me_floating_wa_image_url( $image_id ) {
    $default_image = me_floating_wa_default_image();
    $image_url     = '';
    if ( empty( $image_id ) ) {
        $image_url = $default_image;
    } else {
        $image     = wp_get_attachment_image_src( $image_id, apply_filters( 'me_floating_wa_image_size', 'thumbnail' ) );
        $image_url = $image[0];
    }
    return $image_url;
}

/**
 * RUN PLUGIN.
 */
$plugin = new Me_Floating_WhatsApp();
$plugin->init();

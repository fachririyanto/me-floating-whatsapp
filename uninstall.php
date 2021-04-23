<?php
/**
 * Uninstall plugin.
 * 
 * @todo Delete option(s)
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die();
}
delete_option( 'me_floating_wa_options' );

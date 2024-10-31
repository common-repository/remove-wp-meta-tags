<?php

/**
 * The admin-facing functionality of the plugin.
 *
 * @package    Easy Header Footer
 * @subpackage Admin
 * @author     Sayan Datta
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 */

add_action( 'admin_notices', 'ehf_rating_admin_notice' );
add_action( 'admin_init', 'ehf_dismiss_rating_admin_notice' );

function ehf_rating_admin_notice() {
    // Show notice after 240 hours (10 days) from installed time.
    if ( ehf_plugin_get_installed_time() > strtotime( '-240 hours' )
        || '1' === get_option( 'ehf_plugin_dismiss_rating_notice' )
        || ! current_user_can( 'manage_options' )
        || apply_filters( 'ehf_plugin_show_sticky_notice', false ) ) {
        return;
    }

    $dismiss = wp_nonce_url( add_query_arg( 'ehf_rating_notice_action', 'dismiss_rating_true' ), 'ehf_dismiss_rating_true' ); 
    $no_thanks = wp_nonce_url( add_query_arg( 'ehf_rating_notice_action', 'no_thanks_rating_true' ), 'ehf_no_thanks_rating_true' ); ?>
    
    <div class="notice notice-success">
        <p><?php _e( 'Hey, I noticed you\'ve been using Easy Header Footer for more than 1 week – that’s awesome! Could you please do me a BIG favor and give it a <strong>5-star</strong> rating on WordPress? Just to help me spread the word and boost my motivation.', 'remove-wp-meta-tags' ); ?></p>
        <p><a href="https://wordpress.org/support/plugin/remove-wp-meta-tags/reviews/?filter=5#new-post" target="_blank" class="button button-secondary"><?php _e( 'Ok, you deserve it', 'remove-wp-meta-tags' ); ?></a>&nbsp;
        <a href="<?php echo $dismiss; ?>" class="already-did"><strong><?php _e( 'I already did', 'remove-wp-meta-tags' ); ?></strong></a>&nbsp;<strong>|</strong>
        <a href="<?php echo $no_thanks; ?>" class="later"><strong><?php _e( 'Nope&#44; maybe later', 'remove-wp-meta-tags' ); ?></strong></a></p>
    </div>
<?php
}

function ehf_dismiss_rating_admin_notice() {

    if( get_option( 'ehf_plugin_no_thanks_rating_notice' ) === '1' ) {
        if ( get_option( 'ehf_plugin_dismissed_time' ) > strtotime( '-168 hours' ) ) {
            return;
        }
        delete_option( 'ehf_plugin_dismiss_rating_notice' );
        delete_option( 'ehf_plugin_no_thanks_rating_notice' );
    }

    if ( ! isset( $_GET['ehf_rating_notice_action'] ) ) {
        return;
    }

    if ( 'dismiss_rating_true' === $_GET['ehf_rating_notice_action'] ) {
        check_admin_referer( 'ehf_dismiss_rating_true' );
        update_option( 'ehf_plugin_dismiss_rating_notice', '1' );
    }

    if ( 'no_thanks_rating_true' === $_GET['ehf_rating_notice_action'] ) {
        check_admin_referer( 'ehf_no_thanks_rating_true' );
        update_option( 'ehf_plugin_no_thanks_rating_notice', '1' );
        update_option( 'ehf_plugin_dismiss_rating_notice', '1' );
        update_option( 'ehf_plugin_dismissed_time', time() );
    }

    wp_redirect( remove_query_arg( 'ehf_rating_notice_action' ) );
    exit;
}

function ehf_plugin_get_installed_time() {
    $installed_time = get_option( 'ehf_plugin_installed_time' );
    if ( ! $installed_time ) {
        $installed_time = time();
        update_option( 'ehf_plugin_installed_time', $installed_time );
    }
    return $installed_time;
}
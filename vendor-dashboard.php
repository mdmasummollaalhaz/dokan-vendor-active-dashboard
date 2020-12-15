remove_filter( 'show_admin_bar', 'dokan_disable_admin_bar'  );
add_filter( 'show_admin_bar', 'wpadev_disable_admin_bar' );
/**
 * Prevent seeing the admin bar
 */
function wpadev_disable_admin_bar( $show_admin_bar ) {
    global $current_user;

    if ( $current_user->ID !== 0 ) {
        $role = reset( $current_user->roles );

        if ( in_array( $role, array( 'seller' ) ) ) {
            if ( dokan_is_seller_enabled( $current_user->ID ) ) {
                return true;
            }
        }

        if ( in_array( $role, array( 'seller', 'customer', 'vendor_staff' ) ) ) {
            return false;
        }
    }

    return $show_admin_bar;
}

add_action( 'admin_init', 'wpadev_block_admin_access' );

/**
 * Block user access to admin panel.
 */
function wpadev_block_admin_access() {
    global $pagenow, $current_user;

    // bail out if we are from WP Cli
    if ( defined( 'WP_CLI' ) ) {
        return;
    }

    $valid_pages = array( 'admin-ajax.php', 'admin-post.php', 'async-upload.php', 'media-upload.php' );
    $user_role   = reset( $current_user->roles );

    if ( ! dokan_is_seller_enabled( $current_user->ID ) && ( ! in_array( $pagenow, $valid_pages ) ) && in_array( $user_role, array( 'seller', 'customer', 'vendor_staff' ) ) ) {
        wp_redirect( home_url() );
        exit;
    }
}

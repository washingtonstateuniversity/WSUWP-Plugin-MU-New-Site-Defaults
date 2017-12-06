<?php

namespace WSUWP\New_Site_Defaults;

add_action( 'wpmu_new_blog', 'WSUWP\New_Site_Defaults\set_site_defaults', 10 );

/**
 * Set the defaults used by sites created on the WSUWP Platform.
 *
 * @since 0.0.1
 */
function set_site_defaults( $site_id ) {

	switch_to_blog( $site_id );

	// Update the default category name to "General" from "Uncategorized".
	wp_update_term( 1, 'category', array(
		'name' => 'General',
		'slug' => 'general',
	) );

	// Delete the first comment auto-created by WordPress during install.
	wp_delete_comment( 1 );

	// Update the title of the first post created by WordPress, "Hello World".
	wp_update_post( array(
		1,
		'post_title' => apply_filters( 'wsuwp_first_title', 'Sample Post' ),
		'post_content' => apply_filters( 'wsuwp_first_post_content', '' ),
	) );

	// Update the title of the first page created by WordPress, "Sample Page".
	wp_update_post( array(
		2,
		'post_title' => apply_filters( 'wsuwp_first_page_title', 'Home Page' ),
		'post_content' => apply_filters( 'wsuwp_first_page_content', '' ),
	) );

	// Add a new "News" page to be used for showing posts.
	$post_id = wp_insert_post( array(
		'post_type' => 'page',
		'post_title' => 'News',
		'post_content' => 'This is a placeholder page for news items. Editing is not recommended.',
	) );

	// Set the "Page on Front" setting by default.
	update_option( 'show_on_front', 'page' );

	// Set the page on front to the home page.
	update_option( 'page_on_front', 2 );

	// Set the page for posts to the News page.
	update_option( 'page_for_posts', (int) $post_id );

	// Set a default, but filtered site description for WSU sites.
	update_option( 'blogdescription', apply_filters( 'wsuwp_install_site_description', 'A WSU WordPress Site' ) );

	// Set a default, but filtered timezone for Pacific time.
	update_option( 'timezone_string', apply_filters( 'wsuwp_install_default_timezone_string', 'America/Los_Angeles' ) );

	// Setup default image sizes, allowing them to be filtered.
	$default_image_sizes = array(
		'thumbnail_size_w' => 198,
		'thumbnail_size_h' => 198,
		'medium_size_w'    => 396,
		'medium_size_h'    => 99164,
		'large_size_w'     => 792,
		'large_size_h'     => 99164,
	);
	$image_sizes = apply_filters( 'wsuwp_install_default_image_sizes', $default_image_sizes );

	foreach ( $image_sizes as $size => $val ) {
		if ( array_key_exists( $size, $default_image_sizes ) ) {
			update_option( $size, absint( $val ) );
		}
	}

	restore_current_blog();
}

<?php

namespace WSUWP\New_Site_Defaults;

add_action( 'wpmu_new_blog', 'WSUWP\New_Site_Defaults\set_site_defaults', 10 );
add_action( 'wpmu_new_blog', 'WSUWP\New_Site_Defaults\set_project_site_defaults', 11, 3 ); // project.wsu.edu
add_action( 'wpmu_new_blog', 'WSUWP\New_Site_Defaults\set_sites_site_defaults', 11, 3 );   // sites.wsu.edu

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
	wp_delete_comment( 1, true );

	// Update the title of the first post created by WordPress, "Hello World".
	wp_update_post( array(
		'ID' => 1,
		'post_title' => apply_filters( 'wsuwp_first_title', 'Sample Post' ),
		'post_content' => apply_filters( 'wsuwp_first_post_content', '' ),
	) );

	// Update the title of the first page created by WordPress, "Sample Page".
	wp_update_post( array(
		'ID' => 2,
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

/**
 * Preconfigure a Project site to reduce the overall setup experience.
 *
 * @since 0.0.1
 *
 * @param int    $site_id ID of the site being created.
 * @param int    $user_id ID of the user creating the site.
 * @param string $domain  Domain of the site being created.
 */
function set_project_site_defaults( $site_id, $user_id, $domain ) {

	// Only apply these defaults to project sites.
	if ( ! in_array( $domain, array( 'project.wsu.edu', 'project.wsu.dev' ), true ) ) {
		return;
	}

	switch_to_blog( $site_id );

	// Show posts on the front page rather than a page.
	update_option( 'show_on_front', 'posts' );

	// Activate the WSU Project theme by default.
	update_option( 'stylesheet', 'p2-wsu' );
	update_option( 'template', 'p2-wsu' );

	// Restrict access to logged in users only.
	update_option( 'blog_public', 2 );

	// We're only prepared for SSL on production.
	if ( 'project.wsu.edu' === $domain ) {
		// Replace HTTP with HTTPS in the site and home URLs.
		$site_url = get_option( 'siteurl' );
		$site_url = str_replace( 'http://', 'https://', $site_url );
		update_option( 'siteurl', $site_url );
		$home_url = get_option( 'home' );
		$home_url = str_replace( 'http://', 'https://', $home_url );
		update_option( 'home', $home_url );
	}

	// Setup common P2 widgets.
	update_option( 'widget_mention_me', array(
		2 => array(
			'title' => '',
			'num_to_show' => 5,
			'avatar_size' => 32,
			'show_also_post_followups' => false,
			'show_also_comment_followups' => false,
		),
		'_multiwidget' => 1,
	) );

	update_option( 'widget_p2_recent_tags', array(
		2 => array(
			'title' => '',
			'num_to_show' => 15,
		),
		'_multiwidget' => 1,
	) );

	update_option( 'widget_p2_recent_comments', array(
		2 => array(
			'title' => '',
			'num_to_show' => 5,
			'avatar_size' => 32,
		),
		'_multiwidget' => 1,
	) );

	update_option( 'sidebars_widgets', array(
		'wp_inactive_widgets' => array(),
		'sidebar-1' => array(
			0 => 'search-2',
			1 => 'mention_me-2',
			2 => 'p2_recent_tags-2',
			3 => 'p2_recent_comments-2',
			4 => 'recent-posts-2',
		),
		'sidebar-2' => array(),
		'sidebar-3' => array(),
		'array_version' => 3,
	) );

	wp_schedule_single_event( time() + 5, 'wsuwp_project_flush_rewrite_rules' );
	wp_cache_delete( 'alloptions', 'options' );
	restore_current_blog();

	clean_blog_cache( $site_id );
}

/**
 * Preconfigure a student portfolio site to reduce the overall setup experience.
 *
 * @since 0.0.1
 *
 * @param int    $site_id ID of the site being created.
 * @param int    $user_id ID of the user creating the site.
 * @param string $domain  Domain of the site being created.
 */
function set_sites_site_defaults( $site_id, $user_id, $domain ) {

	// Only apply these defaults to sites sites. ;)
	if ( ! in_array( $domain, array( 'sites.wsu.edu', 'sites.wsu.dev' ), true ) ) {
		return;
	}

	switch_to_blog( $site_id );

	// Show posts on the front page rather than a page. Sites are primarily for
	// student portfolios and will likely have a log format.
	update_option( 'show_on_front', 'posts' );

	// Restrict access to logged in users only.
	update_option( 'blog_public', 2 );

	// We're prepared for SSL everywhere on production.
	if ( 'sites.wsu.edu' === $domain ) {
		// Replace HTTP with HTTPS in the site and home URLs.
		$site_url = get_option( 'siteurl' );
		$site_url = str_replace( 'http://', 'https://', $site_url );
		update_option( 'siteurl', $site_url );
		$home_url = get_option( 'home' );
		$home_url = str_replace( 'http://', 'https://', $home_url );
		update_option( 'home', $home_url );
	}

	wp_cache_delete( 'alloptions', 'options' );
	restore_current_blog();

	clean_blog_cache( $site_id );
}

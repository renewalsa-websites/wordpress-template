<?php

/*
 * Plugin Name: RSA Secrurity Compliance
 * Description: Tweaks to WordPress to make it more secure and comply with RSA securit scanning.
 * Author: Renewal SA / Frame Creative
 * Author URI: https://github.com/renewalsa-websites
 * Version: 1.0.0
 * Plugin URI: https://github.com/renewalsa-websites
 */

// Psudo namespace for backwards compatibility in case we need to drop this into old sites.
 $prefix = 'rsa_security_';

// Redirects all feeds to home page.
function rsa_security_disable_feeds(): void
{
    wp_redirect(home_url());
    exit;
}

// Disable default users API endpoints for security.
// https://www.wp-tweaks.com/hackers-can-find-your-wordpress-username/
function rsa_security_disable_rest_endpoints(array $endpoints): array
{
    if (!is_user_logged_in()) {
        if (isset($endpoints['/wp/v2/users'])) {
            unset($endpoints['/wp/v2/users']);
        }

        if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
            unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
        }
    }

    return $endpoints;
}

// Update login page image link URL.
function rsa_security_login_url(): string
{
    return home_url();
}

// Update login page link title.
function rsa_security_login_title(): string
{
    return get_bloginfo('name');
}

// Remove Gutenberg's front-end block styles.
function rsa_security_remove_block_styles(): void
{
    wp_deregister_style('wp-block-library');
    wp_deregister_style('wp-block-library-theme');
}

// Remove core block styles.
// https://github.com/WordPress/gutenberg/issues/56065
function rsa_security_remove_core_block_styles(): void
{
    wp_dequeue_style('core-block-supports');
}

// Remove Gutenberg's global styles.
// https://github.com/WordPress/gutenberg/pull/34334#issuecomment-911531705
function rsa_security_remove_global_styles(): void
{
    wp_dequeue_style('global-styles');
}

// Remove classic theme styles.
// https://github.com/WordPress/WordPress/commit/143fd4c1f71fe7d5f6bd7b64c491d9644d861355
function rsa_security_remove_classic_theme_styles(): void
{
    wp_dequeue_style('classic-theme-styles');
}

// Remove the SVG Filters that are mostly if not only used in Full Site Editing/Gutenberg
// Detailed discussion at: https://github.com/WordPress/gutenberg/issues/36834
function rsa_security_remove_svg_filters(): void
{
    remove_action('wp_body_open', 'gutenberg_global_styles_render_svg_filters');
    remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
}

// Remove ?ver= query from styles and scripts.
function rsa_security_remove_script_version(string $url): string
{
    if (is_admin()) {
        return $url;
    }

    if ($url) {
        return esc_url(remove_query_arg('ver', $url));
    }

    return $url;
}

// Disable attachment template loading and redirect to 404.
// WordPress 6.4 introduced an update to disable attachment pages, but this
// implementation is not as robust as the current one.
// https://github.com/joppuyo/disable-media-pages/issues/41
// https://make.wordpress.org/core/2023/10/16/changes-to-attachment-pages/
function rsa_security_attachment_redirect_not_found(): void
{
    if (is_attachment()) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
    }
}

// Remove contributor, subscriber roles.
function rsa_security_remove_roles(): void
{
    remove_role('contributor');
    remove_role('subscriber');
}

// Disable attachment canonical redirect links.
function rsa_security_disable_attachment_canonical_redirect_url(string $url): string
{
    attachment_redirect_not_found();

    return $url;
}

// Disable attachment links.
function rsa_security_disable_attachment_link(string $url, int $id): string
{
    if ($attachment_url = wp_get_attachment_url($id)) {
        return $attachment_url;
    }

    return $url;
}

// Remove JPEG compression.
function rsa_security_remove_jpeg_compression(): int
{
    return 100;
}

// Disable feeds.
add_action('do_feed', $prefix . 'disable_feeds', 1);
add_action('do_feed_rdf', $prefix . 'disable_feeds', 1);
add_action('do_feed_rss', $prefix . 'disable_feeds', 1);
add_action('do_feed_rss2', $prefix . 'disable_feeds', 1);
add_action('do_feed_atom', $prefix . 'disable_feeds', 1);

// Disable comments feeds.
add_action('do_feed_rss2_comments', $prefix . 'disable_feeds', 1);
add_action('do_feed_atom_comments', $prefix . 'disable_feeds', 1);

// Disable comments.
add_filter('comments_open', '__return_false');

// Remove language dropdown on login screen.
add_filter('login_display_language_dropdown', '__return_false');

// Disable XML RPC for security.
add_filter('xmlrpc_enabled', '__return_false');
add_filter('xmlrpc_methods', '__return_false');

// Remove WordPress version.
remove_action('wp_head', 'wp_generator');

// Remove generated icons.
remove_action('wp_head', 'wp_site_icon', 99);

// Remove shortlink tag from <head>.
remove_action('wp_head', 'wp_shortlink_wp_head', 10);

// Remove shortlink tag from HTML headers.
remove_action('template_redirect', 'wp_shortlink_header', 11);

// Remove Really Simple Discovery link.
remove_action('wp_head', 'rsd_link');

// Remove RSS feed links.
remove_action('wp_head', 'feed_links', 2);

// Remove all extra RSS feed links.
remove_action('wp_head', 'feed_links_extra', 3);

// Remove wlwmanifest.xml.
remove_action('wp_head', 'wlwmanifest_link');

// Remove meta rel=dns-prefetch href=//s.w.org
remove_action('wp_head', 'wp_resource_hints', 2);

// Remove relational links for the posts.
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);

// Remove REST API link tag from <head>.
remove_action('wp_head', 'rest_output_link_wp_head', 10);

// Remove REST API link tag from HTML headers.
remove_action('template_redirect', 'rest_output_link_header', 11);

// Remove emojis.
// WordPress 6.4 deprecated the use of print_emoji_styles() function, but it has
// been retained for backward compatibility purposes.
// https://make.wordpress.org/core/2023/10/17/replacing-hard-coded-style-tags-with-wp_add_inline_style/
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

// Remove oEmbeds.
remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
remove_action('wp_head', 'wp_oembed_add_host_js');

// Disable REST API User endpoints
add_filter('rest_endpoints', $prefix . 'disable_rest_endpoints');

// Disable JPEG Quality strongs (gives away WordPress)
add_filter('jpeg_quality', $prefix . 'remove_jpeg_compression', 10, 2);

// Change Login title and url to be site specific (no wordpress.org links)
add_filter('login_headerurl', $prefix . 'login_url');
add_filter('login_headertext', $prefix . 'login_title');

// Remove WP Theme cruft
add_action('wp_enqueue_scripts', $prefix . 'remove_block_styles');
add_action('wp_footer', $prefix . 'remove_core_block_styles');
add_action('wp_enqueue_scripts', $prefix . 'remove_global_styles');
add_action('wp_enqueue_scripts', $prefix . 'remove_classic_theme_styles');
add_action('init', $prefix . 'remove_svg_filters');

// Remove Script Versions
add_filter('script_loader_src', $prefix . 'remove_script_version', 15, 1);
add_filter('style_loader_src', $prefix . 'remove_script_version', 15, 1);

// Remove roles
add_action('init', $prefix . 'remove_roles');

// Attachement URLs and Redirects
add_filter('template_redirect', $prefix . 'attachment_redirect_not_found');
add_filter('redirect_canonical', $prefix . 'disable_attachment_canonical_redirect_url', 0, 1);
add_filter('attachment_link', $prefix . 'disable_attachment_link', 10, 2);

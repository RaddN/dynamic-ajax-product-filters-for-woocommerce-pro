<?php

if (!defined('ABSPATH')) {
    exit;
}

function dapfforwcpro_register_template()
{
    global $wp, $dapfforwc_seo_permalinks_options;
    $request = $wp->request;


    if (strpos($request, 'filters') === 0) {
        // Handle requests starting with "filters"
        if (isset($dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"]) && $dapfforwc_seo_permalinks_options["use_attribute_type_in_permalinks"] === "on") {
            $dapfforwcpro_slug = sanitize_text_field(str_replace('/', '&', substr($request, strlen("filters") + 1)));
            set_transient('dapfforwcpro_slug', $dapfforwcpro_slug, 30);
            wp_redirect(home_url("/?filters=1&$dapfforwcpro_slug"), 301);
        } else {
            $dapfforwcpro_slug = sanitize_text_field(str_replace('/', ',', substr($request, strlen("filters") + 1)));
            set_transient('dapfforwcpro_slug', $dapfforwcpro_slug, 30);
            wp_redirect(home_url("/?filters=$dapfforwcpro_slug"), 301);
        }

        exit;
    } elseif (strpos($request, 'filters/') !== false) {
        // Handle requests containing "filters"
        $dapfforwcpro_root_slug = sanitize_text_field(substr($request, 0, strpos($request, 'filters') - 1));
        $dapfforwcpro_slug = sanitize_text_field(substr($request, strpos($request, 'filters') + strlen("filters") + 1));
        set_transient('dapfforwcpro_root_slug', $dapfforwcpro_root_slug, 30);
        set_transient('dapfforwcpro_slug', $dapfforwcpro_slug, 30);
        wp_redirect(home_url("/$dapfforwcpro_root_slug?filters=$dapfforwcpro_slug"), 301);
        exit;
    }
}
function dapfforwcpro_remove_session()
{
    // Remove the slug from the session
    delete_transient('dapfforwcpro_slug');
}

// Hook the functions to appropriate actions
add_action('template_redirect', 'dapfforwcpro_register_template');
add_action('wp_footer', 'dapfforwcpro_remove_session');

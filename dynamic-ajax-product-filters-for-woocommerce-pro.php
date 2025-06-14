<?php

/**
 * Plugin Name: Dynamic AJAX Product Filters for WooCommerce Pro
 * Plugin URI:  https://plugincy.com/
 * Description: A WooCommerce plugin to filter products by attributes, categories, and tags using AJAX for seamless user experience.
 * Version:     1.1.7
 * Author:      Plugincy
 * Author URI:  https://plugincy.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dynamic-ajax-product-filters-for-woocommerce-pro
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DAPFFORWCPRO_VERSION', '1.1.7');

// Check if the free version is installed and deactivate it if active
add_action('plugins_loaded', function () {
    if (is_plugin_active('dynamic-ajax-product-filters-for-woocommerce/dynamic-ajax-product-filters-for-woocommerce.php')) {
        deactivate_plugins('dynamic-ajax-product-filters-for-woocommerce/dynamic-ajax-product-filters-for-woocommerce.php');
        add_action('admin_notices', function () {
            echo '<div class="notice notice-warning is-dismissible"><p>';
            esc_html_e('Dynamic AJAX Product Filters for WooCommerce (free version) has been deactivated. Please use only the Pro version.', 'dynamic-ajax-product-filters-for-woocommerce-pro');
            echo '</p></div>';
        });
    }
}, 1);

// Load text domain for translations
add_action('plugins_loaded', 'dapfforwcpro_load_textdomain');
function dapfforwcpro_load_textdomain()
{
    load_plugin_textdomain('dynamic-ajax-product-filters-for-woocommerce-pro', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Global Variables
global $allowed_tags, $dapfforwc_options, $dapfforwc_seo_permalinks_options, $dapfforwcpro_advance_settings, $dapfforwcpro_styleoptions, $dapfforwcpro_use_url_filter, $dapfforwcpro_auto_detect_pages_filters, $dapfforwcpro_slug, $dapfforwcpro_sub_options, $dapfforwcpro_front_page_slug;


$dapfforwc_options = get_option('dapfforwc_options') ?: [];
$dapfforwcpro_advance_settings = get_option('dapfforwc_advance_options') ?: [];
$dapfforwc_seo_permalinks_options = get_option('dapfforwc_seo_permalinks_options') ?: [];
$dapfforwcpro_styleoptions = get_option('dapfforwc_style_options') ?: [];

$dapfforwcpro_use_url_filter = isset($dapfforwc_options['use_url_filter']) ? $dapfforwc_options['use_url_filter'] : false;
$dapfforwcpro_auto_detect_pages_filters = isset($dapfforwc_options['pages_filter_auto']) ? $dapfforwc_options['pages_filter_auto'] : '';
$dapfforwcpro_slug = "";

// Get the ID of the front page

$dapfforwcpro_front_page_id = get_option('page_on_front') ?: null;

// Get the front page object
$dapfforwcpro_front_page = isset($dapfforwcpro_front_page_id) ? get_post($dapfforwcpro_front_page_id) : null;
// Get the slug of the front page
$dapfforwcpro_front_page_slug = isset($dapfforwcpro_front_page) ? $dapfforwcpro_front_page->post_name : "";


$allowed_tags = array(
    'a' => array(
        'href' => array(),
        'title' => array(),
        'class' => array(),
        'target' => array(), // Allow target attribute for links
    ),
    'strong' => array(),
    'em' => array(),
    'li' => array(
        'class' => array(),
    ),
    'div' => array(
        'class' => array(),
        'id' => array(), // Allow id for divs
    ),
    'img' => array(
        'src' => array(),
        'alt' => array(),
        'class' => array(),
        'width' => array(), // Allow width attribute
        'height' => array(), // Allow height attribute
    ),
    'h1' => array('class' => array()), // Allow h1
    'h2' => array('class' => array()),
    'h3' => array('class' => array()), // Allow h3
    'h4' => array('class' => array()), // Allow h4
    'h5' => array('class' => array()), // Allow h5
    'h6' => array('class' => array()), // Allow h6
    'span' => array('class' => array()),
    'p' => array('class' => array()),
    'br' => array(), // Allow line breaks
    'blockquote' => array(
        'cite' => array(), // Allow cite attribute for blockquotes
        'class' => array(),
    ),
    'table' => array(
        'class' => array(),
        'style' => array(), // Allow inline styles
    ),
    'tr' => array(
        'class' => array(),
    ),
    'td' => array(
        'class' => array(),
        'colspan' => array(), // Allow colspan attribute
        'rowspan' => array(), // Allow rowspan attribute
    ),
    'th' => array(
        'class' => array(),
        'colspan' => array(),
        'rowspan' => array(),
    ),
    'ul' => array('class' => array()), // Allow unordered lists
    'ol' => array('class' => array()), // Allow ordered lists
    'script' => array(), // Be cautious with scripts
);


// Define sub-options
$dapfforwcpro_sub_options = [
    'checkbox' => [
        'checkbox' => 'Checkbox',
        'button_check' => 'Button Checkbox',
        'radio_check' => 'Radio Check',
        'radio' => 'Radio',
        // 'square_check' => 'Square Check',
        'square' => 'Square',
        'checkbox_hide' => 'Checkbox Hide',
    ],
    'color' => [
        'color' => 'Color',
        'color_no_border' => 'Color Without Border',
        'color_circle' => 'Color Circle',
        'color_value' => 'Color With Value',
    ],
    'image' => [
        'image' => 'Image',
        'image_no_border' => 'Image Without Border',
    ],
    'dropdown' => [
        'select' => 'Select',
        'select2' => 'Select 2',
        // 'select2_classic' => 'Select 2 Classic',
    ],
    'price' => [
        'price' => 'Price',
        'slider' => 'Slider',
        'input-price-range' => 'input price range',
    ],
    'rating' => [
        'rating' => 'Rating Star',
        'rating-text' => 'Rating Text',
        'dynamic-rating' => 'Dynamic Rating',
    ],
];



// Check if WooCommerce is active
add_action('plugins_loaded', 'dapfforwcpro_check_woocommerce');

function dapfforwcpro_check_woocommerce()
{
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'dapfforwcpro_missing_woocommerce_notice');
    } else {
        if (is_admin()) {
            require_once plugin_dir_path(__FILE__) . 'admin/admin-notice.php';
            require_once(plugin_dir_path(__FILE__) . 'includes/get_review.php');
            require_once plugin_dir_path(__FILE__) . 'admin/admin-page.php';
        }
        require_once plugin_dir_path(__FILE__) . 'includes/filter-template.php';
        require_once plugin_dir_path(__FILE__) . 'admin/license-page.php';

        add_action('wp_enqueue_scripts', 'dapfforwcpro_enqueue_scripts');
        add_action('admin_enqueue_scripts', 'dapfforwcpro_admin_scripts');
        require_once plugin_dir_path(__FILE__) . 'includes/class-filter-functions.php';

        // add_action('wp_ajax_dapfforwcpro_filter_products', 'dapfforwcpro_filter_products');
        // add_action('wp_ajax_nopriv_dapfforwcpro_filter_products', 'dapfforwcpro_filter_products');

        register_setting('dapfforwc_options_group', 'dapfforwcpro_filters', 'sanitize_text_field');

        add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'dapfforwcpro_add_settings_link');
        require_once plugin_dir_path(__FILE__) . 'includes/common-functions.php';

        // filter error detector
        add_action('admin_bar_menu', 'dapfforwcpro_add_debug_menu', 100);
        add_action('init', 'dapfforwcpro_dapfforwcpro_filter_init');
        add_action('template_redirect', 'dapfforwcpro_template_redirect_filter');

        // Hook into wp_head with a high priority to ensure our tags are output correctly
        add_action('wp_head', 'dapfforwcpro_set_seo_meta_tags', 0);

        require_once(plugin_dir_path(__FILE__) . 'includes/permalinks-setup.php');
    }
}

function dapfforwcpro_missing_woocommerce_notice()
{
    echo '<div class="notice notice-error"><p><strong>' . esc_html__('Filter Plugin', 'dynamic-ajax-product-filters-for-woocommerce-pro') . '</strong> ' . esc_html__('requires WooCommerce to be installed and activated.', 'dynamic-ajax-product-filters-for-woocommerce-pro') . '</p></div>';
}

// Enqueue scripts and styles
function dapfforwcpro_enqueue_scripts()
{
    global $dapfforwcpro_use_url_filter, $dapfforwc_options, $dapfforwc_seo_permalinks_options, $dapfforwcpro_slug, $dapfforwcpro_styleoptions, $dapfforwcpro_advance_settings, $dapfforwcpro_front_page_slug;

    $script_handle = 'urlfilter-ajax';
    $script_path = 'assets/js/filter.min.js';

    wp_enqueue_script('jquery');
    wp_enqueue_script($script_handle, plugin_dir_url(__FILE__) . $script_path, ['jquery'], '1.1.7', true);
    wp_script_add_data($script_handle, 'async', true); // Load script asynchronously
    wp_localize_script($script_handle, 'dapfforwcpro_data', compact('dapfforwc_options', 'dapfforwc_seo_permalinks_options', 'dapfforwcpro_slug', 'dapfforwcpro_styleoptions', 'dapfforwcpro_advance_settings', 'dapfforwcpro_front_page_slug'));
    wp_localize_script($script_handle, 'dapfforwcpro_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'shopPageUrl' => esc_url(get_permalink(wc_get_page_id('shop'))),
        'isProductArchive' =>  is_shop() || is_product_category() || is_product_tag() || is_product(),
        'currencySymbol' => get_woocommerce_currency_symbol(),
        'isHomePage' => is_front_page()
    ]);

    wp_enqueue_style('filter-style', plugin_dir_url(__FILE__) . 'assets/css/style.min.css', [], '1.1.7');
    wp_enqueue_style('select2-css', plugin_dir_url(__FILE__) . 'assets/css/select2.min.css', [], '1.1.7');
    wp_enqueue_script('select2-js', plugin_dir_url(__FILE__) . 'assets/js/select2.min.js', ['jquery'], '1.1.7', true);
    $css = '';
    // Generate inline css for sidebartop in mobile
    if (isset($dapfforwcpro_advance_settings["sidebar_top"]) && $dapfforwcpro_advance_settings["sidebar_top"] === "on") {
        $css .= "@media (max-width: 768px) {
                    div#content>div {
                        flex-direction: column !important;
                    }
        }";
    }
    // Generate CSS for max-height

    $max_height = (is_array($dapfforwcpro_styleoptions) && isset($dapfforwcpro_styleoptions["max_height"])) ? $dapfforwcpro_styleoptions["max_height"] : [];
    foreach ($max_height as $key => $value) {
        // Sanitize the key to create a valid CSS class name
        if (is_numeric($value) && $value > 0) {
            $cssClass = strtolower($key); // Replace dashes with underscores
            $css .= "#{$cssClass} .items{\n";
            $css .= "    max-height: {$value}px;\n"; // Set max-height based on value
            $css .= "    overflow-y: scroll;\n";
            $css .= "    transition: max-height 0.3s ease;\n";
            $css .= "}\n";
        }
    }
    // Add the generated CSS as inline style
    wp_add_inline_style('filter-style', $css);
    wp_add_inline_script('select2-js', '
        jQuery(document).ready(function($) {
            
             function initializeSelect2() {
                $(".select2").select2({
                    placeholder: "Select Options",
                    allowClear: true
                });
                $("select.select2_classic").select2({
                    placeholder: "Select Options",
                    allowClear: true
                });
            }

            // Initial initialization
            initializeSelect2();

            $(document).ajaxComplete(function () {
                // Check if new options are added before reinitializing
                if ($(".select2").find("option").length > 0) {
                    initializeSelect2();
                }
            });

            if ($(window).width() > 768) {
    function initializeCollapsible() {
        $(".title").each(function () {
            const $this = $(this);
            const $items = $this.parent().children().not(".title");

            // Hide items initially if the title has a specific class
            if ($this.hasClass("plugincy_collapsable_minimize_initial")) {
                $items.addClass("dapfforwcpro-hidden-important");
                $items.hide();
            }

            // Clear any existing event handlers before adding new ones
            $this.off("click").on("click", function () {
            if ($this.hasClass("plugincy_collapsable_disabled")) {
                return; // Do nothing if the title has the disabled class
            }
                // Handle `.plugincy_collapsable_arrow` class for rotating the SVG icon
                    $this.find("svg").toggleClass("rotated");
                // Toggle the visibility of the sibling `.items`
                //$items.slideToggle(300);
                  $items.toggleClass("dapfforwcpro-hidden-important", 300);
            });
        });
    }

    // Initialize collapsible elements
    initializeCollapsible();

    // Reinitialize collapsibles after AJAX content is loaded
    $(document).ajaxComplete(function () {
        initializeCollapsible();
    });
}


        });
    ');
}

function dapfforwcpro_admin_scripts($hook)
{
    if ($hook !== 'toplevel_page_dapfforwcpro-admin') {
        return; // Load only on the plugin's admin page
    }
    global $dapfforwcpro_sub_options;
    wp_enqueue_style('dapfforwcpro-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin-style.min.css', [], '1.1.7');
    wp_enqueue_code_editor(array('type' => 'text/html'));
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');
    wp_enqueue_script('dapfforwcpro-admin-script', plugin_dir_url(__FILE__) . 'assets/js/admin-script.min.js', [], '1.1.7', true);
    wp_enqueue_media();
    wp_enqueue_script('dapfforwcpro-media-uploader', plugin_dir_url(__FILE__) . 'assets/js/media-uploader.min.js', ['jquery'], '1.0.0', true);

    $inline_script = 'document.addEventListener("DOMContentLoaded", function () {
    const dropdown = document.getElementById("attribute-dropdown");

    const savedAttribute = localStorage.getItem("dapfforwc_selected_attribute");
    if (savedAttribute) {
        try {
            const parsed = JSON.parse(savedAttribute);
            if (parsed && parsed.attribute && dropdown) {
                dropdown.value = parsed.attribute;

                selectedAttribute = parsed.attribute;

                toggleDisplay(".style-options", "none");

                if (selectedAttribute) {
                    const selectedOptions = document.getElementById(`options-${selectedAttribute}`);
                    if (selectedOptions) {
                        selectedOptions.style.display = "block";
                    }
                }

                if (selectedAttribute === "price") {
                    toggleDisplay(".primary_options label", "none");
                    toggleDisplay(".primary_options label.price", "block");
                    toggleDisplay(".min-max-price-set", "block");
                    toggleDisplay(".setting-item.single-selection", "block");
                    toggleDisplay(".setting-item.show-product-count", "block");
                }
                else if (selectedAttribute === "rating") {
                    toggleDisplay(".min-max-price-set", "none");
                    toggleDisplay(".primary_options label", "none");
                    toggleDisplay(".primary_options label.rating", "block");
                    toggleDisplay(".setting-item.single-selection", "none");
                    toggleDisplay(".setting-item.show-product-count", "none");
                } else if(selectedAttribute === "product-category"){
                    toggleDisplay(".hierarchical", "block");
                    toggleDisplay(".min-max-price-set", "none");
                    toggleDisplay(".primary_options label", "block");
                    toggleDisplay(".primary_options label.price", "none");
                    toggleDisplay(".primary_options label.rating", "none");
                    toggleDisplay(".setting-item.show-product-count", "block");
                    toggleDisplay(".primary_options label.color", "none");
                    toggleDisplay(".primary_options label.image", "none");
                }else if(selectedAttribute === "tag"){
                    toggleDisplay(".hierarchical", "none");
                    toggleDisplay(".min-max-price-set", "none");
                    toggleDisplay(".primary_options label", "block");
                    toggleDisplay(".primary_options label.price", "none");
                    toggleDisplay(".primary_options label.rating", "none");
                    toggleDisplay(".setting-item.show-product-count", "block");
                    toggleDisplay(".primary_options label.color", "none");
                    toggleDisplay(".primary_options label.image", "none");
                }
                else {
                    toggleDisplay(".min-max-price-set", "none");
                    toggleDisplay(".hierarchical", "none");
                    toggleDisplay(".primary_options label", "block");
                    toggleDisplay(".primary_options label.price", "none");
                    toggleDisplay(".primary_options label.rating", "none");
                    toggleDisplay(".setting-item.single-selection", "block");
                    toggleDisplay(".setting-item.show-product-count", "block");
                }
                
            }
        } catch (e) {}
    }

    if(dropdown){
        const firstAttribute = dropdown.value;
        const firstOptions = document.querySelector(`#options-${firstAttribute}`);
        if (firstOptions) {
            firstOptions.style.display = "block";
        }
    }

    function toggleDisplay(selector, display) {
        document.querySelectorAll(selector).forEach(el => {
            el.style.display = display;
        });
    }

    if(dropdown)dropdown.addEventListener("change", function () {
    const selectedAttribute = this.value;
    
    localStorage.setItem("dapfforwc_selected_attribute", JSON.stringify({ "attribute": selectedAttribute }));

    toggleDisplay(".style-options", "none");

    if (selectedAttribute) {
        const selectedOptions = document.getElementById(`options-${selectedAttribute}`);
        if (selectedOptions) {
            selectedOptions.style.display = "block";
        }
    }

    if (selectedAttribute === "price") {
        toggleDisplay(".primary_options label", "none");
        toggleDisplay(".hierarchical", "none");
        toggleDisplay(".primary_options label.price", "block");
        toggleDisplay(".primary_options label.rating", "none");
        toggleDisplay(".min-max-price-set", "block");
        toggleDisplay(".setting-item.single-selection", "none");
        toggleDisplay(".setting-item.show-product-count", "none");
    }
    else if (selectedAttribute === "rating") {
        toggleDisplay(".min-max-price-set", "none");
        toggleDisplay(".hierarchical", "none");
        toggleDisplay(".primary_options label", "none");
        toggleDisplay(".primary_options label.price", "none");
        toggleDisplay(".primary_options label.rating", "block");
        toggleDisplay(".setting-item.single-selection", "none");
        toggleDisplay(".setting-item.show-product-count", "none");
    } else if(selectedAttribute === "product-category"){
        toggleDisplay(".hierarchical", "block");
        toggleDisplay(".min-max-price-set", "none");
        toggleDisplay(".primary_options label", "block");
        toggleDisplay(".primary_options label.price", "none");
        toggleDisplay(".primary_options label.rating", "none");
        toggleDisplay(".setting-item.show-product-count", "block");
        toggleDisplay(".primary_options label.color", "none");
        toggleDisplay(".primary_options label.image", "none");
    }else if(selectedAttribute === "tag"){
        toggleDisplay(".hierarchical", "none");
        toggleDisplay(".min-max-price-set", "none");
        toggleDisplay(".primary_options label", "block");
        toggleDisplay(".primary_options label.price", "none");
        toggleDisplay(".primary_options label.rating", "none");
        toggleDisplay(".setting-item.show-product-count", "block");
        toggleDisplay(".primary_options label.color", "none");
        toggleDisplay(".primary_options label.image", "none");
    }
    else {
        toggleDisplay(".min-max-price-set", "none");
        toggleDisplay(".hierarchical", "none");
        toggleDisplay(".primary_options label", "block");
        toggleDisplay(".primary_options label.price", "none");
        toggleDisplay(".primary_options label.rating", "none");
        toggleDisplay(".setting-item.single-selection", "block");
        toggleDisplay(".setting-item.show-product-count", "block");
    }
});

    document.querySelectorAll(`.style-options .primary_options input[type="radio"][name^="dapfforwc_style_options"]`).forEach(function (radio) {
        radio.addEventListener("change", function () {
            const selectedType = this.value;
            const attributeName = this.name.match(/\[(.*?)\]/)[1];
            const subOptionsContainer = document.querySelector(`#options-${attributeName} .dynamic-sub-options`);
   
            document.querySelectorAll(".primary_options label").forEach(label => {
                label.classList.remove("active");
                const checkIcon = label.querySelector(".active");
                if (checkIcon) {
                    checkIcon.style.display = "none"; 
                }
            });
            const selectedLabel = radio.closest("label");
            selectedLabel.classList.add("active");

            const subOptions = ' . (isset($dapfforwcpro_sub_options) && is_array($dapfforwcpro_sub_options) ? wp_json_encode($dapfforwcpro_sub_options) : '[]') . '

            const currentOptions = subOptions[selectedType] || {};
            subOptionsContainer.innerHTML = "";

            const fragment = document.createDocumentFragment();
            for (const key in currentOptions) {
                const label = document.createElement("label");
                label.innerHTML = `
                    <span class="active" style="display:none;"><i class="fa fa-check"></i></span>
                    <input type="radio" class="optionselect" name="dapfforwc_style_options[${attributeName}][sub_option]" value="${key}">                    
                    <img src="' . plugin_dir_url(__FILE__) . 'assets/images/${key}.png" alt="${currentOptions[key]}">
                   
                `;
                fragment.appendChild(label);
            }
            subOptionsContainer.appendChild(fragment);

            attachSubOptionListeners();

           if(selectedType==="color" || selectedType==="image") {
            document.querySelector(`.advanced-options.${attributeName}`).style.display = "block";
            document.querySelector(`.advanced-options.${attributeName} .color`).style.display = "none";
            document.querySelector(`.advanced-options.${attributeName} .image`).style.display = "none";
            document.querySelector(`.advanced-options.${attributeName} .${selectedType}`).style.display = "block";
            document.querySelector(`.setting-item.single-selection`).style.display = "block";

           }else if(selectedType==="dropdown") {
            document.querySelector(`.setting-item.single-selection`).style.display = "none";
           } else {
            document.querySelector(`.setting-item.single-selection`).style.display = "block";
            document.querySelectorAll(".advanced-options").forEach(advanceoptions =>{
                advanceoptions.style.display = "none";
            })
           }
        });
    });

    function attachSubOptionListeners() {
    const radioButtons = document.querySelectorAll(".optionselect");
    
    
    radioButtons.forEach(radio => {
        radio.addEventListener("change", function() {
            document.querySelectorAll(".dynamic-sub-options label").forEach(label => {
                label.classList.remove("active");
                const checkIcon = label.querySelector(".active");
                if (checkIcon) {
                    checkIcon.style.display = "none";
                }
            });

            const selectedLabel = this.closest("label");
            selectedLabel.classList.add("active");
            const checkIcon = selectedLabel.querySelector(".active");
            if (checkIcon) {
                checkIcon.style.display = "inline"; // Show check icon
            }

            // Managing single selection checkbox
            const singleSelectionCheckbox = this.closest(".style-options").querySelector(".setting-item.single-selection input");
            const singleSelectiondiv = this.closest(".style-options").querySelector(".setting-item.single-selection"); 
            if (this.value === "select") {
                singleSelectionCheckbox.checked = true;
                singleSelectiondiv.style.display = "none"; // Show the checkbox
                
            } else {
                singleSelectionCheckbox.checked = false; // Uncheck if other options are selected
                singleSelectiondiv.style.display = "block"; // Hide the checkbox
            }
        });
    });
}

// Call the function to attach listeners
attachSubOptionListeners();

});';
    wp_add_inline_script('dapfforwcpro-admin-script', $inline_script);
}

// function dapfforwcpro_filter_products()
// {
//     if (class_exists('dapfforwcpro_Filter_Functions')) {
//         $filter = new dapfforwcpro_Filter_Functions();
//         $filter->process_filter();
//     } else {
//         wp_send_json_error('Filter class not found.');
//     }
// }


function dapfforwcpro_add_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=dapfforwcpro-admin">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

function dapfforwcpro_get_full_slug($post_id)
{
    if (empty($post_id)) {
        return ''; // Return an empty string if $post_id is not defined
    }
    $dapfforwcpro_slug_parts = [];
    $current_post_id = $post_id;

    while ($current_post_id) {
        $current_post = get_post($current_post_id);

        if (!$current_post) {
            break; // Exit if no post is found
        }

        // Prepend the current slug
        array_unshift($dapfforwcpro_slug_parts, $current_post->post_name);

        // Get the parent post ID
        $current_post_id = wp_get_post_parent_id($current_post_id);
    }

    return implode('/', $dapfforwcpro_slug_parts); // Combine slugs with '/'
}


require_once(plugin_dir_path(__FILE__) . 'includes/widget_design_template.php');
require_once(plugin_dir_path(__FILE__) . 'includes/blocks_widget_create.php');

// block editor script
function dapfforwcpro_enqueue_dynamic_ajax_filter_block_assets()
{
    wp_enqueue_script(
        'dynamic-ajax-filter-block',
        plugins_url('includes/block.min.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor'),
        filemtime(plugin_dir_path(__FILE__) . 'includes/block.min.js'),
        true
    );

    wp_enqueue_style('custom-box-control-styles', plugin_dir_url(__FILE__) . 'assets/css/block-editor.min.css', [], '1.1.7');
}
add_action('enqueue_block_editor_assets', 'dapfforwcpro_enqueue_dynamic_ajax_filter_block_assets');






function dapfforwcpro_add_debug_menu($wp_admin_bar)
{
    if (current_user_can('administrator')) {
        $args = [
            'id'    => 'dapfforwcpro_debug',
            'title' => '<span class="ab-icon dashicons dashicons-filter"></span><span id="dapfforwcpro_issue_count"></span> ' . __('Product Filter', 'dynamic-ajax-product-filters-for-woocommerce-pro'),
            'meta'  => [
                'class' => 'dapfforwcpro-debug-bar',
            ],
        ];
        $wp_admin_bar->add_node($args);

        $wp_admin_bar->add_node([
            'id'     => 'dapfforwcpro_debug_sub',
            'parent' => 'dapfforwcpro_debug',
            'title'  => '<span id="dapfforwcpro_debug_message">' . __('Checking...', 'dynamic-ajax-product-filters-for-woocommerce-pro') . '</span>',
            'meta'   => [
                'class' => 'ab-sub-wrapper',
            ],
        ]);
    }
}

add_action('wp_footer', 'dapfforwcpro_check_elements');

function dapfforwcpro_check_elements()
{
    global $dapfforwcpro_advance_settings;
    if (current_user_can('administrator')) {
?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                var debugMessage = document.getElementById('dapfforwcpro_debug_message');
                var issueCount = document.getElementById('dapfforwcpro_issue_count');
                if (!document.querySelector('#product-filter')) {
                    debugMessage.innerHTML = '<span style="color: red;">&#10007;</span> <?php echo esc_html__('Filter is not added', 'dynamic-ajax-product-filters-for-woocommerce-pro'); ?>';
                    issueCount.innerHTML = '1';
                    issueCount.style.display = 'block';
                } else if (!document.querySelector('<?php echo esc_js(isset($dapfforwcpro_advance_settings["product_selector"]) && !empty($dapfforwcpro_advance_settings["product_selector"]) ? $dapfforwcpro_advance_settings["product_selector"] : ''); ?>')) {
                    debugMessage.innerHTML = '<span style="color: red;">&#10007;</span> <?php echo esc_html__('Products are not found. Add product or', 'dynamic-ajax-product-filters-for-woocommerce-pro'); ?> <a href="#" style="display: inline; padding: 0;"><?php echo esc_html__('change selector', 'dynamic-ajax-product-filters-for-woocommerce-pro'); ?></a>';
                    issueCount.innerHTML = '1';
                    issueCount.style.display = 'block';
                } else if (!document.querySelector('<?php echo esc_js(isset($dapfforwcpro_advance_settings["pagination_selector"]) && !empty($dapfforwcpro_advance_settings["pagination_selector"]) ? $dapfforwcpro_advance_settings["pagination_selector"] : ''); ?>')) {
                    debugMessage.innerHTML = '<span style="color: red;">&#10007;</span> <?php echo esc_html__('Pagination is not found', 'dynamic-ajax-product-filters-for-woocommerce-pro'); ?> <a href="#" style="display: inline; padding: 0;"><?php echo esc_html__('change selector', 'dynamic-ajax-product-filters-for-woocommerce-pro'); ?></a>';
                    issueCount.innerHTML = '1';
                    issueCount.style.display = 'block';
                } else {
                    debugMessage.innerHTML = '<span style="color: green;">&#10003;</span> <?php echo esc_html__('Filter working fine', 'dynamic-ajax-product-filters-for-woocommerce-pro'); ?>';

                }
            });
        </script>
        <style>
            ul#wp-admin-bar-dapfforwcpro_debug-default {
                padding: 0 !important;
                margin: 0 !important;
            }

            li#wp-admin-bar-dapfforwcpro_debug_sub {
                display: block !important;
                padding: 10px 5px !important;
                height: max-content;
            }
        </style>
<?php
    }
}



function dapfforwcpro_register_api_routes()
{
    register_rest_route('dynamic-ajax-product-filters-for-woocommerce-pro/v1', '/attributes/', array(
        'methods' => 'GET',
        'callback' => 'dapfforwcpro_get_product_attributes',
        'permission_callback' => '__return_true', // Adjust permissions as needed
    ));
}
add_action('rest_api_init', 'dapfforwcpro_register_api_routes');

function dapfforwcpro_get_product_attributes()
{
    // Fetch WooCommerce attribute taxonomies
    $attributes = wc_get_attribute_taxonomies();
    $result = [];

    foreach ($attributes as $attribute) {
        $result[] = [
            'id' => $attribute->attribute_id,
            'name' => $attribute->attribute_label,
            'slug' => $attribute->attribute_name,
        ];
    }

    if (empty($result)) {
        return new WP_Error('no_attributes', __('No product attributes found', 'dynamic-ajax-product-filters-for-woocommerce-pro'), array('status' => 404));
    }

    return rest_ensure_response($result);
}

/** * Set custom SEO meta tags based on URL parameters */
function dapfforwcpro_set_seo_meta_tags()
{
    global $dapfforwc_seo_permalinks_options;

    // Only proceed if SEO is enabled
    if (!isset($dapfforwc_seo_permalinks_options['enable_seo']) || $dapfforwc_seo_permalinks_options['enable_seo'] !== 'on') {
        return;
    }

    // Get sanitized URL parameters using the secure method
    $host = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '';
    $request_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';

    // Build the sanitized URL
    if (!empty($host) && !empty($request_uri)) {
        $url_page = esc_url("http://{$host}{$request_uri}");
    } else {
        $url_page = home_url(); // Fallback to homepage if values are missing
    }

    // Parse the URL
    $parsed_url = wp_parse_url($url_page);

    // Parse the query string into an associative array
    $query_params = [];
    if (isset($parsed_url['query'])) {
        parse_str($parsed_url['query'], $query_params);
    }

    // Check if the URL has a fragment and join it with query_params
    if (isset($parsed_url['fragment']) && !empty($parsed_url['fragment'])) {
        $parsed_fragment = str_replace(['#038;', '038;'], '', $parsed_url['fragment']);
        parse_str($parsed_fragment, $fragment_params);
        $query_params = array_merge($query_params, $fragment_params);
    }

    // Check if we have filter parameters in the URL
    $has_filters = false;

    // Check format 1: filters=1&param=value
    if (isset($query_params['filters']) && $query_params['filters'] == '1') {
        $has_filters = true;
    }
    // Check format 2: filters=value1,value2
    elseif (isset($query_params['filters']) && !empty($query_params['filters']) && $query_params['filters'] != '1') {
        $has_filters = true;
    }

    if (!$has_filters) {
        return;
    }

    // Get base SEO settings
    $seo_title = $dapfforwc_seo_permalinks_options['seo_title'] ?? '{site_title} {page_title} {attribute_prefix} {value}';
    $seo_description = $dapfforwc_seo_permalinks_options['seo_description'] ?? '{site_title} {page_title} {attribute_prefix} {value}';
    $seo_keywords = $dapfforwc_seo_permalinks_options['seo_keywords'] ?? '{site_title} {page_title} {attribute_prefix} {value}';

    // Get site and page title
    $site_title = get_bloginfo('name');
    $page_title = '';

    // Get current category title if available
    if (is_product_category()) {
        $term = get_queried_object();
        if ($term) {
            $page_title = $term->name;
        }
    } else if (is_product_tag()) {
        $term = get_queried_object();
        if ($term) {
            $page_title = $term->name;
        }
    } else {
        $page_title = get_the_title();
    }

    $seo_title = str_replace(array_keys(dapfforwcpro_replacement($seo_title, $query_params, $site_title, $page_title)), array_values(dapfforwcpro_replacement($seo_title, $query_params, $site_title, $page_title)), $seo_title);
    $seo_description = str_replace(array_keys(dapfforwcpro_replacement($seo_description, $query_params, $site_title, $page_title)), array_values(dapfforwcpro_replacement($seo_description, $query_params, $site_title, $page_title)), $seo_description);
    $seo_keywords = str_replace(array_keys(dapfforwcpro_replacement($seo_keywords, $query_params, $site_title, $page_title)), array_values(dapfforwcpro_replacement($seo_keywords, $query_params, $site_title, $page_title)), $seo_keywords);

    // Clean up any extra spaces
    $seo_title = preg_replace('/\s+/', ' ', trim($seo_title));
    $seo_description = preg_replace('/\s+/', ' ', trim($seo_description));
    $seo_keywords = preg_replace('/\s+/', ' ', trim($seo_keywords));

    // Get canonical URL
    $canonical_url = home_url(add_query_arg([], $GLOBALS['wp']->request));

    // Output the meta tags
    echo '<meta name="title" content="' . esc_attr($seo_title) . '">' . "\n";
    echo '<title>' . esc_html($seo_title) . '</title>' . "\n";
    echo '<meta name="description" content="' . esc_attr($seo_description) . '">' . "\n";
    echo '<meta name="keywords" content="' . esc_attr($seo_keywords) . '">' . "\n";
    echo '<meta name="robots" content="' . esc_attr($dapfforwc_seo_permalinks_options['seo_meta_tag'] ?? 'index, follow') . '">' . "\n";
    echo '<link rel="canonical" href="' . esc_url($canonical_url) . '">' . "\n";

    // Open Graph meta tags
    echo '<meta property="og:title" content="' . esc_attr($seo_title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($seo_description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($canonical_url) . '">' . "\n";

    // Twitter meta tags
    echo '<meta name="twitter:title" content="' . esc_attr($seo_title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($seo_description) . '">' . "\n";
}

function dapfforwcpro_replacement($current_place, $query_params, $site_title, $page_title)
{
    // New approach: Extract attribute-value pairs directly from query_params
    $formatted_pairs = [];

    // Process each query parameter as an attribute-value pair
    foreach ($query_params as $param => $value) {
        // Skip special parameters

        if ($param === 'filters' && $value === '1') {
            continue; // Skip special parameters
        }
        // Process multi-value parameters (comma-separated)
        $values = explode(',', sanitize_text_field($value));
        $formatted_values = [];

        if (strpos($current_place, '{attribute_prefix}') !== false) {

            foreach ($values as $val) {
                $formatted_values[] = str_replace('-', ' ', $val);
            }

            // Format as "attribute seperator between {attribute_prefix} & {value} value1, value2"
            if (!empty($formatted_values)) {
                preg_match('/{attribute_prefix}(.*?)\{value\}/', $current_place, $matches);
                $separator = $matches[1] ?? '-';
                $formatted_pairs[] = $param . "{$separator}" . implode(', ', $formatted_values);
            }
        } elseif (strpos($current_place, '{value}') !== false) {
            // Format as "value1, value2"
            if (!empty($values)) {
                $formatted_pairs[] = implode(', ', $values);
            }
        }
    }

    // Combine all formatted pairs
    $formatted_string = implode(', ', $formatted_pairs);

    // Replace placeholders in SEO settings
    $replacements = [
        '{site_title}' => $site_title,
        '{page_title}' => $page_title,
        '{attribute_prefix}' => '', // No longer needed as we format differently
        '{value}' => $formatted_string // Now contains "attribute - value" format
    ];

    return $replacements;
}


function dapfforwcpro_block_categories($categories, $post)
{
    // Create the new category array
    $new_category = array(
        'slug' => 'plugincy',
        'title' => __('Plugincy', 'one-page-quick-checkout-for-woocommerce'),
        'icon'  => 'plugincy',
    );

    // Add the new category to the beginning of the categories array
    array_unshift($categories, $new_category);

    return $categories;
}
add_filter('block_categories_all', 'dapfforwcpro_block_categories', 0, 2);


function dapfforwcpro_editor_script()
{
    if (wp_script_is('plugincy-custom-editor', 'enqueued')) {
        return;
    }
    wp_enqueue_script(
        'plugincy-custom-editor',
        plugin_dir_url(__FILE__) . 'includes/blocks/editor.js',
        array('wp-blocks', 'wp-element', 'wp-edit-post', 'wp-dom-ready', 'wp-plugins'),
        '1.0.3',
        true
    );
}
add_action('enqueue_block_editor_assets', 'dapfforwcpro_editor_script');






// Add this after your plugin initialization
add_action('plugins_loaded', 'dapfforwcpro_init_updater');

function dapfforwcpro_init_updater()
{
    if (class_exists('DAPFFORWCPRO_License_Manager')) {
        $license_manager = new DAPFFORWCPRO_License_Manager();

        // Hook into WordPress update system
        add_filter('pre_set_site_transient_update_plugins', function ($transient) use ($license_manager) {
            return dapfforwcpro_check_for_plugin_updates($transient, $license_manager);
        });

        add_filter('plugins_api', function ($result, $action, $args) use ($license_manager) {
            return dapfforwcpro_plugin_api_call($result, $action, $args, $license_manager);
        }, 10, 3);

        add_action('upgrader_process_complete', function ($upgrader_object, $options) use ($license_manager) {
            dapfforwcpro_clear_cache_after_update($upgrader_object, $options, $license_manager);
        }, 10, 2);

        add_action('admin_notices', array($license_manager, 'show_license_notices'));
    }
}

function dapfforwcpro_check_for_plugin_updates($transient, $license_manager)
{
    if (empty($transient->checked)) {
        return $transient;
    }

    if (!$license_manager->is_license_valid_cached()) {
        return $transient;
    }

    $plugin_file = plugin_basename(__FILE__); // This will automatically get the correct path
    $current_version = defined('DAPFFORWCPRO_VERSION') ? DAPFFORWCPRO_VERSION : '1.0.0';

    $update_info = $license_manager->check_for_updates();

    if ($update_info && version_compare($current_version, $update_info->new_version, '<')) {
        $transient->response[$plugin_file] = (object) array(
            'slug' => dirname($plugin_file),
            'plugin' => $plugin_file,
            'new_version' => $update_info->new_version,
            'url' => isset($update_info->homepage) ? $update_info->homepage : 'https://plugincy.com/',
            'package' => isset($update_info->download_link) ? $update_info->download_link : '',
            'tested' => /*isset($update_info->tested) ? $update_info->tested :*/ get_bloginfo('version'),
            'requires_php' => isset($update_info->requires_php) ? $update_info->requires_php : '7.0',
            'compatibility' => new stdClass()
        );
    }

    return $transient;
}


function dapfforwcpro_plugin_api_call($result, $action, $args, $license_manager)
{
    if ($action !== 'plugin_information') {
        return $result;
    }

    $plugin_slug = dirname(plugin_basename(__FILE__)); // Auto-detect plugin slug

    if (!isset($args->slug) || $args->slug !== $plugin_slug) {
        return $result;
    }

    if (!$license_manager->is_license_valid_cached()) {
        return $result;
    }

    $license_key = get_option('dapfforwcpro_license_key', '');
    $version_info = $license_manager->get_version_info($license_key);

    if ($version_info) {
        // Unserialize sections if they exist and are serialized
        $sections = array(
            'description' => 'Professional dynamic AJAX product filtering solution for WooCommerce.',
            'changelog' => 'Various improvements and bug fixes.'
        );

        if (isset($version_info->sections)) {
            if (is_string($version_info->sections)) {
                // If sections is a serialized string, unserialize it
                $unserialized_sections = @unserialize($version_info->sections);
                if ($unserialized_sections !== false && is_array($unserialized_sections)) {
                    $sections = array_merge($sections, $unserialized_sections);
                }
            } elseif (is_object($version_info->sections)) {
                // If sections is already an object, convert to array
                $sections = array_merge($sections, (array)$version_info->sections);
            } elseif (is_array($version_info->sections)) {
                // If sections is already an array
                $sections = array_merge($sections, $version_info->sections);
            }
        }

        // Handle banners
        $banners = array();
        if (isset($version_info->banners)) {
            if (is_string($version_info->banners)) {
                // If banners is a serialized string, unserialize it
                $unserialized_banners = @unserialize($version_info->banners);
                if ($unserialized_banners !== false && is_array($unserialized_banners)) {
                    $banners = $unserialized_banners;
                }
            } elseif (is_object($version_info->banners)) {
                // If banners is already an object, convert to array
                $banners = (array)$version_info->banners;
            } elseif (is_array($version_info->banners)) {
                // If banners is already an array
                $banners = $version_info->banners;
            }
        }

        // Handle screenshots - WordPress expects array of URLs with numeric keys
        $base_url = "https://ps.w.org/dynamic-ajax-product-filters-for-woocommerce/assets/";
        $default_screenshots = array(
            "1" => "Filters Demo 1",
            "2" => "Filters Demo 2",
            "3" => "Filters Demo 3",
            "4" => "Filters Demo 4 - Mobile View",
            "5" => "Filters Demo 5 - Mobile View",
            "6" => "Form Manage Settings",
            "7" => "Form Style Settings",
            "8" => "Plugin Advance Settings"
        );

        // Get captions from server or use defaults
        $screenshot_captions = $default_screenshots;
        if (isset($version_info->screenshots)) {
            if (is_string($version_info->screenshots)) {
                $unserialized_screenshots = @unserialize($version_info->screenshots);
                if ($unserialized_screenshots !== false && is_array($unserialized_screenshots)) {
                    $screenshot_captions = $unserialized_screenshots;
                }
            } elseif (is_object($version_info->screenshots)) {
                $screenshot_captions = (array)$version_info->screenshots;
            } elseif (is_array($version_info->screenshots)) {
                $screenshot_captions = $version_info->screenshots;
            }
        }

        // Also add screenshot captions to sections for better display
        if (!empty($screenshot_captions)) {
            $screenshot_section = "<ol>";
            foreach ($screenshot_captions as $number => $caption) {
                $screenshot_section .= "<li>";
                $screenshot_section .= "<a href='{$base_url}screenshot-{$number}.png' target='_blank'><img class='screenshots' src='{$base_url}screenshot-{$number}.png' alt='{$caption}'></a><p>{$caption}</p>";
                $screenshot_section .= "</li>";
            }
            $screenshot_section .= "</ol>";
            $sections['screenshots'] = $screenshot_section;
        }

        return (object) array(
            'name' => 'Dynamic AJAX Product Filters Pro',
            'slug' => $plugin_slug,
            'version' => $version_info->new_version,
            'author' => '<a href="https://plugincy.com">Plugincy</a>',
            'homepage' => 'https://plugincy.com/',
            'requires' => isset($version_info->requires) ? $version_info->requires : '5.0',
            'tested' => /*isset($version_info->tested) ? $version_info->tested :*/ get_bloginfo('version'),
            'requires_php' => isset($version_info->requires_php) ? $version_info->requires_php : '7.0',
            'contributors' => array(
                'plugincy' => array(
                    'profile' => 'https://profiles.wordpress.org/plugincy/',
                    'avatar' => 'https://secure.gravatar.com/avatar/ee0db1e8766d68a4bc66e91b4098310d9604ca7670ac9662c15915c517662b39',
                    'display_name' => 'Plugincy'
                )
            ),
            'sections' => $sections,
            'banners' => $banners,
            'download_link' => isset($version_info->download_link) ? $version_info->download_link : ''
        );
    }

    return $result;
}

function dapfforwcpro_clear_cache_after_update($upgrader_object, $options, $license_manager)
{
    if ($options['action'] === 'update' && $options['type'] === 'plugin') {
        $plugin_file = plugin_basename(__FILE__);

        if (isset($options['plugins']) && in_array($plugin_file, $options['plugins'])) {
            $license_manager->clear_all_cache();
        }
    }
}

// Add this temporarily for testing - remove after testing
add_action('admin_init', function () {
    if (isset($_GET['force_check_updates']) && $_GET['force_check_updates'] === '1') {
        delete_site_transient('update_plugins');
        wp_redirect(admin_url('plugins.php'));
        exit;
    }
});
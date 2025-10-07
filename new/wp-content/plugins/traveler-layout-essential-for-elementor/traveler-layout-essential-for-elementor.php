<?php
/**
 * @package Traveler Traveler Layout Essential For Elementor
 */
/*
Plugin Name: Traveler Layout Essential For Elementor
Plugin URI: https://www.facebook.com/shinethemetoday
Description: Plugin only for Traveler theme
Version: 1.6
Author: ShineTheme
Author URI: https://www.facebook.com/shinethemetoday
License: GPLv2 or later
Text Domain: traveler-layout-essential
*/

defined('ABSPATH') or die('Hey, what are you doing here? You silly human!');

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    include_once dirname(__FILE__) . '/vendor/autoload.php';
}
define('ST_ESSENTIA_PLUGIN_VERSION', '1.0.4');
define('ST_ESSENTIAL_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ST_ESSENTIAL_PLUGIN_URL', plugin_dir_url(__FILE__));

if (class_exists('Inc\\STEssentialInit')) {
    Inc\STEssentialInit::registerServices();
}
//Function is called in theme Traveler
if (!function_exists('ste_loadTemplate')) {
    function ste_loadTemplate($name, $data = null)
    {
        if (is_array($data)) {
            extract($data);
        }
        $template = ST_ESSENTIAL_PLUGIN_PATH . 'inc/widget/elements/' . $name . '.php';
        if (is_file($template)) {
            $templateCustom = locate_template(ST_ESSENTIAL_PLUGIN_PATH . 'inc/widget/elements/' . $name . '.php');
            if (is_file($templateCustom)) {
                $template = $templateCustom;
            }
            ob_start();
            include $template;
            $html = ob_get_clean();
            return $html;
        }
    }
}
function tth_get_posts_grandchildren($parent_id, $post_type)
{
    $children = array();
    // grab the posts children

    $posts = get_posts( array( 'posts_per_page' => -1, 'post_status' => 'publish', 'post_type' => $post_type, 'post_parent' => $parent_id, ));

    // now grab the grand children
    if (!empty($posts)) {
        foreach ($posts as $child) {
            // recursion!! hurrah
            $gchildren = tth_get_posts_grandchildren($child->ID, $post_type);
            // merge the grand children into the children array
            if (!empty($gchildren)) {
                $children = array_merge($children, $gchildren);
            }
        }
    }

    // merge in the direct descendants we found earlier
    $children = array_merge($children, $posts);
    return $children;
}

function tth_get_IDs_by_list_post_object($list_post)
{
    $id = array();
    foreach ($list_post as $post) {
        $id[] = $post->ID;
    }
    return $id;
}
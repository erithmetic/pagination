<?php
/*
Plugin Name: pagination
Plugin URI: http://github.com/dkastner/pagination
Description: Customizable pagination.
Version: 1.0
Author: Derek Kastner
Author URI: http://plusrw.wordpress.com
*/


/*  
	This project is a modification to Lester Chan's pagination plugin 
		(http://lesterchan.net/wordpress/readme/pagination.html)

	Copyright 2008  Derek Kastner  (email : dkastner@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


// Use WordPress 2.6 Constants
if (!defined('WP_CONTENT_DIR')) {
	define( 'WP_CONTENT_DIR', ABSPATH.'wp-content');
}
if (!defined('WP_CONTENT_URL')) {
	define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
}
if (!defined('WP_PLUGIN_DIR')) {
	define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
}
if (!defined('WP_PLUGIN_URL')) {
	define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
}


// Create Text Domain For Translations
add_action('init', 'pagination_textdomain');
function pagination_textdomain() {
	if (!function_exists('wp_print_styles')) {
		load_plugin_textdomain('pagination', 'wp-content/plugins/pagination');
	} else {
		load_plugin_textdomain('pagination', false, 'pagination');
	}
}


// Page Navigation Option Menu
add_action('admin_menu', 'pagination_menu');
function pagination_menu() {
	if (function_exists('add_options_page')) {
		add_options_page(__('Pagination', 'pagination'), __('Pagination', 'pagination'), 'manage_options', 'pagination/pagination-options.php') ;
	}
}

function paginate() {
	global $wpdb, $wp_query, $pagination;		// mmm global variables...
	$pagination = arary();
	if (!is_single()) {
		$request = $wp_query->request;
		$posts_per_page = intval(get_query_var('posts_per_page'));
		$current_page = intval(get_query_var('paged'));
		$pagination_options = get_option('pagination_options');
		$numposts = $wp_query->found_posts;
		$max_page = $wp_query->max_num_pages;
		if(empty($current_page) || $current_page == 0) {
			$current_page = 1;
		}
		$pages_to_show = intval($pagination_options['num_pages']);
		$pages_to_show_minus_1 = $pages_to_show-1;
		$half_page_start = floor($pages_to_show_minus_1/2);
		$half_page_end = ceil($pages_to_show_minus_1/2);
		$start_page = $current_page - $half_page_start;
		if($start_page <= 0) {
			$start_page = 1;
		}
		$end_page = $current_page + $half_page_end;
		if(($end_page - $start_page) != $pages_to_show_minus_1) {
			$end_page = $start_page + $pages_to_show_minus_1;
		}
		if($end_page > $max_page) {
			$start_page = $max_page - $pages_to_show_minus_1;
			$end_page = $max_page;
		}
		if($start_page <= 0) {
			$start_page = 1;
		}
		
		$pagination['first_page'] = FALSE;
		$pagination['last_page'] = FALSE;
		$pagination['max_pages'] = $max_page;
		$pagination['current_page'] = $current_page;
		$pagination['display_num_pages'] = $pages_to_show;
		
		$pagination['pageset'] = array();
		
		if ($start_page >= 2 && $pages_to_show < $max_page) {
			$pagination['first_page'] = .clean_url(get_pagenum_link());
		}
		for($i = $start_page; $i  <= $end_page; $i++) {
			if($i == $current_page) {
				$pagination['pageset'][$i] = FALSE;
			} else {
				$pagination['pageset'][$i] = get_pagenum_link($i);
			}
		}
		if ($end_page < $max_page) {
			$pagination['last_page'] = get_pagenum_link($max_page);
		}
	}
}

// Function: Page Navigation Options
add_action('activate_pagination/pagination.php', 'pagination_init');
function pagination_init() {
	$pagination_options = array();
	$pagination_options['num_pages'] = 5;
	$pagination_options['always_show'] = 0;
	add_option('pagination_options', $pagination_options, 'Pagination Options');
}
?>
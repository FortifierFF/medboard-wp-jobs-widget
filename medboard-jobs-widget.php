<?php
/**
 * Plugin Name:       Medboard Jobs Widget
 * Plugin URI:        https://medboard.bg
 * Description:       Embed live Medboard job listings on a WordPress site via shortcode or block. Uses the employer widget token from Medboard → Profile → Widget.
 * Version:           1.0.1
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Medboard
 * Author URI:        https://medboard.bg
 * License:           GPL-2.0-or-later
 * Text Domain:       medboard-jobs-widget
 *
 * Hospital sites install this plugin, paste the mbw_… token from Medboard,
 * and allowlist their WordPress domain under Medboard /profile/widget.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MEDBOARD_JOBS_WIDGET_VERSION', '1.0.1' );
define( 'MEDBOARD_JOBS_WIDGET_FILE', __FILE__ );
define( 'MEDBOARD_JOBS_WIDGET_DIR', plugin_dir_path( __FILE__ ) );
define( 'MEDBOARD_JOBS_WIDGET_URL', plugin_dir_url( __FILE__ ) );

require_once MEDBOARD_JOBS_WIDGET_DIR . 'includes/class-settings.php';
require_once MEDBOARD_JOBS_WIDGET_DIR . 'includes/class-renderer.php';
require_once MEDBOARD_JOBS_WIDGET_DIR . 'includes/class-shortcode.php';
require_once MEDBOARD_JOBS_WIDGET_DIR . 'includes/class-block.php';

/**
 * Boot plugin pieces on init so translations and hooks load in the right order.
 */
function medboard_jobs_widget_bootstrap() {
	Medboard_Jobs_Widget_Settings::init();
	Medboard_Jobs_Widget_Shortcode::init();
	Medboard_Jobs_Widget_Block::init();
}
add_action( 'plugins_loaded', 'medboard_jobs_widget_bootstrap' );

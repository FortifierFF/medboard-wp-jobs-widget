<?php
/**
 * Shortcode: [medboard_jobs]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Medboard_Jobs_Widget_Shortcode {

	public static function init() {
		add_shortcode( 'medboard_jobs', array( __CLASS__, 'render' ) );
	}

	/**
	 * @param array<string, string>|string $atts Shortcode attributes.
	 */
	public static function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'token'          => '',
				'site_url'       => '',
				'mode'           => '', // iframe | script
				'theme'          => '',
				'page_size'      => '',
				'locale'         => '',
				'primary_color'  => '',
				'height'         => '',
				'show_logo'      => '',
				'show_salary'    => '',
				'show_date'      => '',
				'show_workplace' => '',
				'show_view_all'  => '',
			),
			$atts,
			'medboard_jobs'
		);

		return Medboard_Jobs_Widget_Renderer::render( $atts );
	}
}

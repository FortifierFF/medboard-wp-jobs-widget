<?php
/**
 * Simple dynamic Gutenberg block that reuses the shortcode renderer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Medboard_Jobs_Widget_Block {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
	}

	public static function register() {
		// Editor script is tiny — only registers the block; output is PHP-rendered.
		wp_register_script(
			'medboard-jobs-widget-block',
			MEDBOARD_JOBS_WIDGET_URL . 'assets/block.js',
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
			MEDBOARD_JOBS_WIDGET_VERSION,
			true
		);

		register_block_type(
			'medboard/jobs-widget',
			array(
				'api_version'     => 2,
				'title'           => __( 'Medboard Jobs', 'medboard-jobs-widget' ),
				'description'     => __( 'Embed Medboard job listings for this employer.', 'medboard-jobs-widget' ),
				'category'        => 'widgets',
				'icon'            => 'list-view',
				'editor_script'   => 'medboard-jobs-widget-block',
				'render_callback' => array( __CLASS__, 'render' ),
				'attributes'      => array(
					'theme'     => array(
						'type'    => 'string',
						'default' => '',
					),
					'page_size' => array(
						'type'    => 'number',
						'default' => 0,
					),
					'locale'    => array(
						'type'    => 'string',
						'default' => '',
					),
					'mode'      => array(
						'type'    => 'string',
						'default' => '',
					),
					'height'    => array(
						'type'    => 'number',
						'default' => 0,
					),
				),
			)
		);
	}

	/**
	 * @param array<string, mixed> $attributes Block attributes.
	 */
	public static function render( $attributes ) {
		$overrides = array();

		if ( ! empty( $attributes['theme'] ) ) {
			$overrides['theme'] = $attributes['theme'];
		}
		if ( ! empty( $attributes['page_size'] ) ) {
			$overrides['page_size'] = $attributes['page_size'];
		}
		if ( ! empty( $attributes['locale'] ) ) {
			$overrides['locale'] = $attributes['locale'];
		}
		if ( ! empty( $attributes['mode'] ) ) {
			$overrides['mode'] = $attributes['mode'];
		}
		if ( ! empty( $attributes['height'] ) ) {
			$overrides['height'] = $attributes['height'];
		}

		return Medboard_Jobs_Widget_Renderer::render( $overrides );
	}
}

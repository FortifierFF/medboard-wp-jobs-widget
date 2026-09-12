<?php
/**
 * Builds iframe / script markup that matches Medboard's embed helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Medboard_Jobs_Widget_Renderer {

	/**
	 * Merge shortcode/block attrs over Settings defaults.
	 *
	 * @param array<string, mixed> $overrides Shortcode attributes.
	 * @return array<string, mixed>
	 */
	public static function resolve_config( $overrides = array() ) {
		$opts = Medboard_Jobs_Widget_Settings::get_options();
		$overrides = is_array( $overrides ) ? $overrides : array();

		$config = array(
			'token'          => isset( $overrides['token'] ) && $overrides['token'] !== ''
				? sanitize_text_field( $overrides['token'] )
				: (string) $opts['token'],
			'site_url'       => untrailingslashit(
				! empty( $overrides['site_url'] )
					? esc_url_raw( $overrides['site_url'] )
					: (string) $opts['site_url']
			),
			'embed_mode'     => ! empty( $overrides['mode'] )
				? sanitize_key( $overrides['mode'] )
				: (string) $opts['embed_mode'],
			'theme'          => ! empty( $overrides['theme'] )
				? sanitize_key( $overrides['theme'] )
				: (string) $opts['theme'],
			'page_size'      => isset( $overrides['page_size'] )
				? max( 1, min( 50, absint( $overrides['page_size'] ) ) )
				: (int) $opts['page_size'],
			'locale'         => ! empty( $overrides['locale'] )
				? sanitize_key( $overrides['locale'] )
				: (string) $opts['locale'],
			'primary_color'  => ! empty( $overrides['primary_color'] )
				? ( sanitize_hex_color( $overrides['primary_color'] ) ?: (string) $opts['primary_color'] )
				: (string) $opts['primary_color'],
			'height'         => isset( $overrides['height'] )
				? max( 240, min( 2000, absint( $overrides['height'] ) ) )
				: (int) $opts['height'],
			'show_logo'      => self::bool_attr( $overrides, 'show_logo', ! empty( $opts['show_logo'] ) ),
			'show_salary'    => self::bool_attr( $overrides, 'show_salary', ! empty( $opts['show_salary'] ) ),
			'show_date'      => self::bool_attr( $overrides, 'show_date', ! empty( $opts['show_date'] ) ),
			'show_workplace' => self::bool_attr( $overrides, 'show_workplace', ! empty( $opts['show_workplace'] ) ),
			'show_view_all'  => self::bool_attr( $overrides, 'show_view_all', ! empty( $opts['show_view_all'] ) ),
		);

		if ( ! in_array( $config['embed_mode'], array( 'iframe', 'script' ), true ) ) {
			$config['embed_mode'] = 'iframe';
		}
		if ( ! in_array( $config['theme'], array( 'card', 'list', 'compact' ), true ) ) {
			$config['theme'] = 'card';
		}
		if ( ! in_array( $config['locale'], array( 'bg', 'en' ), true ) ) {
			$config['locale'] = 'bg';
		}
		if ( empty( $config['site_url'] ) ) {
			$config['site_url'] = Medboard_Jobs_Widget_Settings::DEFAULT_SITE_URL;
		}

		return $config;
	}

	/**
	 * @param array<string, mixed> $overrides Attr bag.
	 * @param string               $key       Attr name.
	 * @param bool                 $default   Fallback.
	 */
	private static function bool_attr( $overrides, $key, $default ) {
		if ( ! array_key_exists( $key, $overrides ) || $overrides[ $key ] === '' || $overrides[ $key ] === null ) {
			return (bool) $default;
		}
		$raw = strtolower( (string) $overrides[ $key ] );
		return in_array( $raw, array( '1', 'true', 'yes', 'on' ), true );
	}

	/**
	 * Public embed page URL (same query shape as Medboard buildEmbedJobsUrl).
	 *
	 * @param array<string, mixed> $config Resolved config.
	 */
	public static function build_embed_url( $config ) {
		$prefix = ( $config['locale'] === 'bg' ) ? '' : '/' . $config['locale'];
		$query  = array(
			'token'           => $config['token'],
			'theme'           => $config['theme'],
			'pageSize'        => (string) $config['page_size'],
			'locale'          => $config['locale'],
			'primaryColor'    => $config['primary_color'],
			'textColor'       => '#001627',
			'backgroundColor' => '#ffffff',
			'borderRadius'    => '12',
			'showLogo'        => $config['show_logo'] ? 'true' : 'false',
			'showSalary'      => $config['show_salary'] ? 'true' : 'false',
			'showDate'        => $config['show_date'] ? 'true' : 'false',
			'showWorkplace'   => $config['show_workplace'] ? 'true' : 'false',
			'showViewAllLink' => $config['show_view_all'] ? 'true' : 'false',
			'linkTarget'      => '_blank',
		);

		return $config['site_url'] . $prefix . '/embed/jobs?' . http_build_query( $query, '', '&', PHP_QUERY_RFC3986 );
	}

	/**
	 * @param array<string, mixed> $overrides Shortcode/block attrs.
	 * @return string Safe HTML.
	 */
	public static function render( $overrides = array() ) {
		$config = self::resolve_config( $overrides );

		if ( $config['token'] === '' ) {
			if ( current_user_can( 'manage_options' ) ) {
				return '<p class="medboard-jobs-widget-admin-notice">' . esc_html__(
					'Medboard Jobs Widget: set the embed token under Settings → Medboard Jobs.',
					'medboard-jobs-widget'
				) . '</p>';
			}
			return '';
		}

		if ( $config['embed_mode'] === 'script' ) {
			return self::render_script( $config );
		}

		return self::render_iframe( $config );
	}

	/**
	 * @param array<string, mixed> $config Resolved config.
	 */
	private static function render_iframe( $config ) {
		$src = self::build_embed_url( $config );
		$height = (int) $config['height'];

		return sprintf(
			'<div class="medboard-jobs-widget medboard-jobs-widget--iframe"><iframe src="%1$s" width="100%%" height="%2$d" style="border:0;" loading="lazy" title="%3$s"></iframe></div>',
			esc_url( $src ),
			$height,
			esc_attr__( 'Medboard jobs', 'medboard-jobs-widget' )
		);
	}

	/**
	 * Loads Medboard public/widget/jobs.js and a mount node (same as FE script snippet).
	 *
	 * @param array<string, mixed> $config Resolved config.
	 */
	private static function render_script( $config ) {
		$script_src = $config['site_url'] . '/widget/jobs.js';
		$handle     = 'medboard-jobs-widget-embed';

		// Enqueue once per page; shortcode/block can appear multiple times.
		// Footer + defer matches Medboard's script snippet; works on WP 6.0+.
		if ( ! wp_script_is( $handle, 'enqueued' ) ) {
			wp_enqueue_script( $handle, $script_src, array(), MEDBOARD_JOBS_WIDGET_VERSION, true );
			wp_script_add_data( $handle, 'strategy', 'defer' );
		}

		$attrs = array(
			'class'                => 'medboard-jobs',
			'data-token'           => $config['token'],
			'data-theme'           => $config['theme'],
			'data-page-size'       => (string) $config['page_size'],
			'data-locale'          => $config['locale'],
			'data-primary-color'   => $config['primary_color'],
			'data-text-color'      => '#001627',
			'data-background-color'=> '#ffffff',
			'data-border-radius'   => '12',
			'data-show-logo'       => $config['show_logo'] ? 'true' : 'false',
			'data-show-salary'     => $config['show_salary'] ? 'true' : 'false',
			'data-show-date'       => $config['show_date'] ? 'true' : 'false',
			'data-show-workplace'  => $config['show_workplace'] ? 'true' : 'false',
			'data-show-view-all'   => $config['show_view_all'] ? 'true' : 'false',
			'data-link-target'     => '_blank',
		);

		$html = '<div class="medboard-jobs-widget medboard-jobs-widget--script"><div';
		foreach ( $attrs as $name => $value ) {
			$html .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( $value ) );
		}
		$html .= '></div></div>';

		return $html;
	}
}

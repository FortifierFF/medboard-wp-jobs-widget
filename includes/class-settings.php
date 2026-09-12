<?php
/**
 * Admin settings: token, Medboard site URL, default display options.
 *
 * Employers still manage allowed domains + regenerate tokens in Medboard
 * (/profile/widget). This page only stores what WordPress needs to embed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Medboard_Jobs_Widget_Settings {

	const OPTION_KEY = 'medboard_jobs_widget_options';

	/** Default public Medboard frontend (serves /widget/jobs.js and /embed/jobs). */
	const DEFAULT_SITE_URL = 'https://medboard.bg';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Merged options with sane defaults (used by shortcode/block too).
	 *
	 * @return array<string, mixed>
	 */
	public static function get_options() {
		$stored = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		return wp_parse_args(
			$stored,
			array(
				'token'             => '',
				'site_url'          => self::DEFAULT_SITE_URL,
				'embed_mode'        => 'iframe', // iframe | script
				'theme'             => 'card',
				'page_size'         => 8,
				'locale'            => 'bg',
				'primary_color'     => '#4DAFCB',
				'height'            => 640,
				'show_logo'         => 1,
				'show_salary'       => 1,
				'show_date'         => 1,
				'show_workplace'    => 1,
				'show_view_all'     => 1,
			)
		);
	}

	public static function register_menu() {
		add_options_page(
			__( 'Medboard Jobs Widget', 'medboard-jobs-widget' ),
			__( 'Medboard Jobs', 'medboard-jobs-widget' ),
			'manage_options',
			'medboard-jobs-widget',
			array( __CLASS__, 'render_page' )
		);
	}

	public static function register_settings() {
		register_setting(
			'medboard_jobs_widget_group',
			self::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'default'           => array(),
			)
		);
	}

	/**
	 * @param mixed $input Raw POST options.
	 * @return array<string, mixed>
	 */
	public static function sanitize( $input ) {
		$input = is_array( $input ) ? $input : array();
		$out   = self::get_options();

		$out['token'] = isset( $input['token'] ) ? sanitize_text_field( wp_unslash( $input['token'] ) ) : '';

		$site_url = isset( $input['site_url'] ) ? esc_url_raw( wp_unslash( $input['site_url'] ) ) : self::DEFAULT_SITE_URL;
		$out['site_url'] = untrailingslashit( $site_url ? $site_url : self::DEFAULT_SITE_URL );

		$mode = isset( $input['embed_mode'] ) ? sanitize_key( $input['embed_mode'] ) : 'iframe';
		$out['embed_mode'] = in_array( $mode, array( 'iframe', 'script' ), true ) ? $mode : 'iframe';

		$theme = isset( $input['theme'] ) ? sanitize_key( $input['theme'] ) : 'card';
		$out['theme'] = in_array( $theme, array( 'card', 'list', 'compact' ), true ) ? $theme : 'card';

		$out['page_size'] = max( 1, min( 50, absint( $input['page_size'] ?? 8 ) ) );
		$locale           = isset( $input['locale'] ) ? sanitize_key( $input['locale'] ) : 'bg';
		$out['locale']    = in_array( $locale, array( 'bg', 'en' ), true ) ? $locale : 'bg';

		$color = isset( $input['primary_color'] ) ? sanitize_hex_color( wp_unslash( $input['primary_color'] ) ) : '';
		$out['primary_color'] = $color ? $color : '#4DAFCB';

		$out['height'] = max( 240, min( 2000, absint( $input['height'] ?? 640 ) ) );

		foreach ( array( 'show_logo', 'show_salary', 'show_date', 'show_workplace', 'show_view_all' ) as $flag ) {
			$out[ $flag ] = empty( $input[ $flag ] ) ? 0 : 1;
		}

		return $out;
	}

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$opts = self::get_options();
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Medboard Jobs Widget', 'medboard-jobs-widget' ); ?></h1>
			<p>
				<?php
				echo esc_html__(
					'Paste the embed token from Medboard → Profile → Widget. Also add this WordPress site domain to the allowed domains list there.',
					'medboard-jobs-widget'
				);
				?>
			</p>

			<form method="post" action="options.php">
				<?php settings_fields( 'medboard_jobs_widget_group' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="medboard_token"><?php esc_html_e( 'Embed token', 'medboard-jobs-widget' ); ?></label>
						</th>
						<td>
							<input
								type="text"
								class="regular-text"
								id="medboard_token"
								name="<?php echo esc_attr( self::OPTION_KEY ); ?>[token]"
								value="<?php echo esc_attr( $opts['token'] ); ?>"
								placeholder="mbw_…"
								autocomplete="off"
							/>
							<p class="description">
								<?php esc_html_e( 'Long-lived token starting with mbw_ from Medboard widget settings.', 'medboard-jobs-widget' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="medboard_site_url"><?php esc_html_e( 'Medboard site URL', 'medboard-jobs-widget' ); ?></label>
						</th>
						<td>
							<input
								type="url"
								class="regular-text"
								id="medboard_site_url"
								name="<?php echo esc_attr( self::OPTION_KEY ); ?>[site_url]"
								value="<?php echo esc_attr( $opts['site_url'] ); ?>"
							/>
							<p class="description">
								<?php esc_html_e( 'Usually https://medboard.bg (or your staging frontend). Not the Strapi API URL.', 'medboard-jobs-widget' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Embed mode', 'medboard-jobs-widget' ); ?></th>
						<td>
							<select name="<?php echo esc_attr( self::OPTION_KEY ); ?>[embed_mode]">
								<option value="iframe" <?php selected( $opts['embed_mode'], 'iframe' ); ?>>
									<?php esc_html_e( 'iframe (recommended)', 'medboard-jobs-widget' ); ?>
								</option>
								<option value="script" <?php selected( $opts['embed_mode'], 'script' ); ?>>
									<?php esc_html_e( 'Script (jobs.js)', 'medboard-jobs-widget' ); ?>
								</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="medboard_theme"><?php esc_html_e( 'Theme', 'medboard-jobs-widget' ); ?></label>
						</th>
						<td>
							<select id="medboard_theme" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[theme]">
								<?php foreach ( array( 'card', 'list', 'compact' ) as $theme ) : ?>
									<option value="<?php echo esc_attr( $theme ); ?>" <?php selected( $opts['theme'], $theme ); ?>>
										<?php echo esc_html( $theme ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="medboard_page_size"><?php esc_html_e( 'Jobs per page', 'medboard-jobs-widget' ); ?></label>
						</th>
						<td>
							<input
								type="number"
								min="1"
								max="50"
								id="medboard_page_size"
								name="<?php echo esc_attr( self::OPTION_KEY ); ?>[page_size]"
								value="<?php echo esc_attr( (string) $opts['page_size'] ); ?>"
							/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="medboard_locale"><?php esc_html_e( 'Locale', 'medboard-jobs-widget' ); ?></label>
						</th>
						<td>
							<select id="medboard_locale" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[locale]">
								<option value="bg" <?php selected( $opts['locale'], 'bg' ); ?>>bg</option>
								<option value="en" <?php selected( $opts['locale'], 'en' ); ?>>en</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="medboard_primary_color"><?php esc_html_e( 'Primary color', 'medboard-jobs-widget' ); ?></label>
						</th>
						<td>
							<input
								type="text"
								id="medboard_primary_color"
								name="<?php echo esc_attr( self::OPTION_KEY ); ?>[primary_color]"
								value="<?php echo esc_attr( $opts['primary_color'] ); ?>"
							/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="medboard_height"><?php esc_html_e( 'iframe height (px)', 'medboard-jobs-widget' ); ?></label>
						</th>
						<td>
							<input
								type="number"
								min="240"
								max="2000"
								id="medboard_height"
								name="<?php echo esc_attr( self::OPTION_KEY ); ?>[height]"
								value="<?php echo esc_attr( (string) $opts['height'] ); ?>"
							/>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Display', 'medboard-jobs-widget' ); ?></th>
						<td>
							<?php
							$flags = array(
								'show_logo'      => __( 'Show logo', 'medboard-jobs-widget' ),
								'show_salary'    => __( 'Show salary', 'medboard-jobs-widget' ),
								'show_date'      => __( 'Show date', 'medboard-jobs-widget' ),
								'show_workplace' => __( 'Show workplace', 'medboard-jobs-widget' ),
								'show_view_all'  => __( 'Show “view all” link', 'medboard-jobs-widget' ),
							);
							foreach ( $flags as $key => $label ) :
								?>
								<label style="display:block;margin-bottom:4px;">
									<input
										type="checkbox"
										name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $key ); ?>]"
										value="1"
										<?php checked( ! empty( $opts[ $key ] ) ); ?>
									/>
									<?php echo esc_html( $label ); ?>
								</label>
							<?php endforeach; ?>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>

			<hr />
			<h2><?php esc_html_e( 'Usage', 'medboard-jobs-widget' ); ?></h2>
			<p><?php esc_html_e( 'Shortcode (uses settings above):', 'medboard-jobs-widget' ); ?></p>
			<code>[medboard_jobs]</code>
			<p><?php esc_html_e( 'Optional overrides:', 'medboard-jobs-widget' ); ?></p>
			<code>[medboard_jobs theme="list" page_size="6" locale="en"]</code>
			<p><?php esc_html_e( 'Or insert the “Medboard Jobs” block in the block editor.', 'medboard-jobs-widget' ); ?></p>
		</div>
		<?php
	}
}

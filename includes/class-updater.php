<?php
/**
 * GitHub Releases → WordPress Plugins screen updates.
 *
 * Uses YahnisElsts Plugin Update Checker against:
 * https://github.com/FortifierFF/medboard-wp-jobs-widget/releases
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Medboard_Jobs_Widget_Updater {

	const GITHUB_REPO = 'https://github.com/FortifierFF/medboard-wp-jobs-widget/';

	public static function init() {
		$loader = MEDBOARD_JOBS_WIDGET_DIR . 'lib/plugin-update-checker/plugin-update-checker.php';

		if ( ! file_exists( $loader ) ) {
			return;
		}

		require_once $loader;

		if ( ! class_exists( '\YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
			return;
		}

		$checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
			self::GITHUB_REPO,
			MEDBOARD_JOBS_WIDGET_FILE,
			'medboard-jobs-widget'
		);

		// Prefer our built zip asset over GitHub's auto source archive.
		if ( method_exists( $checker, 'getVcsApi' ) && $checker->getVcsApi() ) {
			$checker->getVcsApi()->enableReleaseAssets( '/medboard-jobs-widget\.zip$/' );
		}
	}
}

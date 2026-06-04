<?php
/**
 * Plugin bootstrap.
 *
 * @package SNXWorksCommunityHealthCheck
 */

namespace SNXWorks\CommunityHealthCheck;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin coordinator.
 */
final class Plugin {
	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register hooks.
	 */
	public function init(): void {
		if ( is_admin() ) {
			( new AdminPage( new HealthCheck(), new SupportReport() ) )->register();
		}
	}
}

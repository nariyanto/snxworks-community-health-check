<?php
/**
 * Read-only community health checks.
 *
 * @package SNXWorksCommunityHealthCheck
 */

namespace SNXWorks\CommunityHealthCheck;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Collects safe diagnostic data for community plugins.
 */
final class HealthCheck {
	/**
	 * Return all checks grouped for admin rendering and reports.
	 *
	 * @return array<string,array<int,array<string,string>>>
	 */
	public function get_groups(): array {
		return array(
			'environment' => $this->environment_checks(),
			'community'   => $this->community_checks(),
			'operations'  => $this->operations_checks(),
		);
	}

	/**
	 * WordPress/server checks.
	 *
	 * @return array<int,array<string,string>>
	 */
	private function environment_checks(): array {
		global $wpdb;

		$upload_dir = wp_upload_dir();
		$uploads_ok = empty( $upload_dir['error'] );
		$pretty     = $this->has_pretty_permalinks();

		return array(
			$this->item( 'WordPress', $this->version_status( get_bloginfo( 'version' ), '6.3' ), get_bloginfo( 'version' ), 'WordPress 6.3+ is recommended for current community-site maintenance.' ),
			$this->item( 'PHP', $this->version_status( PHP_VERSION, '7.4' ), PHP_VERSION, 'PHP 7.4+ matches this plugin baseline; newer maintained PHP versions are preferred.' ),
			$this->item( 'Database', 'info', $wpdb ? $wpdb->db_version() : 'unknown', 'Database version as reported by WordPress.' ),
			$this->item( 'Permalinks', $pretty ? 'good' : 'warning', $pretty ? 'Pretty permalinks enabled' : 'Plain permalinks detected', 'Community profiles and activity routes usually work best with pretty permalinks.' ),
			$this->item( 'Uploads directory', $uploads_ok ? 'good' : 'warning', $uploads_ok ? 'Writable/available' : (string) $upload_dir['error'], 'Community sites rely on avatars, covers, attachments, and media uploads.' ),
			$this->item( 'Memory limit', $this->memory_status(), WP_MEMORY_LIMIT, 'Community plugins can need more memory on busy admin/support screens.' ),
		);
	}

	/**
	 * PeepSo/BuddyPress presence and compatibility checks.
	 *
	 * @return array<int,array<string,string>>
	 */
	private function community_checks(): array {
		$plugins     = $this->active_plugins();
		$peepso      = $this->detect_peepso( $plugins );
		$buddypress  = $this->detect_buddypress( $plugins );
		$both_active = 'good' === $peepso['status'] && 'good' === $buddypress['status'];

		$items   = array();
		$items[] = $peepso;
		$items[] = $buddypress;
		$items[] = $this->item(
			'Community stack overlap',
			$both_active ? 'warning' : 'good',
			$both_active ? 'PeepSo and BuddyPress both appear active' : 'No PeepSo/BuddyPress overlap detected',
			'Running multiple community suites can be intentional, but support teams should verify routing, profiles, notifications, and performance impact.'
		);
		$items[] = $this->item(
			'Rest API',
			$this->is_rest_available() ? 'good' : 'warning',
			$this->is_rest_available() ? 'Available' : 'Unavailable or filtered',
			'Modern community plugins and support tooling often rely on the REST API.'
		);

		return $items;
	}

	/**
	 * Operational health checks.
	 *
	 * @return array<int,array<string,string>>
	 */
	private function operations_checks(): array {
		$cron_disabled = defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON;
		$debug_log     = defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG;
		$mail_check    = function_exists( 'wp_mail' );

		return array(
			$this->item( 'WP-Cron', $cron_disabled ? 'warning' : 'good', $cron_disabled ? 'DISABLE_WP_CRON is true' : 'Default WP-Cron trigger enabled', 'If WP-Cron is disabled, confirm a real server cron is configured for notifications and scheduled community jobs.' ),
			$this->item( 'Debug logging', $debug_log ? 'good' : 'info', $debug_log ? 'WP_DEBUG_LOG enabled' : 'WP_DEBUG_LOG not enabled', 'Temporary debug logging can help during support, but should be reviewed for privacy and disabled when not needed.' ),
			$this->item( 'Email function', $mail_check ? 'good' : 'warning', $mail_check ? 'wp_mail() available' : 'wp_mail() missing', 'Community sites rely on registration, notification, reset-password, and moderation emails.' ),
			$this->item( 'User registration', get_option( 'users_can_register' ) ? 'info' : 'good', get_option( 'users_can_register' ) ? 'Open registration enabled' : 'Open registration disabled', 'Open registration may be required for communities, but should be paired with spam and moderation controls.' ),
		);
	}

	/**
	 * Detect PeepSo signals.
	 *
	 * @param array<string,string> $plugins Active plugin map.
	 * @return array<string,string>
	 */
	private function detect_peepso( array $plugins ): array {
		$signals = array();
		foreach ( $plugins as $file => $name ) {
			if ( false !== stripos( $file, 'peepso' ) || false !== stripos( $name, 'peepso' ) ) {
				$signals[] = $name;
			}
		}

		if ( class_exists( '\PeepSo' ) ) {
			$signals[] = 'PeepSo class loaded';
		}

		return $this->item(
			'PeepSo',
			empty( $signals ) ? 'info' : 'good',
			empty( $signals ) ? 'Not detected' : implode( ', ', array_unique( $signals ) ),
			'Detects PeepSo by active plugin metadata and loaded runtime classes without calling private PeepSo APIs.'
		);
	}

	/**
	 * Detect BuddyPress signals.
	 *
	 * @param array<string,string> $plugins Active plugin map.
	 * @return array<string,string>
	 */
	private function detect_buddypress( array $plugins ): array {
		$signals = array();
		foreach ( $plugins as $file => $name ) {
			if ( false !== stripos( $file, 'buddypress' ) || false !== stripos( $name, 'buddypress' ) ) {
				$signals[] = $name;
			}
		}

		if ( function_exists( 'buddypress' ) || class_exists( '\BuddyPress' ) ) {
			$signals[] = 'BuddyPress runtime loaded';
		}

		return $this->item(
			'BuddyPress',
			empty( $signals ) ? 'info' : 'good',
			empty( $signals ) ? 'Not detected' : implode( ', ', array_unique( $signals ) ),
			'Detects BuddyPress by active plugin metadata and public runtime signals.'
		);
	}

	/**
	 * Active plugin file => name map.
	 *
	 * @return array<string,string>
	 */
	private function active_plugins(): array {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins    = function_exists( 'get_plugins' ) ? get_plugins() : array();
		$active_plugins = (array) get_option( 'active_plugins', array() );
		$network_active = is_multisite() ? array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) ) : array();
		$active_files   = array_unique( array_merge( $active_plugins, $network_active ) );
		$active         = array();

		foreach ( $active_files as $file ) {
			$name             = isset( $all_plugins[ $file ]['Name'] ) ? $all_plugins[ $file ]['Name'] : $file;
			$active[ $file ] = wp_strip_all_tags( (string) $name );
		}

		return $active;
	}

	/**
	 * Create normalized item.
	 */
	private function item( string $label, string $status, string $value, string $note ): array {
		return array(
			'label'  => $label,
			'status' => $status,
			'value'  => $value,
			'note'   => $note,
		);
	}

	/**
	 * Check semantic-ish version against baseline.
	 */
	private function version_status( string $current, string $minimum ): string {
		return version_compare( $current, $minimum, '>=' ) ? 'good' : 'warning';
	}

	/**
	 * Basic memory-limit status.
	 */
	private function memory_status(): string {
		$bytes = wp_convert_hr_to_bytes( WP_MEMORY_LIMIT );
		return $bytes >= 128 * 1024 * 1024 ? 'good' : 'info';
	}

	/**
	 * Pretty permalink detection.
	 */
	private function has_pretty_permalinks(): bool {
		return '' !== (string) get_option( 'permalink_structure' );
	}

	/**
	 * REST availability without making remote calls.
	 */
	private function is_rest_available(): bool {
		return function_exists( 'rest_get_server' ) && ! has_filter( 'rest_enabled', '__return_false' );
	}
}

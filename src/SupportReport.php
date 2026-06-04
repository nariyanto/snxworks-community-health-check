<?php
/**
 * Support report formatting.
 *
 * @package SNXWorksCommunityHealthCheck
 */

namespace SNXWorks\CommunityHealthCheck;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Formats health data into a safe copyable report.
 */
final class SupportReport {
	/**
	 * Build plain-text report.
	 *
	 * @param array<string,array<int,array<string,string>>> $groups Health groups.
	 */
	public function build( array $groups ): string {
		$lines   = array();
		$lines[] = 'SNXWorks Community Health Check';
		$lines[] = 'Generated: ' . gmdate( 'Y-m-d H:i:s' ) . ' UTC';
		$lines[] = 'Site URL: ' . $this->redact_url( home_url( '/' ) );
		$lines[] = 'Plugin version: ' . SNXWORKS_CHC_VERSION;
		$lines[] = '';

		foreach ( $groups as $group => $items ) {
			$lines[] = strtoupper( str_replace( '_', ' ', $group ) );
			foreach ( $items as $item ) {
				$lines[] = sprintf( '- [%s] %s: %s', strtoupper( $item['status'] ), $item['label'], $item['value'] );
			}
			$lines[] = '';
		}

		$lines[] = 'Note: report is read-only and intentionally excludes option values, paths, user data, and secrets.';

		return implode( "
", $lines );
	}

	/**
	 * Redact host slightly for copy/paste safety while preserving scheme and TLD shape.
	 */
	private function redact_url( string $url ): string {
		$parts = wp_parse_url( $url );
		if ( empty( $parts['host'] ) ) {
			return '[site-url]';
		}

		$host_bits = explode( '.', $parts['host'] );
		if ( count( $host_bits ) > 1 ) {
			$host_bits[0] = substr( $host_bits[0], 0, 1 ) . '***';
		}

		$scheme = ! empty( $parts['scheme'] ) ? $parts['scheme'] : 'https';
		return $scheme . '://' . implode( '.', $host_bits ) . '/';
	}
}

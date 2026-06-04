<?php
/**
 * Plugin Name: SNXWorks Community Health Check
 * Description: Read-only health checks for WordPress community sites using PeepSo and BuddyPress.
 * Version: 0.1.0
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * Author: Septiyan Nariyanto
 * Author URI: https://nariyanto.id
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: snxworks-community-health-check
 * Domain Path: /languages
 *
 * @package SNXWorksCommunityHealthCheck
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SNXWORKS_CHC_VERSION', '0.1.0' );
define( 'SNXWORKS_CHC_FILE', __FILE__ );
define( 'SNXWORKS_CHC_DIR', plugin_dir_path( __FILE__ ) );
define( 'SNXWORKS_CHC_URL', plugin_dir_url( __FILE__ ) );

require_once SNXWORKS_CHC_DIR . 'src/HealthCheck.php';
require_once SNXWORKS_CHC_DIR . 'src/SupportReport.php';
require_once SNXWORKS_CHC_DIR . 'src/AdminPage.php';
require_once SNXWORKS_CHC_DIR . 'src/Plugin.php';

add_action(
	'plugins_loaded',
	static function () {
		load_plugin_textdomain( 'snxworks-community-health-check', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		SNXWorks\CommunityHealthCheck\Plugin::instance()->init();
	}
);

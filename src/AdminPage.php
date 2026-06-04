<?php
/**
 * Admin UI.
 *
 * @package SNXWorksCommunityHealthCheck
 */

namespace SNXWorks\CommunityHealthCheck;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and renders the admin page.
 */
final class AdminPage {
	/** @var HealthCheck */
	private $health_check;

	/** @var SupportReport */
	private $support_report;

	public function __construct( HealthCheck $health_check, SupportReport $support_report ) {
		$this->health_check   = $health_check;
		$this->support_report = $support_report;
	}

	/**
	 * Register hooks.
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Add Tools submenu page.
	 */
	public function add_page(): void {
		add_management_page(
			esc_html__( 'SNXWorks Community Health Check', 'snxworks-community-health-check' ),
			esc_html__( 'Community Health', 'snxworks-community-health-check' ),
			'manage_options',
			'snxworks-community-health-check',
			array( $this, 'render' )
		);
	}

	/**
	 * Enqueue admin assets only on this plugin page.
	 */
	public function enqueue_assets( string $hook_suffix ): void {
		if ( 'tools_page_snxworks-community-health-check' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'snxworks-community-health-check-admin',
			SNXWORKS_CHC_URL . 'assets/admin.css',
			array(),
			SNXWORKS_CHC_VERSION
		);
		wp_enqueue_script(
			'snxworks-community-health-check-admin',
			SNXWORKS_CHC_URL . 'assets/admin.js',
			array(),
			SNXWORKS_CHC_VERSION,
			true
		);
	}

	/**
	 * Render page.
	 */
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'snxworks-community-health-check' ) );
		}

		$groups = $this->health_check->get_groups();
		$report = $this->support_report->build( $groups );
		?>
		<div class="wrap snxworks-chc">
			<h1><?php esc_html_e( 'SNXWorks Community Health Check', 'snxworks-community-health-check' ); ?></h1>
			<p class="description"><?php esc_html_e( 'Read-only checks for PeepSo and BuddyPress community support workflows.', 'snxworks-community-health-check' ); ?></p>

			<div class="snxworks-chc-grid">
				<?php foreach ( $groups as $group => $items ) : ?>
					<section class="snxworks-chc-card">
						<h2><?php echo esc_html( ucwords( str_replace( '_', ' ', $group ) ) ); ?></h2>
						<ul class="snxworks-chc-list">
							<?php foreach ( $items as $item ) : ?>
								<li class="snxworks-chc-item is-<?php echo esc_attr( $item['status'] ); ?>">
									<span class="snxworks-chc-status"><?php echo esc_html( strtoupper( $item['status'] ) ); ?></span>
									<strong><?php echo esc_html( $item['label'] ); ?></strong>
									<span><?php echo esc_html( $item['value'] ); ?></span>
									<small><?php echo esc_html( $item['note'] ); ?></small>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endforeach; ?>
			</div>

			<section class="snxworks-chc-card snxworks-chc-report">
				<h2><?php esc_html_e( 'Copyable support report', 'snxworks-community-health-check' ); ?></h2>
				<p><?php esc_html_e( 'This report redacts the site URL and excludes paths, user data, option values, and secrets.', 'snxworks-community-health-check' ); ?></p>
				<textarea id="snxworks-chc-report" readonly rows="16"><?php echo esc_textarea( $report ); ?></textarea>
				<p><button type="button" class="button button-primary" data-snxworks-copy-report><?php esc_html_e( 'Copy support report', 'snxworks-community-health-check' ); ?></button> <span class="snxworks-chc-copy-status" aria-live="polite"></span></p>
			</section>
		</div>
		<?php
	}
}

<?php
$root = dirname( __DIR__ );
$main = file_get_contents( $root . '/snxworks-community-health-check.php' );
$health = file_get_contents( $root . '/src/HealthCheck.php' );
$admin = file_get_contents( $root . '/src/AdminPage.php' );
$report = file_get_contents( $root . '/src/SupportReport.php' );

$assertions = array(
	'plugin stays v0.1.0' => substr_count( $main, '0.1.0' ) >= 1,
	'no remote requests' => false === stripos( $health . $admin . $report, 'wp_remote_' ),
	'no destructive deletes' => false === stripos( $health . $admin . $report, 'delete_option' ) && false === stripos( $health . $admin . $report, 'wp_delete_' ),
	'admin capability check' => false !== strpos( $admin, "current_user_can( 'manage_options' )" ),
	'copy JS enqueued' => false !== strpos( $admin, 'wp_enqueue_script' ),
	'PeepSo detector present' => false !== strpos( $health, 'detect_peepso' ),
	'BuddyPress detector present' => false !== strpos( $health, 'detect_buddypress' ),
	'report redaction present' => false !== strpos( $report, 'redact_url' ),
);
foreach ( $assertions as $label => $passed ) {
	if ( ! $passed ) {
		fwrite( STDERR, "Failed assertion: {$label}
" );
		exit( 1 );
	}
}
echo "Plugin safety tests OK
";

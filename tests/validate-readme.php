<?php
$root   = dirname( __DIR__ );
$readme = file_get_contents( $root . '/readme.txt' );
$main   = file_get_contents( $root . '/snxworks-community-health-check.php' );
$checks = array(
	'readme title'     => '=== SNXWorks Community Health Check ===',
	'stable tag'       => 'Stable tag: 0.1.0',
	'requires wp'      => 'Requires at least: 6.3',
	'tested up to'     => 'Tested up to: 6.8',
	'requires php'     => 'Requires PHP: 7.4',
	'license'          => 'License: GPLv2 or later',
	'main plugin name' => 'Plugin Name: SNXWorks Community Health Check',
	'main version'     => 'Version: 0.1.0',
	'text domain'      => 'Text Domain: snxworks-community-health-check',
);
foreach ( $checks as $label => $needle ) {
	$main_check = 0 === strpos( $label, 'main' ) || 'text domain' === $label;
	$haystack   = $main_check ? $main : $readme;
	if ( false === strpos( $haystack, $needle ) ) {
		fwrite( STDERR, "Missing {$label}: {$needle}\n" );
		exit( 1 );
	}
}
echo "Readme/header metadata OK
";

=== SNXWorks Community Health Check ===
Contributors: nariyanto
Tags: community, peepso, buddypress, health check, support
Requires at least: 6.3
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Read-only health checks for WordPress community sites using PeepSo and BuddyPress.

== Description ==

SNXWorks Community Health Check gives WordPress administrators a safe, read-only support screen for community websites that use PeepSo, BuddyPress, or both.

The plugin focuses on practical support signals: WordPress and PHP baseline, permalink status, upload directory availability, memory limit, PeepSo and BuddyPress detection, community stack overlap, WP-Cron state, debug logging, email function availability, registration setting, and REST API availability.

It also includes a copyable support report designed for troubleshooting notes. The report intentionally excludes option values, filesystem paths, user data, and secrets.

= Safety =

* Read-only checks only.
* No destructive cleanup actions.
* No external calls or telemetry.
* Admin-only access under Tools > Community Health.
* Copyable report redacts the site URL.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/snxworks-community-health-check/`, or install the ZIP through the WordPress admin.
2. Activate the plugin through the Plugins screen.
3. Go to Tools > Community Health.

== Frequently Asked Questions ==

= Is this an official PeepSo or BuddyPress plugin? =

No. This is an independent support utility by Septiyan Nariyanto / SNXWorks. It detects public WordPress plugin/runtime signals and does not call private PeepSo or BuddyPress APIs.

= Does it change my community site? =

No. Version 0.1.0 is read-only and does not change settings, delete data, send emails, or make external requests.

= Does it collect telemetry? =

No. It runs inside your WordPress admin and does not send data anywhere.

== Screenshots ==

1. Community Health screen with environment, plugin, and operations checks.
2. Copyable support report for troubleshooting notes.

== Changelog ==

= 0.1.0 =
* Initial read-only health checks for PeepSo and BuddyPress community support workflows.

# Verification Evidence

Date: 2026-06-04
Plugin version: 0.1.0
Environment: disposable Docker WordPress 6.8.1 / PHP 8.2 / MySQL 8.4 with `WP_DEBUG` and `WP_DEBUG_LOG` enabled.

## Install/activation

- Built deterministic ZIP with `bash scripts/build-release.sh`.
- Installed ZIP into a clean WordPress site with WP-CLI.
- Activated `snxworks-community-health-check` successfully.
- Confirmed active plugin version remains `0.1.0`.

## Community detection cases

### Baseline

- PeepSo: `info` / not detected.
- BuddyPress: `info` / not detected.
- Community stack overlap: `good` / no overlap detected.
- REST API: `good` / available.
- WP-Cron: `good` / default trigger enabled.

### BuddyPress-only

- Installed BuddyPress from WordPress.org (`buddypress` 14.4.0).
- BuddyPress: `good` / BuddyPress runtime loaded.
- PeepSo: `info` / not detected.
- Community stack overlap: `good` / no overlap detected.

### PeepSo signal-only fixture

PeepSo core is not currently available from the public WordPress.org plugin API, so PeepSo detection was verified with a local-only fixture plugin that exposes PeepSo-style active-plugin metadata and a `PeepSo` runtime class signal.

- PeepSo: `good` / PeepSo fixture and class loaded.
- BuddyPress: `info` / not detected.
- Community stack overlap: `good` / no overlap detected.

### Combined BuddyPress + PeepSo signal fixture

- BuddyPress: `good` / BuddyPress runtime loaded.
- PeepSo: `good` / PeepSo fixture and class loaded.
- Community stack overlap: `warning` / PeepSo and BuddyPress both appear active.

## Admin UI screenshots

- `.wordpress-org/assets/screenshot-1.png` — Community Health admin screen with environment, community, operations, and overlap warning visible.
- `.wordpress-org/assets/screenshot-2.png` — Copyable support report with redacted site URL and copy button visible.

## Debug log

- No `wp-content/debug.log` was created during the clean install verification.
- No plugin fatal/warning/notice/deprecated entries were observed.

## Safety observations

- Checks are read-only.
- No external calls were made by the plugin.
- Support report redacts the site URL and excludes option values, paths, user data, and secrets.
- Plugin version remains `0.1.0` per pre-WordPress.org publication policy.

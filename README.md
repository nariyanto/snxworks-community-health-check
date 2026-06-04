# SNXWorks Community Health Check

Read-only health checks for WordPress community sites using PeepSo and BuddyPress.

![CI](https://github.com/nariyanto/snxworks-community-health-check/actions/workflows/ci.yml/badge.svg)
![License](https://img.shields.io/badge/license-GPL--2.0--or--later-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.3%2B-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4.svg)

## What it checks

- WordPress, PHP, database, memory-limit, uploads, and permalink basics.
- PeepSo presence through active plugin metadata and runtime signals.
- BuddyPress presence through active plugin metadata and runtime signals.
- Community-stack overlap when PeepSo and BuddyPress both appear active.
- WP-Cron, debug logging, `wp_mail()`, user registration, and REST API signals.
- Copyable support report with URL redaction and no secrets/user data.

## Safety principles

- Read-only by default.
- Admin-only under **Tools → Community Health**.
- No external calls.
- No telemetry.
- No cleanup/destructive actions.
- No storage of diagnostic history.

## Local checks

```bash
php tests/run.php
php tests/validate-readme.php
find . -path ./.git -prune -o -path ./dist -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
bash scripts/build-release.sh
python3 scripts/validate-release-zip.py
```

## Version policy

The plugin stays at `0.1.0` until it is published in the WordPress.org Plugin Directory.

## Verification evidence

Clean-install evidence and screenshot notes live in [`docs/verification-evidence.md`](docs/verification-evidence.md). WordPress.org screenshot candidates are stored in `.wordpress-org/assets/` and are excluded from the runtime ZIP.

#!/usr/bin/env python3
from pathlib import Path
import sys
import zipfile

zip_path = Path('dist/snxworks-community-health-check.zip')
if not zip_path.exists():
    raise SystemExit('Missing release ZIP')
required = {
    'snxworks-community-health-check/snxworks-community-health-check.php',
    'snxworks-community-health-check/readme.txt',
    'snxworks-community-health-check/CHANGELOG.md',
    'snxworks-community-health-check/src/Plugin.php',
    'snxworks-community-health-check/src/HealthCheck.php',
    'snxworks-community-health-check/src/AdminPage.php',
    'snxworks-community-health-check/src/SupportReport.php',
    'snxworks-community-health-check/assets/admin.css',
    'snxworks-community-health-check/assets/admin.js',
    'snxworks-community-health-check/languages/snxworks-community-health-check.pot',
}
forbidden_prefixes = (
    'snxworks-community-health-check/.git/',
    'snxworks-community-health-check/.github/',
    'snxworks-community-health-check/.wordpress-org/',
    'snxworks-community-health-check/tests/',
    'snxworks-community-health-check/scripts/',
    'snxworks-community-health-check/dist/',
)
with zipfile.ZipFile(zip_path) as archive:
    names = set(archive.namelist())
    missing = sorted(required - names)
    if missing:
        raise SystemExit('Missing files in ZIP: ' + ', '.join(missing))
    forbidden = [name for name in names if name.startswith(forbidden_prefixes)]
    if forbidden:
        raise SystemExit('Forbidden files in ZIP: ' + ', '.join(forbidden[:10]))
    main = archive.read('snxworks-community-health-check/snxworks-community-health-check.php').decode()
    readme = archive.read('snxworks-community-health-check/readme.txt').decode()
    for marker in ['Version: 0.1.0', 'Text Domain: snxworks-community-health-check']:
        if marker not in main:
            raise SystemExit(f'Missing main marker: {marker}')
    if 'Stable tag: 0.1.0' not in readme:
        raise SystemExit('Stable tag is not 0.1.0')
print(f'Validated {zip_path} with {len(names)} files')

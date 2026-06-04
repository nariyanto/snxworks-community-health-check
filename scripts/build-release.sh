#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DIST="$ROOT/dist"
BUILD="$DIST/snxworks-community-health-check"
ZIP="$DIST/snxworks-community-health-check.zip"
rm -rf "$DIST"
mkdir -p "$BUILD"
rsync -a "$ROOT/" "$BUILD/" \
  --exclude='.git' \
  --exclude='.github' \
  --exclude='.wordpress-org' \
  --exclude='dist' \
  --exclude='tests' \
  --exclude='docs' \
  --exclude='README.md' \
  --exclude='composer.json' \
  --exclude='scripts' \
  --exclude='.gitignore'
if command -v zip >/dev/null 2>&1; then
  (cd "$DIST" && zip -qr "$ZIP" snxworks-community-health-check)
else
  python3 - <<'PY' "$DIST" "$ZIP"
import sys
import zipfile
from pathlib import Path

dist = Path(sys.argv[1])
zip_path = Path(sys.argv[2])
root = dist / 'snxworks-community-health-check'
with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as archive:
    for path in sorted(root.rglob('*')):
        if path.is_file():
            archive.write(path, path.relative_to(dist).as_posix())
PY
fi
echo "$ZIP"

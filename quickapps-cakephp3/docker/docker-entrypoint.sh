#!/usr/bin/env bash
set -e

cd /var/www/html

if [ ! -d vendor ] || [ -z "$(ls -A vendor 2>/dev/null)" ]; then
  echo "📦 Installing PHP dependencies via Composer..."
  composer install --no-interaction --prefer-dist
else
  echo "✅ Dependencies already installed, skipping composer install."
fi

exec "$@"
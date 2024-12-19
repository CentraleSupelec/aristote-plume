#!/bin/sh
set -e

# If dependencies are missing, install them (should happen only in DEV environnement)
if [ ! -f /app/vendor/autoload_runtime.php ]; then
  symfony server:ca:install
  composer install --no-interaction --optimize-autoloader
fi

php bin/console cache:clear
php bin/console doctrine:cache:clear-metadata
php bin/console doctrine:cache:clear-query
php bin/console doctrine:cache:clear-result

#php bin/console app:sync-migrate

echo "$@"
exec "$@"

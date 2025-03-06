#!/bin/sh
set -e

php bin/console --env=test doctrine:query:sql 'CREATE EXTENSION IF NOT EXISTS "citext"'
php bin/console --env=test doctrine:schema:update --force

echo "$@"
exec "$@"

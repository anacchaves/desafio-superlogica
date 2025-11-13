#!/bin/bash

# Set proper permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Wait for database to be ready
echo "Waiting for database..."
until PGPASSWORD=$DB_PASSWORD psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_DATABASE" -c '\q' 2>/dev/null; do
    echo "Database not ready yet, waiting..."
    sleep 2
done
echo "Database is ready!"

# Publish Sanctum configuration if not exists
if [ ! -f /var/www/config/sanctum.php ]; then
    echo "Publishing Sanctum configuration..."
    php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Run seeders in local/development environment
if [ "$APP_ENV" = "local" ] || [ "$APP_ENV" = "development" ]; then
    echo "Running seeders..."
    php artisan db:seed --force
fi

# Clear and cache config
php artisan config:cache
php artisan route:cache

# Start services
echo "Starting services..."
service nginx start
php-fpm

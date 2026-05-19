#!/bin/bash
set -e

# Generate config.php from env if not exists
if [ ! -f /var/www/html/config.php ]; then
    echo ">>> Generating config.php from environment variables..."

    cat > /var/www/html/config.php <<EOF
<?php
return [
    'app' => [
        'name' => '${APP_NAME:-xAI CMS}',
        'url' => '${APP_URL:-http://localhost:8080}',
        'debug' => '${APP_DEBUG:-false}',
        'timezone' => '${APP_TIMEZONE:-Asia/Shanghai}',
    ],
    'db' => [
        'host' => '${DB_HOST:-db}',
        'port' => '${DB_PORT:-3306}',
        'database' => '${DB_DATABASE:-xaicms}',
        'username' => '${DB_USERNAME:-xaicms}',
        'password' => '${DB_PASSWORD:-xaicms}',
        'charset' => 'utf8mb4',
    ],
    'ai' => [
        'default_provider' => '${AI_PROVIDER:-deepseek}',
        'default_api_key' => '${AI_API_KEY:-}',
        'default_base_url' => '${AI_BASE_URL:-https://api.deepseek.com/v1}',
        'default_model' => '${AI_MODEL:-deepseek-v4-flash}',
        'pro_model' => '${AI_PRO_MODEL:-deepseek-v4-pro}',
    ],
];
EOF
    echo ">>> config.php created."
fi

exec "$@"

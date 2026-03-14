#!/bin/bash
cd "$(dirname "$0")"

if ! php -m | grep -qi '^pdo_mysql$'; then
	echo "❌ Missing PHP extension: pdo_mysql"
	echo "Install it locally, then rerun this script."
	exit 1
fi

echo "🐘 Starting PHP server on http://localhost:8000"
php -S localhost:8000 -t public

#!/bin/bash
cd "$(dirname "$0")"
echo "🐘 Starting PHP server on http://localhost:8000"
php -S localhost:8000 -t public

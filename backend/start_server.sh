#!/bin/bash

echo "=== RAILWAY STARTUP SCRIPT ==="
echo "Current directory: $(pwd)"

# Run debug script first
echo "Running debug script..."
php debug_startup.php

echo ""
echo "Starting PHP server on 0.0.0.0:$PORT"
echo "Document root: public/"
echo "================================"

php -S 0.0.0.0:$PORT -t public

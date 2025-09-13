#!/bin/bash

echo "Starting PHP server..."
echo "Current directory: $(pwd)"
echo "Backend directory contents:"
ls -la

echo "Public directory contents:"
ls -la public/

echo "Vendor directory contents:"
ls -la vendor/ 2>/dev/null || echo "Vendor directory not found"

echo "Environment variables:"
echo "PORT: $PORT"
echo "DATABASE_URL: ${DATABASE_URL:0:20}..." # Only show first 20 chars for security

echo "Starting PHP server on 0.0.0.0:$PORT"
cd backend && php -S 0.0.0.0:$PORT -t public

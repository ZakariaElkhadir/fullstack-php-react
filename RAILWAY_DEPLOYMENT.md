# Railway Deployment Guide

## Environment Variables to Set in Railway

You need to set these environment variables in your Railway project dashboard:

### Database Configuration
Choose one of these options:

**Option 1: Using DATABASE_URL (Recommended)**
```
DATABASE_URL=mysql://username:password@host:port/database_name
```

**Option 2: Using individual variables**
```
DB_HOST=your_mysql_host
DB_USER=your_username
DB_PASS=your_password
DB_NAME=your_database_name
DB_PORT=3306
DB_CHARSET=utf8mb4
```

## How to Set Environment Variables in Railway

1. Go to your Railway project dashboard
2. Click on your backend service
3. Go to the "Variables" tab
4. Add the environment variables listed above
5. Redeploy your service

## Files Added for Railway Deployment

- `Procfile` - Tells Railway how to start your application
- `railway.json` - Railway-specific configuration
- `nixpacks.toml` - Build configuration for PHP
- `backend/simple_start.php` - Simple PHP server startup script
- `backend/debug_startup.php` - Debug script to check directory structure
- `backend/start_server.sh` - Bash startup script with debugging
- `backend/public/health_check.php` - Health check endpoint (accessible via web)
- `backend/public/simple_test.php` - Basic PHP test endpoint
- `backend/public/test_autoloader.php` - Autoloader test endpoint
- Updated `backend/public/index.php` - Fixed autoloader path issues and added health check routes

## Testing Your Deployment

1. After deployment, test the basic PHP server:
   ```
   curl https://your-app.up.railway.app/simple_test.php
   ```

2. Test the autoloader:
   ```
   curl https://your-app.up.railway.app/test_autoloader.php
   ```

3. Test the health check:
   ```
   curl https://your-app.up.railway.app/health_check.php
   ```

4. Test the database connection:
   ```
   curl https://your-app.up.railway.app/db_test
   ```

5. Test the GraphQL endpoint:
   ```
   curl -X POST https://your-app.up.railway.app/graphql \
     -H "Content-Type: application/json" \
     -d '{"query":"query { products { id name inStock description category } }"}'
   ```

## Troubleshooting

If you still get 502 errors:

1. Check the Railway logs in your dashboard
2. Verify all environment variables are set correctly
3. Make sure your database is accessible from Railway
4. Test the health check endpoint first

## Common Issues

- **Autoloader not found**: The updated index.php now checks multiple paths
- **Database connection failed**: Verify your DATABASE_URL or individual DB variables
- **Port binding issues**: Railway automatically sets the PORT environment variable

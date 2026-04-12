# Spatie Laravel Permission Fix for InfinityFree Production

## Problem
Role-based permissions not working on InfinityFree shared hosting due to guard configuration and caching issues.

## Solution Steps (Execute in Order)

### Step 1: Upload Updated Config File
Upload the modified `config/permission.php` file to your production server. The file now includes:
```php
'guards' => [
    'web',
],
```

### Step 2: Clear All Caches on Production

#### Option A: Using the Web Route (Easiest)
Visit this URL in your browser (replace with your actual domain):
```
https://yourdomain.com/clear-all-cache
```

This will clear:
- Application cache
- Configuration cache
- View cache
- Route cache

#### Option B: Using SSH (If available)
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan permission:cache-reset
```

#### Option C: Manual Cache Deletion (If no SSH)
Delete these files/folders via FTP/File Manager:
1. `bootstrap/cache/config.php`
2. `bootstrap/cache/packages.php`
3. `bootstrap/cache/services.php`
4. `bootstrap/cache/routes-v7.php` (if exists)
5. All files in `storage/framework/cache/data/` (keep the folder structure)
6. All files in `storage/framework/views/` (keep the folder structure)

### Step 3: Verify Database Roles
Ensure all roles in the `roles` table have `guard_name = 'web'`

Run this SQL query to check:
```sql
SELECT name, guard_name FROM roles;
```

If any roles have a different guard (or empty), update them:
```sql
UPDATE roles SET guard_name = 'web' WHERE guard_name IS NULL OR guard_name != 'web';
```

### Step 4: Test the Permissions
After clearing caches, test by:
1. Logging into your application
2. Attempting to access a route protected by role middleware
3. Check if the authorization works correctly

## Common Issues & Solutions

### Issue 1: "401 Unauthorized" or redirected to login
**Solution:** This means the role middleware is working but can't find the role. 
- Clear cache again
- Check that the user has the role assigned in `model_has_roles` table

### Issue 2: Still not working after cache clear
**Solution:** InfinityFree sometimes caches PHP opcache separately
- Wait 5-10 minutes for opcache to expire
- OR restart PHP (if control panel allows)
- OR clear opcache via a PHP script (see below)

### Issue 3: Need to clear OpCache
Create a file `clear-opcache.php` in your public folder:
```php
<?php
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "OPcache cleared successfully!";
    } else {
        echo "Failed to clear OPcache";
    }
} else {
    echo "OPcache is not enabled";
}

// Also clear realpath cache
if (function_exists('clearstatcache')) {
    clearstatcache(true);
    echo "<br>Realpath cache cleared!";
}
?>
```

Visit `https://yourdomain.com/clear-opcache.php` then DELETE this file for security.

## Verification Checklist
- [ ] `config/permission.php` uploaded with guards array
- [ ] Cache cleared (via web route or manual deletion)
- [ ] Roles table has `guard_name = 'web'`
- [ ] OpCache cleared (if needed)
- [ ] Waited 5-10 minutes for server cache to expire
- [ ] Tested role-based routes in browser

## Files Modified
1. `config/permission.php` - Added `guards` array
2. `routes/web.php` - Added `/clear-all-cache` route
3. `routes/web.php` - Fixed middleware syntax to use arrays

## Technical Details
- **Guard Name:** `web` (matches `config/auth.php` default)
- **User Model:** Has `protected $guard_name = 'web'`
- **Middleware:** Properly registered in `app/Http/Kernel.php`
- **Cache Key:** `spatie.permission.cache`

## Still Not Working?
If permissions still don't work after all steps:
1. Check Laravel logs in `storage/logs/laravel.log`
2. Enable error reporting temporarily to see specific errors
3. Verify User model uses `HasRoles` trait
4. Confirm middleware is in correct order in routes

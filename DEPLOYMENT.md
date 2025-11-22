# Deployment Guide for SJDM Laravel Application

## Overview
This deployment is optimized for speed by excluding the `vendor` directory from automated FTP deployment. The vendor directory must be uploaded manually via SSH.

## Initial Setup (One-time)

### 1. Upload Vendor Directory via SSH

Since the vendor directory contains thousands of files and is slow to upload via FTP, upload it once manually:

```bash
# Option A: If you have SSH access to the server
ssh your-username@your-server
cd /home/u252000670/domains/sjdm.store
composer install --no-dev --optimize-autoloader
```

```bash
# Option B: Upload vendor as a zip file via SSH/FTP
# On your local machine:
cd /path/to/your/project
composer install --no-dev --optimize-autoloader
zip -r vendor.zip vendor/

# Upload vendor.zip to server via FTP or SCP
scp vendor.zip your-username@your-server:/home/u252000670/domains/sjdm.store/

# SSH into server and extract
ssh your-username@your-server
cd /home/u252000670/domains/sjdm.store
unzip vendor.zip
rm vendor.zip
```

```bash
# Option C: Using rsync over SSH (fastest)
# From your local machine:
rsync -avz --progress vendor/ your-username@your-server:/home/u252000670/domains/sjdm.store/vendor/
```

### 2. Set Correct Permissions

```bash
# On the server via SSH:
cd /home/u252000670/domains/sjdm.store
chmod -R 755 vendor
chmod -R 775 storage bootstrap/cache
```

## When to Update Vendor Directory

You only need to re-upload the vendor directory when:
- You add a new Composer package
- You update existing packages (composer.lock changes)
- After running `composer update`

To update vendor:
```bash
# Option 1: Run composer on server (recommended)
ssh your-username@your-server
cd /home/u252000670/domains/sjdm.store
composer install --no-dev --optimize-autoloader

# Option 2: Upload from local
# Follow the same steps as initial setup above
```

## Automated Deployment

The GitHub Actions workflow automatically deploys:
- All Laravel application files (app, config, database, routes, etc.)
- Compiled frontend assets (public/build)
- Environment configuration
- Storage and cache directories

**Excluded from deployment:**
- `vendor/` - Upload manually via SSH
- `node_modules/` - Only built assets are deployed
- Development files (tests, README, etc.)

## Deployment Optimizations

The workflow includes these optimizations:
1. **Composer caching** - Dependencies cached between builds
2. **NPM caching** - Node modules cached for faster builds
3. **Minimal FTP logging** - Reduces deployment time
4. **Excluded vendor** - Skips thousands of files
5. **15-minute timeout** - Fast deployment without vendor

## Troubleshooting

### If deployment fails
1. Check GitHub Actions logs for specific errors
2. Verify FTP credentials in GitHub Secrets
3. Ensure server has enough disk space

### If site shows errors after deployment
1. Check vendor directory exists: `ls -la /home/u252000670/domains/sjdm.store/vendor`
2. Clear Laravel cache via setup_storage.php (runs automatically)
3. Check file permissions on storage and bootstrap/cache

### Manual cache clearing
```bash
ssh your-username@your-server
cd /home/u252000670/domains/sjdm.store
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## Server Structure

```
/home/u252000670/domains/sjdm.store/
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/          # Uploaded manually via SSH
├── .env
├── artisan
└── public_html/     # Public web root
    ├── index.php
    ├── build/       # Compiled assets
    └── storage -> ../storage/app/public
```

## Performance Notes

- **Before optimization**: ~25-35 minutes (with vendor via FTP)
- **After optimization**: ~5-10 minutes (without vendor)
- **One-time vendor upload**: ~3-5 minutes via SSH/rsync

## Security Notes

- Never commit `.env` file to repository
- Database credentials stored in GitHub Secrets
- Vendor directory should have 755 permissions
- Storage directories need 775 permissions for web server writes

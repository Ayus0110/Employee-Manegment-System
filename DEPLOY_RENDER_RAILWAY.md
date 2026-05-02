# Deploy EMS on Render with Railway MySQL

This project is prepared to run on:

- Render for the Laravel web app
- Railway for the MySQL database

## 1. Push this project to GitHub

Render and Railway both work best from a GitHub repository.

## 2. Create the Railway MySQL database

On Railway:

1. Create a new project.
2. Add a `MySQL` service.
3. Open the MySQL service variables and copy these values:
   - `MYSQLHOST`
   - `MYSQLPORT`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`
   - `MYSQLDATABASE`

Railway documents these variables in the official MySQL docs:
- [Railway MySQL](https://docs.railway.com/databases/mysql)

## 3. Deploy the app on Render

On Render:

1. Click `New` -> `Web Service`
2. Connect your GitHub repository
3. Render will detect the included `render.yaml`
4. Deploy the service

Render supports Docker-based deploys for PHP apps:
- [Render Docker Docs](https://render.com/docs/docker)
- [Render Blueprint YAML Reference](https://render.com/docs/blueprint-spec)

## 4. Set these Render environment variables

In Render, set:

```env
APP_NAME=EMS
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-render-domain.onrender.com
APP_KEY=base64:GENERATE_THIS_LOCALLY

DB_CONNECTION=mysql
DB_HOST=MYSQLHOST_FROM_RAILWAY
DB_PORT=MYSQLPORT_FROM_RAILWAY
DB_DATABASE=MYSQLDATABASE_FROM_RAILWAY
DB_USERNAME=MYSQLUSER_FROM_RAILWAY
DB_PASSWORD=MYSQLPASSWORD_FROM_RAILWAY

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
MAIL_MAILER=log
LOG_CHANNEL=stack
LOG_LEVEL=info
```

## 5. Generate the APP_KEY

Run this locally inside the project:

```powershell
php artisan key:generate --show
```

Copy the output into Render as `APP_KEY`.

## 6. Database migration

This project is configured to run migrations on Render before deploy:

```text
php artisan migrate --force
```

That is already set in [render.yaml](C:\Users\Victus\laravel\EmployeeManegmentSys\render.yaml).

## 7. Important note about uploads

Your project stores photos and documents in Laravel storage. Render's filesystem is ephemeral by default, so uploaded files will not persist after a redeploy unless you attach a persistent disk.

If you want uploads to stay saved, attach a Render disk and mount it under:

```text
/var/www/html/storage/app/public
```

Render disk docs:
- [Render Persistent Disks](https://render.com/docs/disks)

## 8. Recommended first test after deploy

After the first deployment:

1. Open the Render URL
2. Log in
3. Open user settings
4. Test image upload
5. Test leave, attendance, and salary pages

If uploads disappear after redeploy, add the persistent disk.

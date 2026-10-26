# Phase 9 — Deployment & Go-Live

This is the only phase that's genuinely "run commands on your real server," not
something I can write as portable application code. Everything below is
tailored to decisions already made earlier in this conversion — not a generic
Laravel deployment tutorial.

## 1. Server prerequisites

You already run this stack for Hostorio clients, so this should be familiar:

- PHP 8.3+ with extensions: `pdo_mysql`, `mbstring`, `xml`, `curl`, `zip`, `gd`, `bcmath`
- Composer 2.x
- MySQL 8.x (or MariaDB 10.6+)
- Nginx
- Node.js 20+ (for `npm run build` — only needed at deploy time, not at runtime)
- Supervisor (for the queue worker — see §4)
- Certbot (Let's Encrypt)

## 2. Switch the database from SQLite to MySQL

Every prior phase used SQLite for local dev (`DB_CONNECTION=sqlite`) — zero
setup, fine for development. Production should use MySQL. Create the database
and a dedicated user:

```sql
CREATE DATABASE softorio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'softorio'@'localhost' IDENTIFIED BY 'CHANGE_ME_TO_A_REAL_PASSWORD';
GRANT ALL PRIVILEGES ON softorio.* TO 'softorio'@'localhost';
FLUSH PRIVILEGES;
```

Production `.env` database block:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=softorio
DB_USERNAME=softorio
DB_PASSWORD=CHANGE_ME_TO_A_REAL_PASSWORD
```

## 3. Production `.env` — full checklist

Start from `.env.example`, then change these specifically for production
(everything else in the example file is already sensible defaults):

| Variable | Value | Why |
|---|---|---|
| `APP_ENV` | `production` | Disables debug-mode error pages |
| `APP_DEBUG` | `false` | **Critical** — `true` in production leaks stack traces/env vars to visitors |
| `APP_URL` | `https://softorio.com` | Used for generating absolute URLs (sitemap, emails) |
| `APP_KEY` | *(generate fresh)* | Run `php artisan key:generate` once on the server — never reuse the dev key |
| `DB_CONNECTION` | `mysql` | See §2 |
| `SESSION_DRIVER` | `database` | Already the default — fine as-is |
| `SESSION_SECURE_COOKIE` | `true` | Add this — ensures session cookies only transmit over HTTPS |
| `MAIL_MAILER` | your real SMTP driver or `ses` | `log` (the dev default) writes emails to a file instead of sending them — **must change**, or no one gets contact form emails |
| `MAIL_HOST`/`MAIL_PORT`/`MAIL_USERNAME`/`MAIL_PASSWORD` | your real SMTP credentials | Given your AWS background, SES is the natural choice — see §3a |
| `QUEUE_CONNECTION` | `database` | Already the default — see §4, this needs a worker process to actually send queued mail |
| `ADMIN_NOTIFICATION_EMAIL` | `support@softorio.com` | Already the default (Phase 6) — confirm it's an inbox you actually monitor |

### 3a. If using AWS SES for mail

```
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
```

You'll need to verify `softorio.com` as a sending domain in the SES console
and request production access (SES starts new accounts in a sandbox that only
sends to verified addresses).

## 4. Queue worker (for the contact/affiliate form emails)

Phase 6's Mailables all implement `ShouldQueue` — emails are queued to the
`jobs` table (MySQL) rather than sent synchronously during the HTTP request.
This means a worker process must be running continuously to actually send
them, or queued emails will just sit in the `jobs` table forever.

```bash
sudo cp deploy/supervisor-queue-worker.conf /etc/supervisor/conf.d/softorio-worker.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start softorio-worker:*
```

Verify it's running: `sudo supervisorctl status softorio-worker:*`

**For local dev**, you don't need Supervisor — just run
`php artisan queue:work` in a spare terminal while testing forms, or
temporarily set `QUEUE_CONNECTION=sync` in your local `.env` to send
immediately without a worker.

## 5. Web server

```bash
sudo cp deploy/nginx.conf /etc/nginx/sites-available/softorio.conf
sudo ln -s /etc/nginx/sites-available/softorio.conf /etc/nginx/sites-enabled/
sudo nginx -t   # test config before reloading
```

Edit the `server_name` and `root` paths in that file first if your actual
deploy path isn't `/var/www/softorio`.

## 6. SSL (Let's Encrypt)

```bash
sudo certbot --nginx -d softorio.com -d www.softorio.com
```

Certbot auto-configures the Nginx SSL block and sets up auto-renewal via a
systemd timer (`sudo systemctl status certbot.timer` to confirm it's active).

## 7. First deploy

```bash
git clone <your-repo-url> /var/www/softorio
cd /var/www/softorio
git checkout main   # or whichever branch you're shipping

cp .env.example .env
# edit .env with the production values from §3

composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan key:generate
php artisan migrate --seed --force
php artisan storage:link   # for Filament's uploaded images (portfolio thumbnails, team photos)
php artisan config:cache
php artisan route:cache
php artisan view:cache

sudo systemctl reload nginx
```

Then set up the queue worker (§4) and SSL (§6) if not already done.

## 8. Subsequent deploys

Once the first deploy is live, every later deploy is just:

```bash
cd /var/www/softorio
./deploy/deploy.sh
```

That script (in this repo) handles: maintenance mode, pulling code,
`composer install`, rebuilding frontend assets, migrating, rebuilding all
caches, restarting the queue worker so it picks up new code, and reloading
PHP-FPM.

## 9. DNS cutover

Your static site is presumably live at `softorio.com` right now on different
infrastructure. To cut over without downtime:

1. Get the new Laravel app fully deployed and tested on the new server first (use a staging subdomain or the server's IP + `/etc/hosts` override to test before touching DNS)
2. Lower the DNS TTL on your `A`/`AAAA` records to something short (e.g. 300s) at least 24 hours before cutover, so the eventual change propagates fast
3. Once ready, point the `A` record at the new server's IP
4. Keep the old static site's files on the old server for a few days as a rollback option, in case something's wrong post-cutover
5. Monitor error logs closely for the first 24-48 hours after cutover (§10)

## 10. Post-launch monitoring

Laravel logs to `storage/logs/laravel.log` by default (already configured,
`LOG_CHANNEL=stack`). At minimum:

```bash
tail -f storage/logs/laravel.log
```

right after cutover to catch anything immediately. For anything beyond basic
file logs, adding a real error tracker (Sentry, Bugsnag) is a `composer
require` away when you're ready — not included here since it needs a package
install and an account with that service, both outside what this phase can
set up for you.

---

**Everything in this file is documentation and server config — there's no
application code to test here.** The actual code (queued mail, all 9 prior
phases) is already tested via `php artisan test` from Phase 8. This phase is
complete once you've actually walked through these steps on a real server.

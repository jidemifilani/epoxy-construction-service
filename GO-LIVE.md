# Going live: step by step

Work through the steps in order. Run the pre-launch check (step 10) and fix everything it marks FAIL
before you announce the site. Keep a copy of the local project until the live site has been tested.

---

## 0. What you need

- **Hosting** with PHP 8.2 or newer, MariaDB or MySQL, and support for `.htaccess` files (`mod_rewrite`
  enabled). Shared cPanel hosting works; a VPS works too.
- PHP extensions enabled: `pdo_mysql`, `gd`, `fileinfo`, `mbstring`, `curl`, `openssl`.
- Upload limits of at least 40 MB (for videos). The `upload_max_filesize` and `post_max_size` settings.
- **A way to run commands**: SSH access, or the Terminal in cPanel. The installer and the backup run from the
  command line, not in a browser.
- The domain name, and access to its DNS settings.
- Optional, when ready: an email server for enquiry alerts, WhatsApp Business API credentials, Google
  Analytics, Google Ads, and Meta Pixel IDs, and Search Console and Bing verification tokens.

---

## 1. Decide where the site lives

**Recommended: the domain root** (`https://yourdomain.com/`).

**Or a subfolder** (`https://yourdomain.com/iris-epoxy-service/`). Keep the current settings and only change
the folder name where it appears.

For the domain root, make these changes when you upload (step 4):

| File | Line | Change to |
|---|---|---|
| `config/config.php` | `BASE_URL` | `''` (an empty string) |
| `.htaccess` | `ErrorDocument 404 /iris-epoxy-service/404.php` | `ErrorDocument 404 /404.php` |
| `.htaccess` | `RewriteBase /iris-epoxy-service/` | `RewriteBase /` |
| `.htaccess` | `RedirectMatch 404 ^/iris-epoxy-service/(includes\|config\|storage\|database\|admin/includes)(/\|$)` | `RedirectMatch 404 ^/(includes\|config\|storage\|database\|admin/includes)(/\|$)` |

Then check the homepage, `/shop` and `/admin/` after upload. If anything is missing, the first thing to look at
is this table.

---

## 2. Domain and HTTPS

1. Point the domain's DNS at the host (the host gives you the address to use).
2. Install a free SSL certificate. cPanel calls it **AutoSSL** or **Let's Encrypt**; on a VPS, use Certbot.
3. Make sure `https://yourdomain.com` loads with the padlock showing.
4. Optional but recommended: redirect http to https. Add these lines at the top of `.htaccess`, after
   `RewriteEngine On`:

   ```
   RewriteCond %{HTTPS} off
   RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

---

## 3. Create the database and its user

In cPanel, open **MySQL Databases**:

1. Create a database, for example `youraccount_iris`.
2. Create a database user with a strong, unique password. Do not reuse the root password.
3. Add the user to the database with **All Privileges**.
4. Write down the full database name, the user name, and the password. cPanel adds the account prefix to
   each, so use the full names it shows.

Never use the `root` account on a live server. The pre-launch check will warn you if you do.

---

## 4. Upload the files

Upload the project folder to the web root (`public_html` in cPanel) or the subfolder you chose in step 1.

**Include:** all folders and files, including the hidden `.htaccess` files in `admin/`, `assets/uploads/media/`,
`storage/`, and the project root. Hidden files are often skipped by default; turn on "show hidden files" in
cPanel File Manager, or use SFTP.

**Do not upload:**
- `storage/.first-admin-password.txt`, the local first-admin password file
- `storage/backups/`, the local backup files (the server makes its own)
- `assets/uploads/media/` contents from your local machine, if any test media is there. Upload real media
  through the admin instead.

---

## 5. Set the live configuration

Edit `config/config.php` on the server (cPanel File Manager has an editor):

- `BASE_URL`: `''` for the domain root, or `'/iris-epoxy-service'` for a subfolder.
- `SITE_ORIGIN`: `'https://yourdomain.com'`, with no trailing slash.
- `db` block: the database name, user and password from step 3. Host `127.0.0.1` is usually right on
  cPanel; if the host says otherwise, use the host name from its instructions.
- `whatsapp` block: leave `access_token` and `phone_number_id` empty until the WhatsApp setup is complete.

---

## 6. Create the database tables and the first admin account

From SSH or the cPanel Terminal, in the project folder:

```
php database/install.php --user=admin --password="a-long-passphrase-you-will-remember" --name="Your name"
```

Use the PHP command the host provides if `php` is not the right version. Some hosts call it `php82` or
`/opt/cpanel/ea-php82/root/usr/bin/php`.

The installer does four things: it creates the tables, runs the migrations, fills empty tables with the starter
content, and creates the admin account. Running it again is safe. It never overwrites data that already exists.

**The starter content is sample data.** Six sample reviews, placeholder prices and a placeholder bank account
are included so the site is not empty on day one. You replace them in step 8. The pre-launch check lists each one.

If the installer cannot connect, check the `db` block in `config/config.php`.

---

## 7. Sign in and make the admin yours

1. Open `https://yourdomain.com/admin/` and sign in.
2. Open **My password** and choose a new password. Use a passphrase, and keep it in a password manager.
3. If you created a plain-text password file at any point, delete it.

---

## 8. Replace the sample content

Work through the admin in this order:

1. **Settings > Contact**: the real phone, WhatsApp number, and email addresses. Then **Settings > Enquiry alerts**: your own WhatsApp number for alerts.
2. **Settings > Company**: the real company name, founder, and founding year.
3. **Settings > Numbers shown on the site**: real years, projects completed, warranty years, and review count.
   Only publish numbers you can prove.
4. **Settings > Shop and payment**: the real bank account, the delivery fee, the free-delivery threshold, and the
   order terms.
5. **Settings > Prices shown on the site**: the real price ranges, and set **Prices last checked** to today.
6. **Settings > Search and advertising**: the exact office point from the Google Business Profile map pin.
   Add the verification tokens and analytics IDs when you have them.
7. **Products**: set a real price and stock level for every pack. Untick anything you cannot supply.
8. **Testimonials**: replace the samples with real reviews, or unpublish them. Do not publish made-up
   reviews, and do not add review markup until the reviews are real.
9. **Team and case studies**: the team cards on `/about` and the case studies on `/services/commercial` are
   placeholders. Replace them with real people and real projects, or remove them.
10. **Marketing > Photos & videos**: upload real photos and link real videos. Each photo needs alt text.
11. **Blog articles**: check each article's text is accurate for your business, and that the prices in it match
    Settings.
12. **Privacy policy and terms**: have them reviewed before launch.

---

## 9. Server settings and backups

**PHP settings** (cPanel: **MultiPHP INI Editor**, or `php.ini`):
- `display_errors` **Off**. Otherwise visitors can see PHP warnings and file paths.
- `upload_max_filesize` and `post_max_size` at **40M** or more.

**Scheduled backups** (cPanel: **Cron Jobs**, weekly):

```
/usr/local/bin/php /home/YOURUSER/public_html/tools/backup-db.php
```

Use the real path to PHP and to the project folder. Run the command once by hand first and check that a file
appears in `storage/backups/`.

**Copy the backups off the server** at least monthly: download the latest `.sql` file from `storage/backups/` to
another place. A backup on the same server does not protect against losing the server.

---

## 10. Run the pre-launch check

From the project folder on the server:

```
php tools/preflight.php
```

It checks the addresses, the database, the placeholder details, the security settings, the PHP extensions, the
upload limits, the writable folders, and the backups. Every **FAIL** must be fixed. Read every **WARN** and fix
the ones that apply. **INFO** lines are for your information.

Run it again until there are no FAILs.

---

## 11. Check every link on the live site

```
php tools/check-links.php
```

It uses the live address from `SITE_ORIGIN`. It reports any internal link that does not load. Fix each one
before launch.

---

## 12. Connect search engines and advertising

1. **Google Search Console**: add the domain as a property. Verify it with the token from Settings (or the DNS
   method). Submit `https://yourdomain.com/sitemap.xml`.
2. **Bing Webmaster Tools**: add the site, verify it, and submit the same sitemap.
3. **Google Business Profile**: set the website address to `https://yourdomain.com/`, and check the
   business details match the site.
4. **Analytics and ads**: enter the GA4, Google Ads, and Meta IDs in Settings. Confirm the conversion events
   appear in each platform. The playbook (`SEO-AND-ADS-PLAYBOOK.md`) has the campaign set-up.
5. **WhatsApp alerts** (optional): complete the Meta setup in `SEO-IMPLEMENTATION-STATUS.md`, then add the
   token and phone number ID to `config/config.php`.

---

## 13. Test the live site before you announce it

Use a real phone and a real computer, on mobile data as well as Wi-Fi.

- [ ] The home page, the shop, and a landing page load with the padlock showing.
- [ ] `https://yourdomain.com/admin/` signs in with your new password.
- [ ] A quote request from the website appears in **Leads** in the admin.
- [ ] A WhatsApp alert arrives on your phone for that request (if WhatsApp alerts are set up).
- [ ] An order from the shop appears in **Orders**. To test, order one pack, then set the order to **Cancelled**.
- [ ] The photos and videos load on `/gallery`.
- [ ] The contact links open the phone dialler, WhatsApp, and the email app.
- [ ] `https://yourdomain.com/sitemap.xml` and `https://yourdomain.com/robots.txt` open.
- [ ] A page that does not exist shows the branded 404 page.
- [ ] The site is usable on a phone (the sticky call, WhatsApp, and quote bar appear at the bottom).

---

## 14. After launch

**Weekly:**
- Check **Leads** and **Orders** in the admin, and reply to every new enquiry within one business day.
- Check that the latest backup is recent (`storage/backups/`).
- Reply to Google reviews.

**Monthly:**
- Download a backup off the server.
- Check Search Console for errors and pages that are not indexed.
- Run `php tools/check-links.php` and `php tools/preflight.php` again.
- Update the **Prices last checked** date in Settings after you review the prices.

**If something breaks:** restore the database from the latest backup in `storage/backups/` (import the `.sql` file
in phpMyAdmin or with the `mysql` command), and check the error log in cPanel (**Errors**).

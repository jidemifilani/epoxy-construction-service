# Iris Epoxy Service

Website for Iris Epoxy Service: epoxy flooring for homes, businesses and
designers, plus an online shop selling epoxy by the kilo (retail) and by the
drum (wholesale). Plain PHP 8 with MySQL, no build step, no framework.

- Public site: <http://localhost/iris-epoxy-service/>
- Admin dashboard: <http://localhost/iris-epoxy-service/admin/>

---

## First-time setup (XAMPP)

1. Start **Apache** and **MySQL** in the XAMPP control panel.
2. Install the database and create the first admin account:

   ```
   C:\xampp\php\php.exe database\install.php --user=admin --password="a-strong-passphrase" --name="Site Admin"
   ```

   This creates the `iris_epoxy_service` database, applies `database/schema.sql`,
   copies the starter content (projects, reviews, FAQs, blog plan, sample
   products) into empty tables, and creates the admin account. Re-running it is
   safe: it never overwrites content that already exists.

3. Sign in at `/admin/` and change the placeholder details (see below).

The installer only runs from the command line.

---

## Going live

1. In `config/config.php`, set the `db` block to the live server's MySQL details.
2. Set `BASE_URL` (`''` when the site sits at the domain root) and `SITE_ORIGIN`
   (e.g. `https://irisepoxyservice.com`). If `BASE_URL` changes, update the two
   `/iris-epoxy-service/` references in `.htaccess`.
3. Run `database/install.php` on the live server with a real admin password.
4. Delete `storage/.first-admin-password.txt` if it exists.
5. Sign in and replace every placeholder in **Settings**: company details, phone,
   emails, prices, bank account, and the delivery fee.

---

## Admin dashboard

Sign in at `/admin/`. The sidebar has:

| Section | What you can do |
|---|---|
| **Dashboard** | New orders and enquiries, paid revenue this month, products out of stock |
| **Orders** | Every shop order, filterable by status. Open one to see the lines and customer, change its status, and add an internal note |
| **Products** | Add, edit, price and hide shop products. Untick *In stock* to stop orders on a pack |
| **Leads** | Enquiries from the contact, quote and trade forms, with status and follow-up notes |
| **Projects** | Portfolio entries: the cards on `/portfolio` and the project anchors |
| **Testimonials** | Reviews, grouped by audience |
| **FAQs** | Questions grouped as on the FAQ page. *Show on the home page* picks the four on the home page |
| **Blog articles** | The twelve article briefs and their outlines |
| **Settings** | Company details, numbers shown on the site, prices, delivery fee, bank details, order terms, enquiry alerts |
| **My password** | Change your own password |

Clearing a field in **Settings** removes the override, so the site goes back to
the default in `config/config.php`.

Sign-in locks for 15 minutes after five wrong passwords in one browser session,
and an admin session signs out after eight idle hours. Every change is a POST
with a CSRF token, and delete asks for confirmation.

---

## The shop

- **/shop**: retail packs from 1 kg and wholesale drums, shown on separate sections.
  Products with several pack sizes are one card with a pack dropdown.
- **/cart**: change quantities or remove lines. Prices are always read from the
  database, never from the browser.
- **/checkout**: delivery details. Individuals or businesses (company name is
  required for business orders). Duplicate submissions are throttled, and the
  honeypot and CSRF checks apply.
- **/order-placed**: the order reference and bank transfer details. The
  reference is kept in the session, so no order number appears in a URL.

Delivery is a flat fee from Settings, waived at or above the free-delivery
threshold. Each order copies the product name, pack and price at the time it is
placed, so later price changes never alter a past order.

Payment is by bank transfer and the admin marks orders *Paid* when the money
arrives. Online card payment is not wired in yet.

---

## Layout

```
config/config.php          defaults, database connection, shop terms
database/
  schema.sql               every table
  install.php              CLI installer: creates DB, schema, seed, first admin
includes/
  functions.php            helpers; cfg() reads Settings overrides first
  db.php                   PDO connection and query helpers
  data.php                 public site content, read from the database
  shop.php                 cart, totals, placing orders
  leads.php                enquiry validation, guards, saving
  artwork.php              all illustration (inline SVG)
  components.php           render helpers: cards, steps, accordions, CTAs
  header.php / footer.php  page chrome
admin/
  index.php                dashboard
  list.php, edit.php,      generic list / create / edit / delete for any section
  delete.php
  orders.php, order.php    shop orders
  leads.php, lead.php      enquiries
  settings.php, password.php
  includes/
    admin.php              sign-in, layout, the CRUD engine
    entities.php           the sections: fields, validation, list columns
assets/css/                main.css (site), shop.css, admin.css
assets/js/                 main.js (site), admin.js (confirmations)
storage/                   blocked from the web
```

Adding a field to a section means adding one line to `admin/includes/entities.php`
and one column to `database/schema.sql`. The form, the validation and the list
column all follow from that.

---

## Security notes

- All queries use prepared statements. Table and column names come from the
  entity definitions, never from the request.
- The admin, database and config folders are blocked from direct URLs.
- Passwords are hashed with `password_hash()`. Sessions are regenerated on sign-in.
- The sign-in lock is per browser session, not per account. For a public
  deployment, add a rate limit at the web server or firewall as well.

---

## Before you publish

- [ ] Real company details, phone numbers and email addresses in **Settings**
- [ ] Real prices for every product, and real stock levels
- [ ] Real bank account details and order terms
- [ ] Real project photography (`/portfolio` says where it slots in)
- [ ] Replace the placeholder reviews, the team cards on `/about`, and the case studies
- [ ] Write the twelve blog articles, starting from their outlines
- [ ] Have `/privacy` and `/terms` reviewed by a lawyer
- [ ] Embed a real Google Map on `/contact`
- [ ] Set `'notify' => true` in Settings once a mail server is configured
- [ ] Test an order from start to finish on the live server

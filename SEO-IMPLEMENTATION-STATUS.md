# The 40 recommendations: implementation status

**Done** means it is built into the site and tested here. **Partly** means the site
side is done and something else must still happen. **Needs you** means it lives
in an account, a real-world activity, or content that only you can provide, so it
cannot be completed inside the website.

| # | Recommendation | Status | Where / what |
|---|---|---|---|
| 1 | Claim and complete the Google Business Profile | Needs you | Claim it with the address on your signage. The site's structured data already matches the business details |
| 2 | Use the real address and check the map pin | Needs you | Office coordinates are in Settings (6.4474, 3.4726, approximate). Set the exact point from the pin |
| 3 | Set the service area to the cities you work in | Done | Settings > Location, and the structured data lists the same cities |
| 4 | Choose "Flooring contractor" as the main category | Needs you | Set in the Google Business Profile |
| 5 | Upload 10+ real photos to the profile | Needs you | Photos must come from your real work |
| 6 | Post a weekly update on the profile | Needs you | Ongoing activity |
| 7 | Ask for Google reviews and reply to each | Partly | Post-delivery message with your review link is built into the admin order page. Set the link in Settings > Shop and payment |
| 8 | Never buy or write reviews | Done | The site shows no star rating or review markup until there are real reviews |
| 9 | Verify in Search Console and submit the sitemap | Partly | Sitemap and verification tag are ready. Needs the live domain and your account |
| 10 | Check Search Console monthly | Needs you | Ongoing activity |
| 11 | Set up Bing Places and Bing Webmaster Tools | Partly | Bing verification field is in Settings |
| 12 | Set up Apple Business Connect | Needs you | Apple account |
| 13 | List on Nigerian directories | Needs you | Check each one is active before listing |
| 14 | Keep name, address and phone identical everywhere | Done on the site | The name, address and phone come from one place in Settings |
| 15 | Publish the remaining blog articles | Done | All twelve articles now have text |
| 16 | Answer "how much" with real ranges | Done | Cost guide, with prices from Settings and a "last checked" date |
| 17 | Write an article on when epoxy is the wrong choice | Done | Included in the epoxy vs polished concrete and tile article |
| 18 | Add a contractor checklist | Done | "How to choose an epoxy contractor" article |
| 19 | Create a page per Lagos area you serve | Not done | Area pages should only exist where you have done work there. Tell me the areas and I will build them |
| 20 | Use the terms people search for | Done on key pages | Titles and keywords use "epoxy floor", "garage", "resin" and the city |
| 21 | Put the city and service in the first heading | Done on key pages | Landing pages and the home page |
| 22 | Refresh the cost guide every six months | Partly | The "prices last checked" date is shown. Update the date in Settings when you review the prices |
| 23 | Shoot 30 real photos | Needs you | Use the shot list in SEO-AND-ADS-PLAYBOOK.md |
| 24 | Name files and write alt text | Done | The media form requires alt text |
| 25 | Film the three videos | Needs you | Shot plans are in the playbook. Link them in the admin |
| 26 | Add a video to the cost guide and garage page | Partly | Available in the media library. Add the link once the video exists |
| 27 | Compress photos before upload | Done | Photos are scaled to 1600 px and saved as WebP |
| 28 | Use a real photo as the social share image | Partly | The current share image is a drawn card. Replace it with a photo when you have one |
| 29 | Use HTTPS on the live domain | Needs you | Set `SITE_ORIGIN` to the https address when the domain is live |
| 30 | Test mobile speed on a Nigerian connection | Needs you | Test on a real phone after launch |
| 31 | Run the Google Rich Results Test | Needs you | Run after launch. Structured data is valid in local checks |
| 32 | Fix every 404 | Partly | `tools/check-links.php` checks every internal link: 47 checked, none broken. Google's own 404 report needs Search Console |
| 33 | Keep the sitemap current | Done | Rebuilt from the database on each request |
| 34 | Back up the database weekly | Partly | `tools/backup-db.php` backs up and keeps the newest 14 copies. Schedule it in Windows Task Scheduler (instructions below) |
| 35 | Set real prices and stock levels | Needs you | Products > each pack. Untick out-of-stock packs |
| 36 | Ask customers for a review and feature the best | Partly | Review request on delivered orders. Featuring reviews needs real reviews to exist |
| 37 | One-line "how to use" note on each product | Done | Each product points to its data sheet for coverage and mixing. Add exact figures from the data sheet to each product |
| 38 | Start Google Ads with small budgets | Needs you | Account and budget. The playbook has the structure |
| 39 | Install the GA4, Google Ads and Meta IDs | Partly | Tags are ready and tested. Enter the IDs in Settings once the accounts exist |
| 40 | Review the leads report weekly | Done | Each enquiry shows its campaign in the admin |

## Scheduling the backup

Open Windows Task Scheduler, create a basic task, set it to run weekly, and use:

- **Program:** `C:\xampp\php\php.exe`
- **Arguments:** `tools\backup-db.php`
- **Start in:** `C:\xampp\htdocs\iris-epoxy-service`

Copy the files in `storage\backups\` to another drive or to cloud storage. A backup
on the same disk does not protect against losing that disk.

## Checking the site before launch

- `php tools/check-links.php`: every internal link on every page in the sitemap
- `php tools/backup-db.php`: a fresh database backup
- `php database/migrate.php`: confirms the database is up to date

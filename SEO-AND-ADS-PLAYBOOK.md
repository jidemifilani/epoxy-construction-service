# Iris Epoxy Service: search, local and advert playbook

This is the working plan for getting the site found in Nigeria and running paid
adverts that send enquiries. It covers what is already built in the site, what
you need to set up on the outside (Google, Bing, Meta), and what to produce
(photos, videos, articles).

> The keyword list below is based on search intent for epoxy flooring in
> Nigeria. Search volumes and competition have **not** been checked yet. Before
> you spend money on a keyword, confirm its demand in Google Keyword Planner and
> adjust the list.

---

## 1. What is built into the site

| Area | Done |
|---|---|
| Page titles and descriptions | Set for every page, with the city and Nigeria in the priority ones. Editable per page in **Marketing > SEO by page** |
| Structured data | Local business (with coordinates), website, breadcrumbs, FAQ, product (each shop pack), article, image and video |
| Indexing control | Cart, checkout, order confirmation, thank-you, 404 and admin are `noindex`. Articles without text are `noindex` |
| Sitemap | `/sitemap.xml` built from the live database, with image and video entries |
| robots.txt | Private pages closed; photos and videos open; sitemap declared |
| Social sharing | Open Graph and Twitter tags on every page, with a 1200 x 630 PNG card |
| Local signals | en-NG language tag, Nigeria region and place name, geo coordinates, service-area list |
| Landing pages | Five pages for adverts and focused searches (see section 3) |
| Campaign tracking | UTM parameters and click IDs are saved with each enquiry and order |
| Conversion tracking | Google Analytics 4, Google Ads and Meta Pixel tags, switched on by entering their IDs in **Settings** |
| Conversion events | Lead on enquiry, purchase on order, and contact clicks (call, WhatsApp, email) |
| Media | Photos converted to WebP, lazy-loaded, with alt text. Videos uploaded or linked from YouTube or Vimeo |

**Before you launch, set the following in Settings:**
- The real office latitude and longitude (confirm the map pin on your Google Business Profile)
- The Google Search Console and Bing Webmaster verification tokens
- The GA4, Google Ads and Meta IDs, once the accounts exist

---

## 2. Keyword plan by page

Use the title and first heading on each page for its primary phrase. Use the
secondary phrases in the body, naturally. Do not repeat a phrase in every
paragraph.

| Page | Primary phrase | Secondary phrases |
|---|---|---|
| Home | epoxy flooring Lagos | epoxy flooring Nigeria, epoxy contractor Lagos, metallic epoxy floor Lagos |
| Garage landing | garage epoxy floor Lagos | garage floor coating Lagos, epoxy garage floor Nigeria, flake garage floor Lagos |
| Commercial landing | commercial epoxy flooring Nigeria | warehouse epoxy Lagos, factory floor epoxy Nigeria, epoxy mortar flooring |
| Cost guide | epoxy floor cost Nigeria | how much is epoxy flooring Nigeria, epoxy price per square metre Nigeria |
| Lagos landing | epoxy flooring Lagos | epoxy floor installation Lagos, epoxy flooring Lekki, epoxy flooring Ikeja |
| Shop | buy epoxy resin Nigeria | epoxy for sale Nigeria, epoxy resin price Nigeria, epoxy primer Nigeria, epoxy drum wholesale |
| Buy landing | buy epoxy resin Nigeria | epoxy resin for sale Nigeria, epoxy drum Nigeria, epoxy floor coat Lagos |
| Residential | residential epoxy flooring Lagos | basement epoxy Lagos, patio epoxy Nigeria |
| Decorative | metallic epoxy floor Lagos | 3D epoxy floor Nigeria, marble effect epoxy Nigeria |
| Process | epoxy installation process Nigeria | diamond grinding floor Nigeria, concrete surface preparation |
| Portfolio / gallery | epoxy floor projects Lagos | epoxy floor photos Nigeria, epoxy floor videos |
| Blog | epoxy flooring guides Nigeria | epoxy floor peeling causes, garage floor paint vs epoxy |

**Match intent.** Someone typing "buy epoxy resin Nigeria" wants the shop, not
an essay. Someone typing "how much epoxy flooring Lagos" wants a price range.
Keep each page to one kind of intent.

**Keep it local but true.** Only name areas you actually serve. The service
area in Settings is the list the site uses. Do not add cities you do not work in.

---

## 3. Landing pages for adverts

| Landing page | Use it for |
|---|---|
| `/epoxy-flooring-lagos` | Broad local search and Lagos-wide adverts |
| `/garage-epoxy-floor-lagos` | Homeowner garage campaigns |
| `/commercial-epoxy-flooring-nigeria` | Facility, procurement and industrial campaigns |
| `/epoxy-floor-cost-nigeria` | Price-research searches, and a useful destination for remarketing |
| `/buy-epoxy-resin-nigeria` | Shop campaigns, and bulk or trade enquiries |

Send each advert to the landing page that matches its keyword group. A garage
advert should land on the garage page, not the home page.

---

## 4. Setting up search engines (Nigeria)

1. **Google Search Console.** Add the domain as a property, verify it using the
   token in Settings, and submit `https://your-domain/sitemap.xml`. Check the
   coverage report after two weeks. Fix any page marked "Discovered, not indexed"
   or "Crawled, not indexed" by adding real content, not by resubmitting.
2. **Google Business Profile** (most important for local search). Claim the
   business. Use the address as it appears on your signage. Set the service
   area to Lagos and the South-West cities. Choose the primary category
   "Flooring contractor" (or the closest available) and add "Epoxy flooring
   contractor" as an additional category if offered. Add the phone and website,
   the opening hours, and at least 10 photos. Post an update every week.
3. **Bing Places and Bing Webmaster Tools.** Bing also serves search results in
   Nigeria, so do not skip it. Import from Google Business Profile if offered,
   then verify with the Bing token in Settings.
4. **Apple Business Connect**, for iPhone users searching in Maps and Siri.
5. **Nigerian business directories.** List the business on reputable directories
   that are still active. Check each one before you pay for a listing. The same
   name, address and phone number must appear everywhere (NAP consistency).
6. **Reviews.** Ask every satisfied client to leave a Google review, and reply to
   every review. Do not buy reviews or write them yourself. The site does not show
   a star rating until real reviews exist on the page.
7. **HTTPS.** Set `SITE_ORIGIN` to the `https://` address before launch. Canonical
   links, the sitemap and structured data all use it.

---

## 5. Content to produce

### Photos (target 30 before launch)

Shoot in daylight. Use the same camera position for before and after.

- **Preparation:** diamond grinding in progress (dust visible), crack repair, primer
- **Install:** metallic pour, flake broadcast, the topcoat going on
- **Finished:** garage (wide and close), basement, warehouse bays with line marking,
  showroom with logo floor, kitchen or commercial wash-down area
- **Team and kit:** crew at work with PPE; grinders and equipment
- **Shop:** product packs, drums and a pack on a bench for the shop listing

Name each file descriptively, for example `metallic-epoxy-garage-lekki.jpg`.
Fill in the alt text in the admin. Describe what is in the picture, and add the
place. Do not stuff keywords into alt text.

### Videos (three plans, to shoot and upload)

**Video 1, the process (60 to 90 seconds).** Time-lapse of one garage from
grinding to finished floor, with a voiceover in plain English: "Prep is 70% of
the job. Here is what we do in each step." Upload to YouTube, then link it in
the admin under Photos and videos.

**Video 2, why floors peel (90 seconds).** Show a failed floor, then a diamond
ground surface being checked with a moisture test. Explain the five causes in
the "why epoxy floors peel" article. This is the video most likely to be shared.

**Video 3, a finished-floor walk-through (30 to 45 seconds).** Slow pan across a
finished floor, with the project name, size and system on screen at the start.
Use it as a short advert for remarketing.

Film in landscape at 1080p. Keep sound clean, and add captions. YouTube videos
can be linked in the admin without any file-size limit.

### Articles

Four articles are written. The other eight are briefs that stay `noindex` until
they have text. Write each one in the admin under **Blog articles**, starting
with the outline already in the brief. Each article should:

- answer the title's question in the first two paragraphs
- use the real prices from Settings, so figures match the quotes
- say where epoxy is the wrong answer
- link to the process page and the quote page

---

## 6. Advert setup

### Google Ads (search)

- **Campaign structure:** one campaign per service (garage, commercial, decorative,
  shop). Each has one ad group per keyword group.
- **Match types:** start with phrase match and exact match. Use broad match only
  with a tight negative keyword list.
- **Negative keywords to add at once:** "free", "DIY video", "jobs", "salary",
  "course", "training", "pdf", and any city you do not serve.
- **Landing page:** the matching page from section 3.
- **Conversions:** enter the Google Ads ID and conversion label in Settings. Mark
  "lead" (enquiry sent) and "purchase" as conversions. Add the phone and
  WhatsApp clicks as secondary conversions.
- **Call and location extensions** on every campaign.

### Meta (Facebook and Instagram)

- Enter the Meta Pixel ID in Settings. Check the Events Manager shows
  PageView and Lead.
- Run lead campaigns to the landing pages. Use a photo or video from section 5
  as the creative.
- Build a retargeting audience from visitors to the cost guide and the garage
  page.

### Budgets and measurement

Start small and judge by cost per lead, not by clicks. Check weekly:

| Measure | Where |
|---|---|
| Enquiries by landing page and campaign | Admin > Leads (each one shows its campaign) |
| Orders by campaign | Admin > Orders (the campaign is in the internal note) |
| Phone and WhatsApp clicks | Analytics, event `contact_click` |
| Cost per enquiry | Ads account, using the lead conversion |

If a page gets clicks but no enquiries, the page is failing. Change the headline
and offer before you change the budget.

---

## 7. Launch checklist

- [ ] Office coordinates confirmed in Settings
- [ ] Search Console and Bing verification tokens in Settings
- [ ] `SITE_ORIGIN` set to the https address; `BASE_URL` set for the live path
- [ ] Sitemap submitted in Search Console and Bing Webmaster Tools
- [ ] Google Business Profile claimed, verified and complete
- [ ] At least 10 real photos uploaded with alt text
- [ ] Real prices and stock for every shop product
- [ ] Bank details and order terms correct in Settings
- [ ] Placeholder reviews, team, case studies and the Google Map replaced
- [ ] Privacy policy reviewed (it now describes cookies and tracking)
- [ ] One test order and one test enquiry from an advert link, end to end
- [ ] GA4, Google Ads and Meta IDs entered; conversion events seen in each platform
- [ ] Mobile speed checked on a real Nigerian mobile connection

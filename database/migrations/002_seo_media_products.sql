-- ============================================================================
--  002: SEO fields, media library, article bodies, and more shop products.
--  Applied by database/migrate.php. Each statement is safe to run once; the
--  runner records this file as applied and never runs it twice.
-- ============================================================================

ALTER TABLE articles ADD COLUMN body MEDIUMTEXT NULL AFTER outline;

-- Photos and videos. Uploaded files live under assets/uploads/media/;
-- YouTube or Vimeo videos are stored as a link instead.
CREATE TABLE IF NOT EXISTS media (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kind         VARCHAR(10)  NOT NULL DEFAULT 'image',
  title        VARCHAR(160) NOT NULL,
  alt_text     VARCHAR(255) NOT NULL DEFAULT '',
  caption      VARCHAR(400) NOT NULL DEFAULT '',
  file_path    VARCHAR(255) NULL,
  video_url    VARCHAR(255) NULL,
  thumbnail    VARCHAR(255) NULL,
  mime         VARCHAR(60)  NOT NULL DEFAULT '',
  width        INT UNSIGNED NULL,
  height       INT UNSIGNED NULL,
  bytes        INT UNSIGNED NOT NULL DEFAULT 0,
  attach_to    VARCHAR(20)  NOT NULL DEFAULT 'gallery',
  attach_id    INT UNSIGNED NOT NULL DEFAULT 0,
  sort         INT          NOT NULL DEFAULT 0,
  published    TINYINT(1)   NOT NULL DEFAULT 1,
  created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_attach (attach_to, attach_id, sort)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Per-page title, description and indexing controls. A row here overrides the
-- defaults written in includes/seo.php for that page.
CREATE TABLE IF NOT EXISTS seo_pages (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  path         VARCHAR(190) NOT NULL UNIQUE,
  title        VARCHAR(190) NOT NULL DEFAULT '',
  description  VARCHAR(400) NOT NULL DEFAULT '',
  keywords     VARCHAR(400) NOT NULL DEFAULT '',
  noindex      TINYINT(1)   NOT NULL DEFAULT 0,
  updated_at   TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- New shop products. Each is inserted only if its name and pack are not already
-- present, so re-running is harmless. All prices are placeholders to set in the admin.
INSERT INTO products (name, channel, pack_label, unit_price, min_qty, description, in_stock, sort, published)
SELECT 'Polyaspartic Topcoat (UV-Stable)', 'retail', '5 kg tin', 58000, 1,
       'Fast-cure, UV-stable clear topcoat. Walk on it in hours; ideal for garages in direct sun.', 1, 60, 1
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Polyaspartic Topcoat (UV-Stable)' AND pack_label = '5 kg tin');

INSERT INTO products (name, channel, pack_label, unit_price, min_qty, description, in_stock, sort, published)
SELECT 'Polyaspartic Topcoat (UV-Stable)', 'wholesale', 'Drum (25 kg)', 520000, 1,
       'Bulk fast-cure topcoat for contractors and facilities. Priced per drum.', 1, 61, 1
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Polyaspartic Topcoat (UV-Stable)' AND pack_label = 'Drum (25 kg)');

INSERT INTO products (name, channel, pack_label, unit_price, min_qty, description, in_stock, sort, published)
SELECT 'Self-Levelling Epoxy Floor', 'retail', '5 kg tin', 52000, 1,
       'Smooth, level finish for showrooms and offices. Two-part, solvent-free.', 1, 70, 1
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Self-Levelling Epoxy Floor' AND pack_label = '5 kg tin');

INSERT INTO products (name, channel, pack_label, unit_price, min_qty, description, in_stock, sort, published)
SELECT 'Self-Levelling Epoxy Floor', 'wholesale', 'Drum (200 kg)', 1980000, 1,
       'Bulk self-levelling system for commercial fit-outs. Priced per drum.', 1, 71, 1
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Self-Levelling Epoxy Floor' AND pack_label = 'Drum (200 kg)');

INSERT INTO products (name, channel, pack_label, unit_price, min_qty, description, in_stock, sort, published)
SELECT 'Epoxy Mortar Repair Compound', 'retail', '5 kg tin', 26000, 1,
       'Heavy-duty mortar for chipped edges, joints and loading-bay damage. Mixes to a trowel-able paste.', 1, 80, 1
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Epoxy Mortar Repair Compound' AND pack_label = '5 kg tin');

INSERT INTO products (name, channel, pack_label, unit_price, min_qty, description, in_stock, sort, published)
SELECT 'Epoxy Crack Filler (Flexible)', 'retail', '1 kg tin', 9000, 1,
       'Flexible polymer filler for chasing and sealing hairline and shrinkage cracks before coating.', 1, 90, 1
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Epoxy Crack Filler (Flexible)' AND pack_label = '1 kg tin');

INSERT INTO products (name, channel, pack_label, unit_price, min_qty, description, in_stock, sort, published)
SELECT 'Metallic Epoxy Pigment Kit', 'retail', '500 g pack', 15000, 1,
       'Liquid-metal pigments for metallic and marble-effect floors. Includes one 500 g colour; sold in packs of colour.', 1, 100, 1
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Metallic Epoxy Pigment Kit' AND pack_label = '500 g pack');

INSERT INTO products (name, channel, pack_label, unit_price, min_qty, description, in_stock, sort, published)
SELECT 'Decorative Vinyl Flake Blend', 'retail', '1 kg bag', 12000, 1,
       'Pre-mixed decorative flakes for flake-broadcast garage and basement floors. Slate, domino and shoreline blends.', 1, 110, 1
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Decorative Vinyl Flake Blend' AND pack_label = '1 kg bag');

INSERT INTO products (name, channel, pack_label, unit_price, min_qty, description, in_stock, sort, published)
SELECT 'Epoxy Floor Cleaner & Degreaser', 'retail', '5 litre', 14000, 1,
       'pH-neutral cleaner for everyday maintenance. Safe on sealed epoxy. Do not use abrasives.', 1, 120, 1
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Epoxy Floor Cleaner & Degreaser' AND pack_label = '5 litre');

INSERT INTO products (name, channel, pack_label, unit_price, min_qty, description, in_stock, sort, published)
SELECT 'Epoxy Thinner (Reducer)', 'retail', '5 litre', 16500, 1,
       'Reduces viscosity and cleans tools. Use only with the matching epoxy system.', 1, 130, 1
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name = 'Epoxy Thinner (Reducer)' AND pack_label = '5 litre');

-- Starter article bodies for the four highest-intent articles. The other
-- briefs stay unpublished-to-search (noindex) until they are written.
UPDATE articles SET body = '**Short answer:** in Nigeria a professionally installed epoxy floor usually costs between N480,000 and N1,350,000 for a standard single garage, depending on the condition of the slab, the system chosen and how much preparation is needed.\n\nThe price is set mainly by three things:\n\n- **Preparation.** Diamond grinding, crack repair and moisture testing take the most labour. A cheap quote that skips them is the most common reason a floor peels.\n- **The system.** A flake or solid-colour epoxy costs less than a metallic or 3D finish. A polyaspartic topcoat adds cost but cures much faster.\n- **Area and access.** Per-square-metre rates fall as the area grows, but moving furniture or vehicles, night work and difficult access add to the price.\n\nAsk every contractor for a line-itemed quote covering prep, primer, base coat, decorative layer, topcoat and warranty. If one number is much lower than the rest, ask which of those lines is missing.\n\nFor a site-specific price, book a free inspection. The figures above are indicative ranges, not a fixed price list.'
WHERE slug = 'how-much-does-epoxy-flooring-cost';

UPDATE articles SET body = '**Short answer:** a garage needs an epoxy system, not a garage paint. Paint is a thin film that wears through under tyres and oil within a few years. A properly prepared epoxy is a bonded, thicker coating designed to handle vehicle traffic, oil and hot tyres.\n\n- **Garage paint** is cheap and quick, but usually fails within a few years, especially at the wheel tracks and near the door.\n- **Standard epoxy** is hard, chemical-resistant and easy to clean. It is the most common choice for a garage.\n- **Polyaspartic topcoat** cures faster and resists UV, so it suits a garage that gets direct sun or needs to be used sooner.\n\nIn every case the result depends on preparation. If the concrete is not ground to the correct profile, even a good coating will lift. Ask any contractor how they prepare the surface before you compare prices.'
WHERE slug = 'best-garage-floor-coatings';

UPDATE articles SET body = '**Short answer:** epoxy floors peel because the bond between the coating and the concrete fails. The coating itself is rarely the problem. The usual causes are:\n\n1. **Skipped or wrong preparation.** Acid-etching or light sanding does not open the surface enough. Diamond grinding to the correct profile does.\n2. **Moisture in the slab.** Water rising through concrete pushes the coating off. A moisture test before work starts is essential.\n3. **Contamination.** Oil, dust, curing compound or old sealers left on the surface stop the epoxy bonding.\n4. **Wrong product for the environment.** A coating that is not rated for the chemicals, heat or traffic it faces will fail early.\n5. **Coating over a coating.** Applying new epoxy over a failing old layer without removing it simply moves the failure up.\n\nWhen you choose a contractor, ask how they test moisture, how they prepare the surface and what the written warranty covers.'
WHERE slug = 'why-epoxy-floors-peel';

UPDATE articles SET body = '**Use this checklist before you sign.**\n\n1. **How do you prepare the surface?** The answer should include diamond grinding to a stated profile. "We clean and prime it" is not enough.\n2. **Do you test the slab for moisture?** Ask for the test and the result.\n3. **Who will be on site?** Ask whether the crew who quote are the crew who install, and whether any work is subcontracted.\n4. **Can you show finished floors?** Ask for recent projects in a similar space and, if possible, to visit one.\n5. **Is the quote line-itemed?** Prep, primer, base coat, decorative layer, topcoat, cleanup and warranty should each be priced.\n6. **What is the warranty, and is it in writing?** Check what it covers, for how long, and what it excludes.\n7. **What products do you use?** Ask for the manufacturer and the product data sheet.\n8. **What happens if something goes wrong?** Ask who you contact and how quickly they respond.\n9. **Do you have insurance and a licence?** Ask for copies. A reputable contractor will not mind.\n\nA contractor who answers these clearly is usually worth the higher price.'
WHERE slug = 'how-to-choose-an-epoxy-contractor';

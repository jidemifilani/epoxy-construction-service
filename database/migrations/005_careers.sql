-- ============================================================================
--  005: Careers. Job postings, applications, and the seven roles asked for at
--  launch. Applied once by database/migrate.php.
-- ============================================================================

CREATE TABLE IF NOT EXISTS jobs (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug            VARCHAR(120) NOT NULL UNIQUE,
  title           VARCHAR(160) NOT NULL,
  department      VARCHAR(80)  NOT NULL DEFAULT '',
  location        VARCHAR(160) NOT NULL DEFAULT '',
  employment_type VARCHAR(40)  NOT NULL DEFAULT 'Full-time',
  salary_range    VARCHAR(120) NOT NULL DEFAULT '',
  summary         TEXT         NOT NULL,
  responsibilities TEXT        NULL,
  requirements    TEXT         NULL,
  status          VARCHAR(10)  NOT NULL DEFAULT 'open',
  sort            INT          NOT NULL DEFAULT 0,
  published       TINYINT(1)   NOT NULL DEFAULT 1,
  created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_status (status, published, sort)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- The job title is copied onto the application, so it still makes sense even
-- if the posting is later edited or removed.
CREATE TABLE IF NOT EXISTS job_applications (
  id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  job_id              INT UNSIGNED NULL,
  job_title           VARCHAR(160) NOT NULL,
  applicant_name      VARCHAR(160) NOT NULL,
  email               VARCHAR(160) NOT NULL,
  phone               VARCHAR(40)  NOT NULL,
  cover_note          TEXT         NULL,
  resume_path         VARCHAR(255) NOT NULL,
  resume_original_name VARCHAR(255) NOT NULL DEFAULT '',
  resume_mime         VARCHAR(100) NOT NULL DEFAULT '',
  status              VARCHAR(20)  NOT NULL DEFAULT 'new',
  admin_note          TEXT         NULL,
  ip_address          VARCHAR(45)  NOT NULL DEFAULT '',
  created_at          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_status (status, created_at),
  INDEX idx_job (job_id),
  CONSTRAINT fk_application_job FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- The seven roles. Each is inserted only if its slug is not already present,
-- so re-running this file, or editing a job afterwards, never overwrites it.
INSERT INTO jobs (slug, title, department, location, employment_type, summary, responsibilities, requirements, sort)
SELECT 'site-supervisor', 'Site Supervisor', 'Operations', 'Lagos (site-based, travel within the service area)', 'Full-time',
'Leads the crew on site from set-up through handover, making sure every stage follows the company''s preparation and quality standards.',
'Plan and run the daily site schedule
Supervise the installation crew and any subcontracted trades on site
Check that grinding and surface preparation meet the required profile before coating starts
Keep the client informed of progress and any changes
Enforce safety and PPE rules on site
Sign off each stage before the next one begins
Report progress and issues to the project manager',
'Experience supervising a flooring, construction or similar site crew
Working knowledge of concrete preparation and epoxy or resin flooring systems is an advantage
Able to read a project programme and keep it on track
Clear communication with clients and crew
A valid means of transport between sites within Lagos',
10
WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE slug = 'site-supervisor');

INSERT INTO jobs (slug, title, department, location, employment_type, summary, responsibilities, requirements, sort)
SELECT 'epoxy-floor-installer', 'Epoxy Floor Installer', 'Installation', 'Lagos (site-based)', 'Full-time',
'Installs epoxy flooring systems on residential, commercial and industrial sites, from surface preparation through to the final topcoat.',
'Operate diamond grinders and prep equipment safely
Mix and apply primer, base coat and topcoat to specification
Handle decorative layers such as flake broadcast under supervision
Follow the method statement and manufacturer instructions for each system
Keep the work area clean and materials used efficiently
Work safely with PPE and follow site safety rules',
'Experience installing epoxy, polyurethane or similar resin flooring is preferred
Comfortable with physical site work, including standing, kneeling and lifting
Attention to detail and a steady hand for finish work
Willingness to work some night or weekend shifts on commercial projects
A team player who takes instruction from the site supervisor',
20
WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE slug = 'epoxy-floor-installer');

INSERT INTO jobs (slug, title, department, location, employment_type, summary, responsibilities, requirements, sort)
SELECT 'epoxy-mortar-flooring-expert', 'Epoxy Mortar Flooring Expert', 'Installation', 'Lagos (site-based)', 'Full-time',
'Specialises in epoxy mortar and screed systems for heavy-duty industrial and commercial floors, including loading bays and high-traffic areas.',
'Mix and lay epoxy mortar screeds to the specified thickness and fall
Install coved skirtings where required
Repair damaged or spalled concrete before the overlay goes down
Work to the tolerances needed for heavy forklift and vehicle traffic
Coordinate with the site supervisor on sequencing in live facilities',
'Hands-on experience with epoxy mortar, screed or similar trowel-applied systems
Understanding of substrate preparation for heavy-duty floors
Able to work accurately to level and fall
Experience on industrial or warehouse sites is an advantage',
30
WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE slug = 'epoxy-mortar-flooring-expert');

INSERT INTO jobs (slug, title, department, location, employment_type, summary, responsibilities, requirements, sort)
SELECT 'epoxy-top-coating-expert', 'Epoxy Top Coating Expert', 'Installation', 'Lagos (site-based)', 'Full-time',
'Applies the final sealed topcoat, including UV-stable, anti-slip and antimicrobial finishes, to the standard every job is handed over on.',
'Apply clear and pigmented topcoats to a consistent, defect-free finish
Select and apply the right topcoat for the environment: UV-stable, anti-slip or antimicrobial
Check gloss level and coverage match the specification before sign-off
Spot and correct issues such as bubbling or uneven cure
Keep application tools and equipment in good condition',
'Experience applying epoxy, polyurethane or polyaspartic topcoats
A good eye for a level, consistent finish
Understanding of cure times and the environmental conditions that affect them
Comfortable working to a tight finish-quality standard',
40
WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE slug = 'epoxy-top-coating-expert');

INSERT INTO jobs (slug, title, department, location, employment_type, summary, responsibilities, requirements, sort)
SELECT 'priming-expert', 'Priming Expert', 'Installation', 'Lagos (site-based)', 'Full-time',
'Responsible for primer selection and application, the step that determines whether every later layer bonds properly.',
'Assess the prepared concrete surface and confirm it is ready for primer
Select and mix the correct primer for the substrate and moisture condition
Apply primer evenly at the correct coverage rate
Carry out or support moisture testing before priming
Flag any surface issue to the site supervisor before coating continues',
'Experience with concrete surface preparation and priming on flooring or construction sites
Understanding of moisture testing and why it matters for epoxy adhesion
A careful, methodical approach to following product data sheets
Experience with diamond grinding equipment is an advantage',
50
WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE slug = 'priming-expert');

INSERT INTO jobs (slug, title, department, location, employment_type, summary, responsibilities, requirements, sort)
SELECT 'epoxy-chemical-mixer', 'Epoxy Chemical Mixer', 'Installation', 'Lagos (site-based)', 'Full-time',
'Mixes epoxy resins, hardeners, pigments and aggregates to the exact ratios each system requires, on site and ahead of application.',
'Measure and mix two-part epoxy systems to the manufacturer ratio
Incorporate pigments, flake or aggregate as specified for each job
Manage pot life so mixed material is used before it goes off
Store and handle chemicals safely, following the safety data sheets
Keep mixing equipment clean and well maintained
Report low stock or damaged materials to the site supervisor',
'Experience mixing two-part resins, paints or similar chemical systems
Careful attention to ratios, timing and labelling
Understanding of safe handling of flooring chemicals
Comfortable working with PPE, including gloves and respiratory protection where required',
60
WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE slug = 'epoxy-chemical-mixer');

INSERT INTO jobs (slug, title, department, location, employment_type, summary, responsibilities, requirements, sort)
SELECT 'procurement-executive', 'Procurement Executive', 'Procurement', 'Lagos (office and site)', 'Full-time',
'Sources and purchases epoxy materials, tools and site consumables, keeping projects supplied without overstocking.',
'Source epoxy resins, primers, aggregates and tools from approved suppliers
Obtain and compare quotes, and negotiate pricing and lead times
Raise purchase orders and track deliveries against the project schedule
Monitor stock levels for the shop and for active sites
Maintain supplier records and product data sheets
Flag price changes or supply issues to management early',
'Experience in procurement or purchasing, ideally in construction, flooring or a related trade
Good negotiation skills and attention to commercial detail
Comfortable using spreadsheets to track orders and stock
Organised, with the ability to manage multiple suppliers and deadlines',
70
WHERE NOT EXISTS (SELECT 1 FROM jobs WHERE slug = 'procurement-executive');

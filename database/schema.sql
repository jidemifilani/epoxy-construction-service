-- ============================================================================
--  Iris Epoxy Service - database schema
--  Run through database/install.php, which creates the database, applies this
--  file and seeds the starter content. Safe to re-run: every table uses IF NOT
--  EXISTS and install.php only seeds tables that are empty.
-- ============================================================================

CREATE TABLE IF NOT EXISTS settings (
  `key`   VARCHAR(64)  NOT NULL PRIMARY KEY,
  `value` TEXT         NULL,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admins (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(64)  NOT NULL UNIQUE,
  display_name  VARCHAR(120) NOT NULL DEFAULT '',
  password_hash VARCHAR(255) NOT NULL,
  last_login_at DATETIME     NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS projects (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug       VARCHAR(120) NOT NULL UNIQUE,
  name       VARCHAR(160) NOT NULL,
  location   VARCHAR(160) NOT NULL DEFAULT '',
  tag        VARCHAR(20)  NOT NULL DEFAULT 'residential',
  space      VARCHAR(160) NOT NULL DEFAULT '',
  size       VARCHAR(40)  NOT NULL DEFAULT '',
  system     VARCHAR(200) NOT NULL DEFAULT '',
  finish     VARCHAR(200) NOT NULL DEFAULT '',
  timeline   VARCHAR(80)  NOT NULL DEFAULT '',
  art        VARCHAR(30)  NOT NULL DEFAULT 'metallic',
  summary    TEXT         NULL,
  sort       INT          NOT NULL DEFAULT 0,
  published  TINYINT(1)   NOT NULL DEFAULT 1,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS testimonials (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  quote      TEXT         NOT NULL,
  name       VARCHAR(120) NOT NULL,
  role       VARCHAR(160) NOT NULL DEFAULT '',
  group_key  VARCHAR(20)  NOT NULL DEFAULT 'homeowners',
  project    VARCHAR(200) NOT NULL DEFAULT '',
  sort       INT          NOT NULL DEFAULT 0,
  published  TINYINT(1)   NOT NULL DEFAULT 1,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS faqs (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  group_name   VARCHAR(120) NOT NULL DEFAULT 'General',
  group_sort   INT          NOT NULL DEFAULT 0,
  question     VARCHAR(255) NOT NULL,
  answer       TEXT         NOT NULL,
  show_on_home TINYINT(1)   NOT NULL DEFAULT 0,
  sort         INT          NOT NULL DEFAULT 0,
  published    TINYINT(1)   NOT NULL DEFAULT 1,
  created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS articles (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug       VARCHAR(120) NOT NULL UNIQUE,
  title      VARCHAR(255) NOT NULL,
  category   VARCHAR(60)  NOT NULL DEFAULT '',
  purpose    TEXT         NULL,
  outline    TEXT         NULL,
  sort       INT          NOT NULL DEFAULT 0,
  published  TINYINT(1)   NOT NULL DEFAULT 1,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- One row per pack size. Retail packs start at 1 kg; wholesale is sold in drums.
CREATE TABLE IF NOT EXISTS products (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(160) NOT NULL,
  channel     VARCHAR(20)  NOT NULL DEFAULT 'retail',
  pack_label  VARCHAR(80)  NOT NULL,
  unit_price  DECIMAL(12,2) NOT NULL DEFAULT 0,
  min_qty     INT UNSIGNED NOT NULL DEFAULT 1,
  description TEXT         NULL,
  in_stock    TINYINT(1)   NOT NULL DEFAULT 1,
  sort        INT          NOT NULL DEFAULT 0,
  published   TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_channel (channel, published, sort)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reference       VARCHAR(20)  NOT NULL UNIQUE,
  channel         VARCHAR(20)  NOT NULL DEFAULT 'retail',
  buyer_type      VARCHAR(20)  NOT NULL DEFAULT 'individual',
  company_name    VARCHAR(160) NOT NULL DEFAULT '',
  customer_name   VARCHAR(120) NOT NULL,
  email           VARCHAR(160) NOT NULL,
  phone           VARCHAR(40)  NOT NULL,
  address         VARCHAR(255) NOT NULL,
  city            VARCHAR(120) NOT NULL DEFAULT '',
  notes           TEXT         NULL,
  subtotal        DECIMAL(12,2) NOT NULL DEFAULT 0,
  delivery_fee    DECIMAL(12,2) NOT NULL DEFAULT 0,
  total           DECIMAL(12,2) NOT NULL DEFAULT 0,
  status          VARCHAR(20)  NOT NULL DEFAULT 'new',
  admin_note      TEXT         NULL,
  ip_address      VARCHAR(45)  NOT NULL DEFAULT '',
  created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product name, pack and price are copied onto the line at the moment of
-- ordering, so later price changes never rewrite a past order.
CREATE TABLE IF NOT EXISTS order_items (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id      INT UNSIGNED NOT NULL,
  product_id    INT UNSIGNED NULL,
  product_name  VARCHAR(160) NOT NULL,
  pack_label    VARCHAR(80)  NOT NULL,
  unit_price    DECIMAL(12,2) NOT NULL,
  qty           INT UNSIGNED NOT NULL,
  line_total    DECIMAL(12,2) NOT NULL,
  INDEX idx_order (order_id),
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leads (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  form       VARCHAR(20)  NOT NULL,
  name       VARCHAR(160) NOT NULL DEFAULT '',
  email      VARCHAR(160) NOT NULL DEFAULT '',
  phone      VARCHAR(40)  NOT NULL DEFAULT '',
  location   VARCHAR(160) NOT NULL DEFAULT '',
  details    TEXT         NULL,
  status     VARCHAR(20)  NOT NULL DEFAULT 'new',
  admin_note TEXT         NULL,
  ip_address VARCHAR(45)  NOT NULL DEFAULT '',
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_status (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

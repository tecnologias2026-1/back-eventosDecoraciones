-- ============================================================
-- Eventos & Decoraciones — Schema
-- Run: psql $DATABASE_URL -f database/schema.sql
-- ============================================================

CREATE TABLE IF NOT EXISTS users (
  id            SERIAL PRIMARY KEY,
  name          VARCHAR(120) NOT NULL,
  email         VARCHAR(255) UNIQUE NOT NULL,
  password_hash TEXT NOT NULL,
  role          VARCHAR(10) NOT NULL DEFAULT 'client' CHECK (role IN ('admin','client')),
  created_at    TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS venues (
  id              SERIAL PRIMARY KEY,
  slug            VARCHAR(60) UNIQUE NOT NULL,
  name            VARCHAR(120) NOT NULL,
  description     TEXT,
  base_price      INTEGER NOT NULL,
  base_guests     INTEGER NOT NULL,
  location_label  VARCHAR(200),
  address         VARCHAR(255),
  map_lat         DOUBLE PRECISION,
  map_lng         DOUBLE PRECISION,
  video_url       TEXT,
  featured_image  TEXT,
  is_recommended  BOOLEAN DEFAULT FALSE,
  is_active       BOOLEAN DEFAULT TRUE,
  created_at      TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS venue_features (
  id            SERIAL PRIMARY KEY,
  venue_id      INTEGER NOT NULL REFERENCES venues(id) ON DELETE CASCADE,
  title         VARCHAR(100),
  image_url     TEXT,
  display_order INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS venue_includes (
  id            SERIAL PRIMARY KEY,
  venue_id      INTEGER NOT NULL REFERENCES venues(id) ON DELETE CASCADE,
  description   TEXT,
  display_order INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS services (
  id            SERIAL PRIMARY KEY,
  slug          VARCHAR(80) UNIQUE NOT NULL,
  category      VARCHAR(20) NOT NULL CHECK (category IN ('ceremony','reception','food','others')),
  name          VARCHAR(150) NOT NULL,
  price         INTEGER NOT NULL,
  price_unit    VARCHAR(20) NOT NULL DEFAULT 'fixed' CHECK (price_unit IN ('fixed','per_person','per_set')),
  base_capacity INTEGER,
  description   TEXT,
  image_url     TEXT,
  is_active     BOOLEAN DEFAULT TRUE,
  display_order INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS service_features (
  id            SERIAL PRIMARY KEY,
  service_id    INTEGER NOT NULL REFERENCES services(id) ON DELETE CASCADE,
  feature_text  VARCHAR(200),
  display_order INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS weddings (
  id            SERIAL PRIMARY KEY,
  bride_name    VARCHAR(80),
  groom_name    VARCHAR(80),
  wedding_date  DATE,
  venue_id      INTEGER REFERENCES venues(id) ON DELETE SET NULL,
  banner_image  TEXT,
  review_text   TEXT,
  is_featured   BOOLEAN DEFAULT FALSE,
  display_order INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS wedding_features (
  id            SERIAL PRIMARY KEY,
  wedding_id    INTEGER NOT NULL REFERENCES weddings(id) ON DELETE CASCADE,
  title         VARCHAR(100),
  description   TEXT,
  image_url     TEXT,
  display_order INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS reservations (
  id                   SERIAL PRIMARY KEY,
  code                 VARCHAR(20) UNIQUE NOT NULL,
  user_id              INTEGER REFERENCES users(id) ON DELETE SET NULL,
  groom_name           VARCHAR(100),
  bride_name           VARCHAR(100),
  email                VARCHAR(255) NOT NULL,
  phone                VARCHAR(30),
  venue_id             INTEGER NOT NULL REFERENCES venues(id),
  wedding_date         DATE NOT NULL,
  guest_count          INTEGER NOT NULL,
  special_requirements TEXT,
  deposit_amount       INTEGER,
  total_price          INTEGER,
  status               VARCHAR(15) NOT NULL DEFAULT 'pending' CHECK (status IN ('pending','confirmed','cancelled','completed')),
  created_at           TIMESTAMPTZ DEFAULT NOW(),
  updated_at           TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS reservation_items (
  id             SERIAL PRIMARY KEY,
  reservation_id INTEGER NOT NULL REFERENCES reservations(id) ON DELETE CASCADE,
  service_id     INTEGER REFERENCES services(id) ON DELETE SET NULL,
  item_type      VARCHAR(20) NOT NULL CHECK (item_type IN ('venue','ceremony','reception','food','others')),
  item_name      VARCHAR(150) NOT NULL,
  quantity       INTEGER NOT NULL DEFAULT 1,
  unit_price     INTEGER NOT NULL,
  total_price    INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS calendar_blocks (
  id           SERIAL PRIMARY KEY,
  venue_id     INTEGER NOT NULL REFERENCES venues(id) ON DELETE CASCADE,
  blocked_date DATE NOT NULL,
  reason       VARCHAR(200)
);

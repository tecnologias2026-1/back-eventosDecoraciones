-- Add 'completed' to the reservations.status CHECK constraint
-- Run once against the live DB: psql $DATABASE_URL -f database/migrate_status_completed.sql

ALTER TABLE reservations
    DROP CONSTRAINT IF EXISTS reservations_status_check;

ALTER TABLE reservations
    ADD CONSTRAINT reservations_status_check
    CHECK (status IN ('pending', 'confirmed', 'cancelled', 'completed'));

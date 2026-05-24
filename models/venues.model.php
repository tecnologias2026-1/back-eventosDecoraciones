<?php
require_once __DIR__ . '/../database/connection.php';
global $pdo;

function getVenuesModel(?int $minGuests = null): array {
    global $pdo;
    $sql    = "SELECT * FROM venues WHERE is_active = TRUE";
    $params = [];
    if ($minGuests !== null) {
        $sql .= " AND base_guests >= :guests";
        $params[':guests'] = $minGuests;
    }
    $sql .= " ORDER BY is_recommended DESC, id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getVenueBySlugModel(string $slug): ?array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM venues WHERE slug = :slug AND is_active = TRUE");
    $stmt->execute([':slug' => $slug]);
    $venue = $stmt->fetch();
    if (!$venue) return null;

    $stmt = $pdo->prepare("SELECT * FROM venue_features WHERE venue_id = :id ORDER BY display_order");
    $stmt->execute([':id' => $venue['id']]);
    $venue['features'] = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT * FROM venue_includes WHERE venue_id = :id ORDER BY display_order");
    $stmt->execute([':id' => $venue['id']]);
    $venue['includes'] = $stmt->fetchAll();

    return $venue;
}

function getVenueAvailabilityModel(string $slug): ?array {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM venues WHERE slug = :slug");
    $stmt->execute([':slug' => $slug]);
    $venue = $stmt->fetch();
    if (!$venue) return null;

    $stmt = $pdo->prepare(
        "SELECT blocked_date, reason FROM calendar_blocks WHERE venue_id = :id ORDER BY blocked_date"
    );
    $stmt->execute([':id' => $venue['id']]);
    return $stmt->fetchAll();
}

function createVenueModel(array $d): int {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO venues
          (slug, name, description, base_price, base_guests, location_label, address,
           map_lat, map_lng, video_url, featured_image, is_recommended, is_active)
        VALUES
          (:slug, :name, :description, :base_price, :base_guests, :location_label, :address,
           :map_lat, :map_lng, :video_url, :featured_image, :is_recommended, :is_active)
        RETURNING id
    ");
    $stmt->execute([
        ':slug'           => $d['slug'],
        ':name'           => $d['name'],
        ':description'    => $d['description']    ?? null,
        ':base_price'     => (int) ($d['base_price']  ?? 0),
        ':base_guests'    => (int) ($d['base_guests'] ?? 0),
        ':location_label' => $d['location_label'] ?? null,
        ':address'        => $d['address']        ?? null,
        ':map_lat'        => $d['map_lat']        ?? null,
        ':map_lng'        => $d['map_lng']        ?? null,
        ':video_url'      => $d['video_url']      ?? null,
        ':featured_image' => $d['featured_image'] ?? null,
        ':is_recommended' => ($d['is_recommended'] ?? false) ? 'true' : 'false',
        ':is_active'      => ($d['is_active']      ?? true)  ? 'true' : 'false',
    ]);
    return (int) $stmt->fetchColumn();
}

function updateVenueModel(int $id, array $d): int {
    global $pdo;
    $stmt = $pdo->prepare("
        UPDATE venues SET
          slug=:slug, name=:name, description=:description, base_price=:base_price,
          base_guests=:base_guests, location_label=:location_label, address=:address,
          map_lat=:map_lat, map_lng=:map_lng, video_url=:video_url,
          featured_image=:featured_image, is_recommended=:is_recommended, is_active=:is_active
        WHERE id=:id
    ");
    $stmt->execute([
        ':slug'           => $d['slug'],
        ':name'           => $d['name'],
        ':description'    => $d['description']    ?? null,
        ':base_price'     => (int) ($d['base_price']  ?? 0),
        ':base_guests'    => (int) ($d['base_guests'] ?? 0),
        ':location_label' => $d['location_label'] ?? null,
        ':address'        => $d['address']        ?? null,
        ':map_lat'        => $d['map_lat']        ?? null,
        ':map_lng'        => $d['map_lng']        ?? null,
        ':video_url'      => $d['video_url']      ?? null,
        ':featured_image' => $d['featured_image'] ?? null,
        ':is_recommended' => ($d['is_recommended'] ?? false) ? 'true' : 'false',
        ':is_active'      => ($d['is_active']      ?? true)  ? 'true' : 'false',
        ':id'             => $id,
    ]);
    return $stmt->rowCount();
}

function deleteVenueModel(int $id): int {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM venues WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}

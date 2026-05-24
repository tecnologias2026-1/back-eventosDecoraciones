<?php
require_once __DIR__ . '/../database/connection.php';
global $pdo;

function getWeddingsModel(): array {
    global $pdo;
    $stmt = $pdo->query("
        SELECT w.*, v.name AS venue_name, v.slug AS venue_slug,
               COALESCE(json_agg(wf ORDER BY wf.display_order) FILTER (WHERE wf.id IS NOT NULL), '[]') AS features
        FROM weddings w
        LEFT JOIN venues v   ON v.id = w.venue_id
        LEFT JOIN wedding_features wf ON wf.wedding_id = w.id
        GROUP BY w.id, v.name, v.slug
        ORDER BY w.display_order ASC, w.id ASC
    ");
    $rows = $stmt->fetchAll();
    foreach ($rows as &$row) {
        $row['features'] = json_decode($row['features'], true);
    }
    return $rows;
}

function getWeddingByIdModel(int $id): ?array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT w.*, v.name AS venue_name, v.slug AS venue_slug,
               COALESCE(json_agg(wf ORDER BY wf.display_order) FILTER (WHERE wf.id IS NOT NULL), '[]') AS features
        FROM weddings w
        LEFT JOIN venues v   ON v.id = w.venue_id
        LEFT JOIN wedding_features wf ON wf.wedding_id = w.id
        WHERE w.id = :id
        GROUP BY w.id, v.name, v.slug
    ");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (!$row) return null;
    $row['features'] = json_decode($row['features'], true);
    return $row;
}

function createWeddingModel(array $d): int {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO weddings (bride_name, groom_name, wedding_date, venue_id, banner_image, review_text, is_featured, display_order)
        VALUES (:bride_name, :groom_name, :wedding_date, :venue_id, :banner_image, :review_text, :is_featured, :display_order)
        RETURNING id
    ");
    $stmt->execute([
        ':bride_name'     => $d['bride_name']     ?? null,
        ':groom_name'     => $d['groom_name']     ?? null,
        ':wedding_date'   => $d['wedding_date']   ?? null,
        ':venue_id'       => $d['venue_id']       ?? null,
        ':banner_image'   => $d['banner_image']   ?? null,
        ':review_text'    => $d['review_text']    ?? null,
        ':is_featured'    => ($d['is_featured'] ?? false) ? 'true' : 'false',
        ':display_order'  => (int) ($d['display_order'] ?? 0),
    ]);
    return (int) $stmt->fetchColumn();
}

function updateWeddingModel(int $id, array $d): int {
    global $pdo;
    $stmt = $pdo->prepare("
        UPDATE weddings SET
          bride_name=:bride_name, groom_name=:groom_name, wedding_date=:wedding_date,
          venue_id=:venue_id, banner_image=:banner_image, review_text=:review_text,
          is_featured=:is_featured, display_order=:display_order
        WHERE id=:id
    ");
    $stmt->execute([
        ':bride_name'    => $d['bride_name']    ?? null,
        ':groom_name'    => $d['groom_name']    ?? null,
        ':wedding_date'  => $d['wedding_date']  ?? null,
        ':venue_id'      => $d['venue_id']      ?? null,
        ':banner_image'  => $d['banner_image']  ?? null,
        ':review_text'   => $d['review_text']   ?? null,
        ':is_featured'   => ($d['is_featured'] ?? false) ? 'true' : 'false',
        ':display_order' => (int) ($d['display_order'] ?? 0),
        ':id'            => $id,
    ]);
    return $stmt->rowCount();
}

function deleteWeddingModel(int $id): int {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM weddings WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}

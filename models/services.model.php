<?php
require_once __DIR__ . '/../database/connection.php';
global $pdo;

function getServicesModel(?string $category = null): array {
    global $pdo;
    $sql    = "SELECT s.*, COALESCE(json_agg(sf ORDER BY sf.display_order) FILTER (WHERE sf.id IS NOT NULL), '[]') AS features
               FROM services s
               LEFT JOIN service_features sf ON sf.service_id = s.id
               WHERE s.is_active = TRUE";
    $params = [];
    if ($category !== null) {
        $sql .= " AND s.category = :category";
        $params[':category'] = $category;
    }
    $sql .= " GROUP BY s.id ORDER BY s.display_order ASC, s.id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$row) {
        $row['features'] = json_decode($row['features'], true);
    }
    return $rows;
}

function getServiceBySlugModel(string $slug): ?array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT s.*, COALESCE(json_agg(sf ORDER BY sf.display_order) FILTER (WHERE sf.id IS NOT NULL), '[]') AS features
        FROM services s
        LEFT JOIN service_features sf ON sf.service_id = s.id
        WHERE s.slug = :slug AND s.is_active = TRUE
        GROUP BY s.id
    ");
    $stmt->execute([':slug' => $slug]);
    $row = $stmt->fetch();
    if (!$row) return null;
    $row['features'] = json_decode($row['features'], true);
    return $row;
}

function createServiceModel(array $d): int {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO services
          (slug, category, name, price, price_unit, base_capacity, description, image_url, is_active, display_order)
        VALUES
          (:slug, :category, :name, :price, :price_unit, :base_capacity, :description, :image_url, :is_active, :display_order)
        RETURNING id
    ");
    $stmt->execute([
        ':slug'          => $d['slug'],
        ':category'      => $d['category'],
        ':name'          => $d['name'],
        ':price'         => (int) ($d['price'] ?? 0),
        ':price_unit'    => $d['price_unit']    ?? 'fixed',
        ':base_capacity' => $d['base_capacity'] ?? null,
        ':description'   => $d['description']   ?? null,
        ':image_url'     => $d['image_url']     ?? null,
        ':is_active'     => ($d['is_active'] ?? true) ? 'true' : 'false',
        ':display_order' => (int) ($d['display_order'] ?? 0),
    ]);
    return (int) $stmt->fetchColumn();
}

function updateServiceModel(int $id, array $d): int {
    global $pdo;
    $stmt = $pdo->prepare("
        UPDATE services SET
          slug=:slug, category=:category, name=:name, price=:price, price_unit=:price_unit,
          base_capacity=:base_capacity, description=:description, image_url=:image_url,
          is_active=:is_active, display_order=:display_order
        WHERE id=:id
    ");
    $stmt->execute([
        ':slug'          => $d['slug'],
        ':category'      => $d['category'],
        ':name'          => $d['name'],
        ':price'         => (int) ($d['price'] ?? 0),
        ':price_unit'    => $d['price_unit']    ?? 'fixed',
        ':base_capacity' => $d['base_capacity'] ?? null,
        ':description'   => $d['description']   ?? null,
        ':image_url'     => $d['image_url']     ?? null,
        ':is_active'     => ($d['is_active'] ?? true) ? 'true' : 'false',
        ':display_order' => (int) ($d['display_order'] ?? 0),
        ':id'            => $id,
    ]);
    return $stmt->rowCount();
}

function deleteServiceModel(int $id): int {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}

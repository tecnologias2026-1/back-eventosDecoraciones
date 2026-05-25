<?php
require_once __DIR__ . '/../database/connection.php';
global $pdo;

function generateReservationCode(): string {
    return 'WP-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}

function createReservationModel(array $d): array {
    global $pdo;
    $code = generateReservationCode();

    if (empty($d['venue_id']) && !empty($d['venue_slug'])) {
        $s = $pdo->prepare("SELECT id FROM venues WHERE slug = :slug AND is_active = TRUE");
        $s->execute([':slug' => $d['venue_slug']]);
        $row = $s->fetch();
        if (!$row) {
            throw new \RuntimeException("Venue not found: " . $d['venue_slug']);
        }
        $d['venue_id'] = $row['id'];
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("
            INSERT INTO reservations
              (code, user_id, groom_name, bride_name, email, phone, venue_id,
               wedding_date, guest_count, special_requirements, deposit_amount, total_price, status)
            VALUES
              (:code, :user_id, :groom_name, :bride_name, :email, :phone, :venue_id,
               :wedding_date, :guest_count, :special_requirements, :deposit_amount, :total_price, 'pending')
            RETURNING id, code
        ");
        $stmt->execute([
            ':code'                 => $code,
            ':user_id'              => $d['user_id']              ?? null,
            ':groom_name'           => $d['groom_name']           ?? null,
            ':bride_name'           => $d['bride_name']           ?? null,
            ':email'                => $d['email'],
            ':phone'                => $d['phone']                ?? null,
            ':venue_id'             => (int) $d['venue_id'],
            ':wedding_date'         => $d['wedding_date'],
            ':guest_count'          => (int) $d['guest_count'],
            ':special_requirements' => $d['special_requirements'] ?? null,
            ':deposit_amount'       => $d['deposit_amount']       ?? null,
            ':total_price'          => $d['total_price']          ?? null,
        ]);
        $reservation = $stmt->fetch();

        if (!empty($d['items']) && is_array($d['items'])) {
            $itemStmt = $pdo->prepare("
                INSERT INTO reservation_items
                  (reservation_id, service_id, item_type, item_name, quantity, unit_price, total_price)
                VALUES
                  (:reservation_id, :service_id, :item_type, :item_name, :quantity, :unit_price, :total_price)
            ");
            foreach ($d['items'] as $item) {
                $itemStmt->execute([
                    ':reservation_id' => $reservation['id'],
                    ':service_id'     => $item['service_id']  ?? null,
                    ':item_type'      => $item['item_type'],
                    ':item_name'      => $item['item_name'],
                    ':quantity'       => (int) ($item['quantity']   ?? 1),
                    ':unit_price'     => (int) ($item['unit_price'] ?? 0),
                    ':total_price'    => (int) ($item['total_price'] ?? 0),
                ]);
            }
        }

        $pdo->commit();
        return $reservation;
    } catch (\Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function getReservationByCodeModel(string $code): ?array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT r.*, v.name AS venue_name, v.slug AS venue_slug,
               COALESCE(json_agg(ri ORDER BY ri.id) FILTER (WHERE ri.id IS NOT NULL), '[]') AS items
        FROM reservations r
        JOIN venues v ON v.id = r.venue_id
        LEFT JOIN reservation_items ri ON ri.reservation_id = r.id
        WHERE r.code = :code
        GROUP BY r.id, v.name, v.slug
    ");
    $stmt->execute([':code' => $code]);
    $row = $stmt->fetch();
    if (!$row) return null;
    $row['items'] = json_decode($row['items'], true);
    return $row;
}

function getReservationsByEmailModel(string $email): array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT r.*, v.name AS venue_name, v.slug AS venue_slug
        FROM reservations r
        JOIN venues v ON v.id = r.venue_id
        WHERE r.email = :email
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([':email' => $email]);
    return $stmt->fetchAll();
}

function getAllReservationsModel(): array {
    global $pdo;
    $stmt = $pdo->query("
        SELECT r.*, v.name AS venue_name, v.slug AS venue_slug
        FROM reservations r
        JOIN venues v ON v.id = r.venue_id
        ORDER BY r.created_at DESC
    ");
    return $stmt->fetchAll();
}

function updateReservationStatusModel(string $code, string $status): int {
    global $pdo;
    $stmt = $pdo->prepare(
        "UPDATE reservations SET status=:status, updated_at=NOW() WHERE code=:code"
    );
    $stmt->execute([':status' => $status, ':code' => $code]);
    return $stmt->rowCount();
}

function deleteReservationModel(string $code): int {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE code = :code");
    $stmt->execute([':code' => $code]);
    return $stmt->rowCount();
}

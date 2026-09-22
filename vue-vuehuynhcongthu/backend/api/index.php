<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

function respond(mixed $data, int $status = 200): never {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
function body(): array {
    $data = json_decode(file_get_contents('php://input'), true);
    return is_array($data) ? $data : [];
}
function required(array $data, string ...$keys): void {
    foreach ($keys as $key) if (!isset($data[$key]) || trim((string)$data[$key]) === '') respond(['message' => "Thiếu trường: $key"], 422);
}
function publicUser(array $user): array {
    return ['id' => (int)$user['id'], 'fullname' => $user['fullname'], 'email' => $user['email'], 'role' => $user['role']];
}

$method = $_SERVER['REQUEST_METHOD'];
$route = trim($_GET['route'] ?? '', '/');
$pdo = db();

try {
    if ($method === 'POST' && $route === 'auth/register') {
        $data = body(); required($data, 'fullname', 'email', 'password');
        $email = strtolower(trim($data['email']));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) respond(['message' => 'Email không hợp lệ.'], 422);
        if (strlen($data['password']) < 6) respond(['message' => 'Mật khẩu phải có ít nhất 6 ký tự.'], 422);
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ?'); $check->execute([$email]);
        if ($check->fetch()) respond(['message' => 'Email này đã được đăng ký.'], 409);
        // Không nhận role admin từ giao diện công khai.
        $stmt = $pdo->prepare('INSERT INTO users (fullname, email, password_hash, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([trim($data['fullname']), $email, password_hash($data['password'], PASSWORD_DEFAULT), 'customer']);
        $id = (int)$pdo->lastInsertId();
        respond(['message' => 'Đăng ký thành công.', 'user' => ['id' => $id, 'fullname' => trim($data['fullname']), 'email' => $email, 'role' => 'customer']], 201);
    }

    if ($method === 'POST' && $route === 'auth/login') {
        $data = body(); required($data, 'email', 'password');
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?'); $stmt->execute([strtolower(trim($data['email']))]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($data['password'], $user['password_hash'])) respond(['message' => 'Email hoặc mật khẩu không đúng.'], 401);
        respond(['message' => 'Đăng nhập thành công.', 'user' => publicUser($user)]);
    }

    if ($method === 'GET' && $route === 'plants') {
        $stmt = $pdo->query('SELECT p.id, p.name, p.latin_name, p.short_note, p.description, p.planting_guide, p.care_guide, p.image_url, p.image_source_url, p.price, c.name AS category FROM plants p JOIN plant_categories c ON c.id=p.category_id WHERE p.is_active=1 ORDER BY p.id');
        respond(['plants' => $stmt->fetchAll()]);
    }

    if ($method === 'GET' && $route === 'lots') {
        $stmt = $pdo->query("SELECT l.id, l.lot_code, p.name AS plant_name, a.name AS area, l.sowed_date, l.initial_quantity, l.remaining_quantity, l.status, DATEDIFF(CURDATE(), l.sowed_date) AS age_days FROM nursery_lots l JOIN plants p ON p.id=l.plant_id JOIN nursery_areas a ON a.id=l.area_id ORDER BY l.sowed_date");
        respond(['lots' => $stmt->fetchAll()]);
    }

    if ($method === 'PATCH' && preg_match('#^lots/(\\d+)/status$#', $route, $matches)) {
        $data = body(); required($data, 'status');
        $allowed = ['growing', 'ready', 'needs_check', 'sold_out'];
        if (!in_array($data['status'], $allowed, true)) respond(['message' => 'Trạng thái không hợp lệ.'], 422);
        $stmt = $pdo->prepare('UPDATE nursery_lots SET status = ? WHERE id = ?'); $stmt->execute([$data['status'], $matches[1]]);
        if (!$stmt->rowCount()) respond(['message' => 'Không tìm thấy lô cây.'], 404);
        respond(['message' => 'Đã cập nhật trạng thái lô cây.']);
    }

    if ($method === 'POST' && $route === 'orders') {
        $data = body(); required($data, 'customer_name', 'items');
        if (!is_array($data['items']) || !$data['items']) respond(['message' => 'Đơn hàng chưa có sản phẩm.'], 422);
        $pdo->beginTransaction();
        $code = 'VX' . date('ymdHis') . random_int(10, 99);
        $order = $pdo->prepare('INSERT INTO orders (order_code, user_id, customer_name, customer_email, customer_phone, delivery_address, subtotal, payment_status) VALUES (?, ?, ?, ?, ?, ?, 0, ?)');
        $order->execute([$code, $data['user_id'] ?? null, trim($data['customer_name']), $data['customer_email'] ?? null, $data['customer_phone'] ?? null, $data['delivery_address'] ?? null, 'proof_submitted']);
        $orderId = (int)$pdo->lastInsertId(); $total = 0;
        $plantQuery = $pdo->prepare('SELECT id, price FROM plants WHERE id = ? AND is_active = 1');
        $itemQuery = $pdo->prepare('INSERT INTO order_items (order_id, plant_id, quantity, unit_price, line_total) VALUES (?, ?, ?, ?, ?)');
        foreach ($data['items'] as $item) {
            $quantity = (int)($item['quantity'] ?? 0); $plantId = (int)($item['plant_id'] ?? 0);
            if ($quantity < 1 || $plantId < 1) throw new RuntimeException('Sản phẩm hoặc số lượng không hợp lệ.');
            $plantQuery->execute([$plantId]); $plant = $plantQuery->fetch();
            if (!$plant) throw new RuntimeException('Sản phẩm không còn kinh doanh.');
            $lineTotal = $quantity * (float)$plant['price']; $total += $lineTotal;
            $itemQuery->execute([$orderId, $plantId, $quantity, $plant['price'], $lineTotal]);
        }
        $pdo->prepare('UPDATE orders SET subtotal = ? WHERE id = ?')->execute([$total, $orderId]);
        $pdo->commit(); respond(['message' => 'Đã tạo đơn hàng, chờ quản trị viên duyệt.', 'order_code' => $code], 201);
    }

    respond(['message' => 'Không tìm thấy API.'], 404);
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log($exception->getMessage());
    respond(['message' => 'Có lỗi máy chủ. Kiểm tra cấu hình MySQL và nhật ký PHP.'], 500);
}
